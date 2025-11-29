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
   DELETE DATA
============================================================ */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id_users = ?");
    $stmt->execute([$id]);

    header("Location: manajemen_admin.php?msg=deleted");
    exit;
}

/* ============================================================
   TAMBAH DATA (INSERT)
============================================================ */
if (isset($_POST['create'])) {

    // Ambil input
    $id_role   = $_POST['id_role'];
    $nama      = $_POST['nama_users'];
    $email     = $_POST['email_users'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Query INSERT
    $stmt = $pdo->prepare("
        INSERT INTO users (id_role, nama_users, email_users, password)
        VALUES (?, ?, ?, ?)
    ");

    $save = $stmt->execute([$id_role, $nama, $email, $password]);

    if ($save) {
        header("Location: manajemen_admin.php?msg=added");
    } else {
        echo "Gagal menambahkan user.";
    }
    exit;
}

/* ============================================================
   UPDATE DATA
============================================================ */
if (isset($_POST['update'])) {

    // Ambil input
    $id        = $_POST['id_users'];
    $id_role   = $_POST['id_role'];
    $nama      = $_POST['nama_users'];
    $email     = $_POST['email_users'];

    // Cek apakah password diubah atau tidak
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users 
            SET id_role = ?, nama_users = ?, email_users = ?, password = ?
            WHERE id_users = ?
        ");

        $stmt->execute([$id_role, $nama, $email, $password, $id]);

    } else {

        // update tanpa mengubah password
        $stmt = $pdo->prepare("
            UPDATE users 
            SET id_role = ?, nama_users = ?, email_users = ?
            WHERE id_users = ?
        ");

        $stmt->execute([$id_role, $nama, $email, $id]);
    }

    header("Location: manajemen_admin.php?msg=updated");
    exit;
}

?>
