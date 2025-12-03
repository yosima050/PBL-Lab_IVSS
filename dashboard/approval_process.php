<?php
session_start();
require_once __DIR__ . '/db.php';

// Pastikan hanya role yang boleh mengakses
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'ketua_lab' && $_SESSION['role'] !== 'admin_sistem') {
    echo "Akses Ditolak!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id']);
    $action = $_POST['action'];

    try {

        if ($action === 'approve') {

            // Stored Procedure TERIMA PENDAFTAR
            $stmt = $pdo->prepare("CALL sp_terima_pendaftar(:id)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['message'] = "Pendaftaran berhasil disetujui! Akun user telah dibuat.";
            $_SESSION['msg_type'] = "success";

        } elseif ($action === 'reject') {

            // Stored Procedure TOLAK PENDAFTAR
            $stmt = $pdo->prepare("CALL sp_tolak_pendaftar(:id)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['message'] = "Pendaftaran telah ditolak.";
            $_SESSION['msg_type'] = "danger";
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = "Terjadi kesalahan: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }

    // Kembali ke halaman approval
    header("Location: approval.php");
    exit;
}