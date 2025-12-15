<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek Role (Admin Sistem / Ketua Lab)
if (!in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    echo "Akses Ditolak!";
    exit;
}

/* ============================================================
   DELETE DATA (DENGAN PEMBERSIHAN DATA TERKAIT)
============================================================ */
/* ============================================================
   DELETE DATA (FIX CIRCULAR DEPENDENCY)
============================================================ */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    try {
        $pdo->beginTransaction();

        // 1. [PENTING] Putuskan hubungan di tabel USERS dulu!
        // Set id_mahasiswa dan id_dosen jadi NULL agar tidak mengunci tabel anak
        $stmtUnlink = $pdo->prepare("UPDATE users SET id_mahasiswa = NULL, id_dosen = NULL WHERE id_users = ?");
        $stmtUnlink->execute([$id]);

        // 2. Hapus data di tabel anak (Sekarang aman dihapus karena users sudah tidak memegangnya)
        $pdo->prepare("DELETE FROM mahasiswa WHERE id_users = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM dosen WHERE id_users = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM pendaftaran WHERE id_users = ?")->execute([$id]);
        
        // (Opsional: Hapus data terkait lain seperti publikasi/berita jika ada)
        // $pdo->prepare("DELETE FROM publikasi WHERE id_users = ?")->execute([$id]);

        // 3. Terakhir, Hapus user utama
        $pdo->prepare("DELETE FROM users WHERE id_users = ?")->execute([$id]);

        $pdo->commit();
        header("Location: manajemen_admin.php?msg=deleted");
    } catch (PDOException $e) {
        $pdo->rollBack();
        // Tampilkan pesan error yang jelas
        echo "<h3>Gagal Menghapus Data</h3>";
        echo "<p>Error Database: " . $e->getMessage() . "</p>";
        echo "<a href='manajemen_admin.php'>Kembali</a>";
    }
    exit;
}

/* ============================================================
   TAMBAH DATA (INSERT)
============================================================ */
if (isset($_POST['create'])) {
    $id_role   = $_POST['id_role'];
    $nama      = $_POST['nama_users'];
    $email     = $_POST['email_users'];
    $password  = $_POST['password'];

    try {
        $pdo->beginTransaction();

        // 1. Insert User
        $stmt = $pdo->prepare("INSERT INTO users (id_role, nama_users, email_users, password) VALUES (?, ?, ?, ?) RETURNING id_users");
        $stmt->execute([$id_role, $nama, $email, $password]);
        $newUserId = $stmt->fetchColumn();

        // 2. Buat data detail sesuai Role
        if ($id_role == 4) { // Dosen
            $stmtD = $pdo->prepare("INSERT INTO dosen (id_users, nama_dosen) VALUES (?, ?) RETURNING id_dosen");
            $stmtD->execute([$newUserId, $nama]);
            $newIdDosen = $stmtD->fetchColumn();
            
            // Update User link ke Dosen
            $pdo->prepare("UPDATE users SET id_dosen = ? WHERE id_users = ?")->execute([$newIdDosen, $newUserId]);

        } elseif ($id_role == 5) { // Mahasiswa
            $stmtM = $pdo->prepare("INSERT INTO mahasiswa (id_users, status_mahasiswa) VALUES (?, 'Aktif') RETURNING id_mahasiswa");
            $stmtM->execute([$newUserId]);
            $newIdMhs = $stmtM->fetchColumn();

            // Update User link ke Mahasiswa
            $pdo->prepare("UPDATE users SET id_mahasiswa = ? WHERE id_users = ?")->execute([$newIdMhs, $newUserId]);
        }

        $pdo->commit();
        header("Location: manajemen_admin.php?msg=added");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error Insert: " . $e->getMessage());
    }
    exit;
}

/* ============================================================
   UPDATE DATA (DENGAN MIGRASI TABEL DOSEN <-> MAHASISWA)
============================================================ */
if (isset($_POST['update'])) {
    $id        = $_POST['id_users'];
    $id_role   = $_POST['id_role']; // Role Baru
    $nama      = $_POST['nama_users'];
    $email     = $_POST['email_users'];

    try {
        $pdo->beginTransaction();

        // 1. Cek Role Lama & Data Lama
        $stmtOld = $pdo->prepare("SELECT id_role, id_dosen, id_mahasiswa FROM users WHERE id_users = ?");
        $stmtOld->execute([$id]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);
        $roleLama = $oldData['id_role'];

        // 2. Update Data User Dasar
        $sql = "UPDATE users SET id_role = ?, nama_users = ?, email_users = ?";
        $params = [$id_role, $nama, $email];

        // Jika password diisi, update password juga
        if (!empty($_POST['password'])) {
            $sql .= ", password = ?";
            $params[] = $_POST['password'];
        }
        $sql .= " WHERE id_users = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);


        // 3. LOGIKA MIGRASI (PENTING!)
        // Jika Role Berubah, kita harus pindahkan datanya
        if ($roleLama != $id_role) {
            
            // KASUS A: Berubah JADI DOSEN (Role 4)
            if ($id_role == 4) {
                // Buat data di tabel Dosen
                $stmtIn = $pdo->prepare("INSERT INTO dosen (id_users, nama_dosen) VALUES (?, ?) RETURNING id_dosen");
                $stmtIn->execute([$id, $nama]);
                $newIdDosen = $stmtIn->fetchColumn();

                // Update Users (Link ke Dosen, Hapus Link Mahasiswa)
                $pdo->prepare("UPDATE users SET id_dosen = ?, id_mahasiswa = NULL WHERE id_users = ?")->execute([$newIdDosen, $id]);

                // Hapus data lama di Mahasiswa (jika ada)
                $pdo->prepare("DELETE FROM mahasiswa WHERE id_users = ?")->execute([$id]);
            }
            
            // KASUS B: Berubah JADI MAHASISWA (Role 5)
            elseif ($id_role == 5) {
                // Buat data di tabel Mahasiswa
                $stmtIn = $pdo->prepare("INSERT INTO mahasiswa (id_users, status_mahasiswa) VALUES (?, 'Aktif') RETURNING id_mahasiswa");
                $stmtIn->execute([$id]);
                $newIdMhs = $stmtIn->fetchColumn();

                // Update Users (Link ke Mahasiswa, Hapus Link Dosen)
                $pdo->prepare("UPDATE users SET id_mahasiswa = ?, id_dosen = NULL WHERE id_users = ?")->execute([$newIdMhs, $id]);

                // Hapus data lama di Dosen (jika ada)
                $pdo->prepare("DELETE FROM dosen WHERE id_users = ?")->execute([$id]);
            }
            
            // KASUS C: Berubah JADI ADMIN (Role 1, 2, 3)
            // Admin biasanya tidak butuh data di tabel dosen/mahasiswa, jadi kita bersihkan
            else {
                 $pdo->prepare("UPDATE users SET id_dosen = NULL, id_mahasiswa = NULL WHERE id_users = ?")->execute([$id]);
                 $pdo->prepare("DELETE FROM dosen WHERE id_users = ?")->execute([$id]);
                 $pdo->prepare("DELETE FROM mahasiswa WHERE id_users = ?")->execute([$id]);
            }
        }
        // Jika Role TIDAK Berubah, tapi NAMA berubah, update nama di tabel terkait
        else {
             if ($id_role == 4) {
                 $pdo->prepare("UPDATE dosen SET nama_dosen = ? WHERE id_users = ?")->execute([$nama, $id]);
             }
             // Mahasiswa tidak ada kolom nama di tabel mahasiswa (nama ada di pendaftaran/users), jadi aman.
        }

        // Refresh View (Penting agar tampilan tabel admin update)
        $pdo->query("REFRESH MATERIALIZED VIEW mv_dosen"); // Jika ada view lain, refresh juga
        
        $pdo->commit();
        header("Location: manajemen_admin.php?msg=updated");

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error Update & Migrasi: " . $e->getMessage());
    }
    exit;
}
?>