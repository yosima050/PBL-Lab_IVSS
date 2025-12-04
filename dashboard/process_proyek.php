<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login & Role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    header("Location: login.php");
    exit;
}

// ==============================================
// 1. INSERT PROYEK DOSEN (UPDATE: Tambah Lokasi)
// ==============================================
if (isset($_POST['create_dosen'])) {
    $id_dosen   = $_POST['id_dosen'];
    $judul      = $_POST['judul'];
    $deskripsi  = $_POST['deskripsi'];
    $tahun      = $_POST['tahun'];
    $tipe       = $_POST['tipe'];
    $tgl_mulai  = $_POST['tgl_mulai'];
    $tgl_selesai= $_POST['tgl_selesai'];
    $penulis    = $_POST['nama_penulis'];
    $kategori   = $_POST['kategori'];
    $lokasi     = $_POST['lokasi']; // <--- BARU

    try {
        $pdo->beginTransaction();

        // Insert Proyek Utama
        $stmt = $pdo->prepare("INSERT INTO proyek (judul_proyek, deskripsi_proyek, tahun_proyek, tipe_proyek, id_dosen) VALUES (:judul, :deskripsi, :tahun, :tipe, :id_dosen) RETURNING id_proyek");
        $stmt->execute([':judul'=>$judul, ':deskripsi'=>$deskripsi, ':tahun'=>$tahun, ':tipe'=>$tipe, ':id_dosen'=>$id_dosen]);
        $newId = $stmt->fetchColumn();

        // Insert Detail Dosen (+ Lokasi)
        $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_dosen (id_dosen, id_proyek, tanggal_mulai_proyek_dosen, tanggal_selesai_proyek_dosen, nama_penulis_proyek_dosen, kategori_proyek_dosen, lokasi_proyek_dosen) VALUES (:id_dosen, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        $stmt2->execute([':id_dosen'=>$id_dosen, ':id_proyek'=>$newId, ':mulai'=>$tgl_mulai, ':selesai'=>$tgl_selesai, ':penulis'=>$penulis, ':kategori'=>$kategori, ':lokasi'=>$lokasi]);

        $pdo->commit();
        header("Location: proyek.php?success=dosen_added");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Insert Dosen: " . $e->getMessage());
    }
}

// ==============================================
// 2. UPDATE PROYEK DOSEN (UPDATE: Tambah Lokasi)
// ==============================================
if (isset($_POST['update_dosen'])) {
    $id_proyek  = $_POST['edit_id_proyek'];
    $id_dosen   = $_POST['edit_id_dosen'];
    $judul      = $_POST['edit_judul'];
    $deskripsi  = $_POST['edit_deskripsi'];
    $tahun      = $_POST['edit_tahun'];
    $tipe       = $_POST['edit_tipe'];
    $tgl_mulai  = $_POST['edit_tgl_mulai'];
    $tgl_selesai= $_POST['edit_tgl_selesai'];
    $penulis    = $_POST['edit_nama_penulis'];
    $kategori   = $_POST['edit_kategori'];
    $lokasi     = $_POST['edit_lokasi']; // <--- BARU

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek=:judul, deskripsi_proyek=:deskripsi, tahun_proyek=:tahun, tipe_proyek=:tipe, id_dosen=:id_dosen WHERE id_proyek=:id");
        $stmt->execute([':judul'=>$judul, ':deskripsi'=>$deskripsi, ':tahun'=>$tahun, ':tipe'=>$tipe, ':id_dosen'=>$id_dosen, ':id']);

        $stmt2 = $pdo->prepare("UPDATE detail_proyek_dosen SET id_dosen=:id_dosen, tanggal_mulai_proyek_dosen=:mulai, tanggal_selesai_proyek_dosen=:selesai, nama_penulis_proyek_dosen=:penulis, kategori_proyek_dosen=:kategori, lokasi_proyek_dosen=:lokasi WHERE id_proyek=:id");
        $stmt2->execute([':id_dosen'=>$id_dosen, ':mulai'=>$tgl_mulai, ':selesai'=>$tgl_selesai, ':penulis'=>$penulis, ':kategori'=>$kategori, ':lokasi'=>$lokasi, ':id'=>$id_proyek]);

        $pdo->commit();
        header("Location: proyek.php?success=dosen_updated");
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
    // Pastikan function di database sudah benar, atau kita pakai query manual saja biar aman
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
        header("Location: proyek.php?success=mahasiswa_added");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Insert Mahasiswa: " . $e->getMessage());
    }
}

if (isset($_POST['update_mahasiswa'])) {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek=:judul, deskripsi_proyek=:deskripsi, tahun_proyek=:tahun, tipe_proyek=:tipe, id_mahasiswa=:id_mhs WHERE id_proyek=:id");
        $stmt->execute([
            ':judul' => $_POST['edit_judul_mhs'],
            ':deskripsi' => $_POST['edit_deskripsi_mhs'],
            ':tahun' => $_POST['edit_tahun_mhs'],
            ':tipe' => $_POST['edit_tipe_mhs'],
            ':id_mhs' => $_POST['edit_id_mahasiswa'],
            ':id' => $_POST['edit_id_proyek_mhs']
        ]);

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
        header("Location: proyek.php?success=mahasiswa_updated");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Update Mahasiswa: " . $e->getMessage());
    }
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM detail_proyek_dosen WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM proyek WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->commit();
        header("Location: proyek.php?success=deleted");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Delete: " . $e->getMessage());
    }
}
?>