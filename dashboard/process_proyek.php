<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin & ketua lab
if (!in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    echo "Akses Ditolak!";
    exit;
}

// ==============================================
// 1. INSERT PROYEK DOSEN
// ==============================================
if (isset($_POST['create_dosen'])) {
    try {
        $pdo->beginTransaction();

        // Insert Proyek Utama
        $stmt = $pdo->prepare("INSERT INTO proyek (judul_proyek, deskripsi_proyek, tahun_proyek, tipe_proyek, id_dosen) VALUES (:judul, :deskripsi, :tahun, :tipe, :id_dosen) RETURNING id_proyek");
        $stmt->execute([
            ':judul' => $_POST['judul'],
            ':deskripsi' => $_POST['deskripsi'],
            ':tahun' => $_POST['tahun'],
            ':tipe' => $_POST['tipe'],
            ':id_dosen' => $_POST['id_dosen']
        ]);
        $newId = $stmt->fetchColumn();

        // Insert Detail
        $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_dosen (id_dosen, id_proyek, tanggal_mulai_proyek_dosen, tanggal_selesai_proyek_dosen, nama_penulis_proyek_dosen, kategori_proyek_dosen, lokasi_proyek_dosen) VALUES (:id_dosen, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        $stmt2->execute([
            ':id_dosen' => $_POST['id_dosen'],
            ':id_proyek' => $newId,
            ':mulai' => $_POST['tgl_mulai'],
            ':selesai' => $_POST['tgl_selesai'],
            ':penulis' => $_POST['nama_penulis'],
            ':kategori' => $_POST['kategori'],
            ':lokasi' => $_POST['lokasi']
        ]);

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Dosen berhasil ditambahkan!";
        header("Location: proyek.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Insert Dosen: " . $e->getMessage());
    }
}

// ==============================================
// 2. UPDATE PROYEK DOSEN
// ==============================================
if (isset($_POST['update_dosen'])) {
    try {
        $pdo->beginTransaction();

        // Update Tabel Proyek
        $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek=:judul, deskripsi_proyek=:deskripsi, tahun_proyek=:tahun, tipe_proyek=:tipe, id_dosen=:id_dosen WHERE id_proyek=:id");
        $stmt->execute([
            ':judul' => $_POST['edit_judul'],
            ':deskripsi' => $_POST['edit_deskripsi'],
            ':tahun' => $_POST['edit_tahun'],
            ':tipe' => $_POST['edit_tipe'],
            ':id_dosen' => $_POST['edit_id_dosen'],
            ':id' => $_POST['edit_id_proyek']
        ]);

        // Update Tabel Detail
        $stmt2 = $pdo->prepare("UPDATE detail_proyek_dosen SET id_dosen=:id_dosen, tanggal_mulai_proyek_dosen=:mulai, tanggal_selesai_proyek_dosen=:selesai, nama_penulis_proyek_dosen=:penulis, kategori_proyek_dosen=:kategori, lokasi_proyek_dosen=:lokasi WHERE id_proyek=:id");
        $stmt2->execute([
            ':id_dosen' => $_POST['edit_id_dosen'],
            ':mulai' => $_POST['edit_tgl_mulai'],
            ':selesai' => $_POST['edit_tgl_selesai'],
            ':penulis' => $_POST['edit_nama_penulis'],
            ':kategori' => $_POST['edit_kategori'],
            ':lokasi' => $_POST['edit_lokasi'],
            ':id' => $_POST['edit_id_proyek']
        ]);

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Dosen berhasil diperbarui!";
        header("Location: proyek.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Update Dosen: " . $e->getMessage());
    }
}

// ==============================================
// 3. INSERT PROYEK MAHASISWA
// ==============================================
if (isset($_POST['create_mahasiswa'])) {
    try {
        $pdo->beginTransaction();

        // Insert Proyek Utama
        $stmt = $pdo->prepare("INSERT INTO proyek (judul_proyek, deskripsi_proyek, tahun_proyek, tipe_proyek, id_mahasiswa) VALUES (:judul, :deskripsi, :tahun, :tipe, :id_mhs) RETURNING id_proyek");
        $stmt->execute([
            ':judul' => $_POST['judul'],
            ':deskripsi' => $_POST['deskripsi'],
            ':tahun' => $_POST['tahun'],
            ':tipe' => $_POST['tipe'],
            ':id_mhs' => $_POST['id_mahasiswa']
        ]);
        $newId = $stmt->fetchColumn();

        // Insert Detail Mahasiswa
        $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        $stmt2->execute([
            ':id_mhs' => $_POST['id_mahasiswa'],
            ':id_proyek' => $newId,
            ':mulai' => $_POST['tgl_mulai'],
            ':selesai' => $_POST['tgl_selesai'],
            ':penulis' => $_POST['nama_penulis'],
            ':kategori' => $_POST['kategori'],
            ':lokasi' => $_POST['lokasi']
        ]);

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Mahasiswa berhasil ditambahkan!";
        header("Location: proyek.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Insert Mahasiswa: " . $e->getMessage());
    }
}

// ==============================================
// 4. UPDATE PROYEK MAHASISWA
// ==============================================
if (isset($_POST['update_mahasiswa'])) {
    try {
        $pdo->beginTransaction();

        // Update Proyek Utama
        $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek=:judul, deskripsi_proyek=:deskripsi, tahun_proyek=:tahun, tipe_proyek=:tipe, id_mahasiswa=:id_mhs WHERE id_proyek=:id");
        $stmt->execute([
            ':judul' => $_POST['edit_judul_mhs'],
            ':deskripsi' => $_POST['edit_deskripsi_mhs'],
            ':tahun' => $_POST['edit_tahun_mhs'],
            ':tipe' => $_POST['edit_tipe_mhs'],
            ':id_mhs' => $_POST['edit_id_mahasiswa'],
            ':id' => $_POST['edit_id_proyek_mhs']
        ]);

        // Update Detail Mahasiswa
        $stmt2 = $pdo->prepare("UPDATE detail_proyek_mahasiswa SET id_mahasiswa=:id_mhs, tanggal_mulai_proyek_mahasiswa=:mulai, tanggal_selesai_proyek_mahasiswa=:selesai, nama_penulis_proyek_mahasiswa=:penulis, kategori_proyek_mahasiswa=:kategori, lokasi_proyek_mahasiswa=:lokasi WHERE id_proyek=:id");
        $stmt2->execute([
            ':id_mhs' => $_POST['edit_id_mahasiswa'],
            ':mulai' => $_POST['edit_tgl_mulai'],
            ':selesai' => $_POST['edit_tgl_selesai'],
            ':penulis' => $_POST['edit_nama_penulis_mhs'],
            ':kategori' => $_POST['edit_kategori_mhs'],
            ':lokasi' => $_POST['edit_lokasi_mhs'],
            ':id' => $_POST['edit_id_proyek_mhs']
        ]);

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Mahasiswa berhasil diperbarui!";
        header("Location: proyek.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Update Mahasiswa: " . $e->getMessage());
    }
}

// ==============================================
// 5. DELETE PROYEK (Universal)
// ==============================================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $pdo->beginTransaction();
        // Hapus detail dulu (karena FK)
        $pdo->prepare("DELETE FROM detail_proyek_dosen WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")->execute([':id' => $id]);
        // Hapus proyek utama
        $pdo->prepare("DELETE FROM proyek WHERE id_proyek = :id")->execute([':id' => $id]);
        
        $pdo->commit();
        $_SESSION['flash'] = "Proyek berhasil dihapus.";
        header("Location: proyek.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Delete: " . $e->getMessage());
    }
}

// Fallback redirect
header("Location: proyek.php");
exit;
?>