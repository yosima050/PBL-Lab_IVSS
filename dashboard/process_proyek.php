<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login & Role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    header("Location: login.php"); exit;
}

if (isset($_POST['create_dosen'])) {
    try {
        $pdo->beginTransaction();

        $dosen_ids = $_POST['id_dosen'] ?? [];
        if (count($dosen_ids) === 0) throw new Exception("Wajib memilih minimal satu dosen.");
        
        $ketua_id = $dosen_ids[0];
        
        // 1. Insert Proyek (Kolom id_mahasiswa KITA KOSONGKAN/NULL karena asistennya banyak)
        $stmt = $pdo->prepare("INSERT INTO proyek (judul_proyek, deskripsi_proyek, tahun_proyek, tipe_proyek, id_dosen, id_mahasiswa) VALUES (:judul, :deskripsi, :tahun, :tipe, :id_dosen, NULL) RETURNING id_proyek");
        $stmt->execute([
            ':judul' => $_POST['judul'],
            ':deskripsi' => $_POST['deskripsi'],
            ':tahun' => $_POST['tahun'],
            ':tipe' => $_POST['tipe'],
            ':id_dosen' => $ketua_id
        ]);
        $newId = $stmt->fetchColumn();

        // 2. Insert Tim Dosen (Tabel Detail Dosen)
        $stmtDosen = $pdo->prepare("INSERT INTO detail_proyek_dosen (id_dosen, id_proyek, tanggal_mulai_proyek_dosen, tanggal_selesai_proyek_dosen, nama_penulis_proyek_dosen, kategori_proyek_dosen, lokasi_proyek_dosen) VALUES (:id_dosen, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        foreach ($dosen_ids as $dosen_id) {
            $stmtDosen->execute([
                ':id_dosen' => $dosen_id,
                ':id_proyek' => $newId,
                ':mulai' => $_POST['tgl_mulai'],
                ':selesai' => $_POST['tgl_selesai'],
                ':penulis' => $_POST['nama_penulis'],
                ':kategori' => $_POST['kategori'],
                ':lokasi' => $_POST['lokasi']
            ]);
        }

        // 3. INSERT ASISTEN MAHASISWA (Tabel Detail Mahasiswa)
        $asisten_ids = $_POST['mahasiswa_asisten'] ?? [];
        if (!empty($asisten_ids)) {
            $stmtAsisten = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, 'Asisten', 'Asisten', :lokasi)");
            foreach ($asisten_ids as $mhs_id) {
                $stmtAsisten->execute([
                    ':id_mhs' => $mhs_id,
                    ':id_proyek' => $newId,
                    ':mulai' => $_POST['tgl_mulai'],
                    ':selesai' => $_POST['tgl_selesai'],
                    ':lokasi' => $_POST['lokasi']
                ]);
            }
        }

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Dosen (+ Asisten) berhasil ditambahkan!";
        header("Location: proyek.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); die("Error: " . $e->getMessage());
    }
}

if (isset($_POST['update_dosen'])) {
    try {
        $pdo->beginTransaction();
        $id_proyek = $_POST['edit_id_proyek'];
        
        $dosen_ids = $_POST['edit_id_dosen'] ?? [];
        if (count($dosen_ids) === 0) throw new Exception("Tim tidak boleh kosong.");
        $ketua_id = $dosen_ids[0];

        // 1. Update Proyek Utama (id_mahasiswa tetap NULL atau tidak diubah)
        $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek=:judul, deskripsi_proyek=:deskripsi, tahun_proyek=:tahun, tipe_proyek=:tipe, id_dosen=:id_dosen WHERE id_proyek=:id");
        $stmt->execute([
            ':judul' => $_POST['edit_judul'],
            ':deskripsi' => $_POST['edit_deskripsi'],
            ':tahun' => $_POST['edit_tahun'],
            ':tipe' => $_POST['edit_tipe'],
            ':id_dosen' => $ketua_id,
            ':id' => $id_proyek
        ]);

        // 2. Reset & Re-Insert Tim Dosen
        $pdo->prepare("DELETE FROM detail_proyek_dosen WHERE id_proyek = :id")->execute([':id' => $id_proyek]);
        $stmtDosen = $pdo->prepare("INSERT INTO detail_proyek_dosen (id_dosen, id_proyek, tanggal_mulai_proyek_dosen, tanggal_selesai_proyek_dosen, nama_penulis_proyek_dosen, kategori_proyek_dosen, lokasi_proyek_dosen) VALUES (:id_dosen, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        foreach ($dosen_ids as $dosen_id) {
            $stmtDosen->execute([
                ':id_dosen' => $dosen_id,
                ':id_proyek' => $id_proyek,
                ':mulai' => $_POST['edit_tgl_mulai'],
                ':selesai' => $_POST['edit_tgl_selesai'],
                ':penulis' => $_POST['edit_nama_penulis'],
                ':kategori' => $_POST['edit_kategori'],
                ':lokasi' => $_POST['edit_lokasi']
            ]);
        }

        // 3. Reset & Re-Insert Asisten Mahasiswa
        // Hapus asisten lama (yang ada di detail_proyek_mahasiswa untuk proyek ini)
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")->execute([':id' => $id_proyek]);
        
        $asisten_ids = $_POST['edit_mahasiswa_asisten'] ?? [];
        if (!empty($asisten_ids)) {
            $stmtAsisten = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, 'Asisten', 'Asisten', :lokasi)");
            foreach ($asisten_ids as $mhs_id) {
                $stmtAsisten->execute([
                    ':id_mhs' => $mhs_id,
                    ':id_proyek' => $id_proyek,
                    ':mulai' => $_POST['edit_tgl_mulai'],
                    ':selesai' => $_POST['edit_tgl_selesai'],
                    ':lokasi' => $_POST['edit_lokasi']
                ]);
            }
        }

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Dosen berhasil diperbarui!";
        header("Location: proyek.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); die("Error: " . $e->getMessage());
    }
}

// ==============================================
// 3. INSERT PROYEK MAHASISWA (+ DOSEN PEMBIMBING)
// ==============================================
if (isset($_POST['create_mahasiswa'])) {
    try {
        $pdo->beginTransaction();

        $mhs_ids = $_POST['id_mahasiswa'] ?? [];
        if (count($mhs_ids) === 0) throw new Exception("Wajib memilih minimal satu mahasiswa.");

        $ketua_id = $mhs_ids[0];
        // Tangkap Pembimbing
        $id_pembimbing = !empty($_POST['dosen_pembimbing']) ? $_POST['dosen_pembimbing'] : null;

        // Insert Proyek (Simpan Pembimbing di kolom id_dosen)
        $stmt = $pdo->prepare("INSERT INTO proyek (judul_proyek, deskripsi_proyek, tahun_proyek, tipe_proyek, id_mahasiswa, id_dosen) VALUES (:judul, :deskripsi, :tahun, :tipe, :id_mhs, :id_pembimbing) RETURNING id_proyek");
        $stmt->execute([
            ':judul' => $_POST['judul'],
            ':deskripsi' => $_POST['deskripsi'],
            ':tahun' => $_POST['tahun'],
            ':tipe' => $_POST['tipe'],
            ':id_mhs' => $ketua_id,
            ':id_pembimbing' => $id_pembimbing // <-- Pembimbing Disimpan Disini
        ]);
        $newId = $stmt->fetchColumn();

        // Insert Detail Tim Mahasiswa
        $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        
        foreach ($mhs_ids as $single_id) {
            $stmt2->execute([
                ':id_mhs' => $single_id,
                ':id_proyek' => $newId,
                ':mulai' => $_POST['tgl_mulai'],
                ':selesai' => $_POST['tgl_selesai'],
                ':penulis' => $_POST['nama_penulis'],
                ':kategori' => $_POST['kategori'],
                ':lokasi' => $_POST['lokasi']
            ]);
        }

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Mahasiswa berhasil ditambahkan!";
        header("Location: proyek.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); die("Error Insert Mahasiswa: " . $e->getMessage());
    }
}

// ==============================================
// 4. UPDATE PROYEK MAHASISWA (+ DOSEN PEMBIMBING)
// ==============================================
if (isset($_POST['update_mahasiswa'])) {
    try {
        $pdo->beginTransaction();
        $id_proyek = $_POST['edit_id_proyek_mhs'];

        $mhs_ids = $_POST['edit_id_mahasiswa'] ?? [];
        if (count($mhs_ids) === 0) throw new Exception("Tim tidak boleh kosong.");

        $ketua_id = $mhs_ids[0];
        $id_pembimbing = !empty($_POST['edit_dosen_pembimbing']) ? $_POST['edit_dosen_pembimbing'] : null;

        // Update Proyek Utama
        $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek=:judul, deskripsi_proyek=:deskripsi, tahun_proyek=:tahun, tipe_proyek=:tipe, id_mahasiswa=:id_mhs, id_dosen=:id_pembimbing WHERE id_proyek=:id");
        $stmt->execute([
            ':judul' => $_POST['edit_judul_mhs'],
            ':deskripsi' => $_POST['edit_deskripsi_mhs'],
            ':tahun' => $_POST['edit_tahun_mhs'],
            ':tipe' => $_POST['edit_tipe_mhs'],
            ':id_mhs' => $ketua_id,
            ':id_pembimbing' => $id_pembimbing, // Update Pembimbing
            ':id' => $id_proyek
        ]);

        // Update Detail
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")->execute([':id' => $id_proyek]);
        $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        
        foreach ($mhs_ids as $single_id) {
            $stmt2->execute([
                ':id_mhs' => $single_id,
                ':id_proyek' => $id_proyek,
                ':mulai' => $_POST['edit_tgl_mulai'],
                ':selesai' => $_POST['edit_tgl_selesai'],
                ':penulis' => $_POST['edit_nama_penulis_mhs'],
                ':kategori' => $_POST['edit_kategori_mhs'],
                ':lokasi' => $_POST['edit_lokasi_mhs']
            ]);
        }

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Mahasiswa berhasil diperbarui!";
        header("Location: proyek.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); die("Error Update Mahasiswa: " . $e->getMessage());
    }
}

// ==============================================
// 5. DELETE PROYEK (Universal)
// ==============================================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM detail_proyek_dosen WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM proyek WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->commit();
        $_SESSION['flash'] = "Proyek berhasil dihapus.";
        header("Location: proyek.php"); exit;
    } catch (PDOException $e) {
        $pdo->rollBack(); die("Error Delete: " . $e->getMessage());
    }
}

header("Location: proyek.php"); exit;
?>