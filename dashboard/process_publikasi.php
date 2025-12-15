<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// === CREATE (Tambah Publikasi) ===
if (isset($_POST['create'])) {

    $id_users = $_POST['id_users'];
    $judul = $_POST['judul_publikasi'];
    $tahun = $_POST['tahun_publikasi'];
    $link = $_POST['link_publikasi'];

    try {
        // Gunakan function PostgreSQL
        $sql = "SELECT public.fn_insert_publikasi(:id_users, :judul, :tahun, :link)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_users' => (int)$id_users,
            ':judul'    => $judul,
            ':tahun'    => (int)$tahun,
            ':link'     => $link
        ]);

        header("Location: publikasi.php?status=created");
        exit;

    } catch (PDOException $e) {
        die("Insert Error: " . $e->getMessage());
    }
}


// === UPDATE (Edit Publikasi) ===
if (isset($_POST['update'])) {

    $id_publikasi = $_POST['id_publikasi'];
    $judul = $_POST['judul_publikasi'];
    $tahun = $_POST['tahun_publikasi'];
    $link = $_POST['link_publikasi'];

    try {
        $sql = "UPDATE publikasi 
                SET judul_publikasi = :judul,
                    tahun_publikasi = :tahun,
                    link_publikasi = :link
                WHERE id_publikasi = :id_publikasi";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':judul'         => $judul,
            ':tahun'         => $tahun,
            ':link'          => $link,
            ':id_publikasi'  => $id_publikasi
        ]);

        header("Location: publikasi.php?status=updated");
        exit;

    } catch (PDOException $e) {
        die("Update Error: " . $e->getMessage());
    }
}


// === DELETE (Hapus Publikasi) ===
if (isset($_GET['delete'])) {

    $id_publikasi = $_GET['delete'];

    try {
        $sql = "DELETE FROM publikasi WHERE id_publikasi = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_publikasi]);

        header("Location: publikasi.php?status=deleted");
        exit;

    } catch (PDOException $e) {
        die("Delete Error: " . $e->getMessage());
    }
}

?>