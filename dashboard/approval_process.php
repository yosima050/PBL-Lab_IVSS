<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === 'approve') {

        // Panggil Stored Procedure PostgreSQL
        $stmt = $pdo->prepare("CALL sp_terima_pendaftaran(:id)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['message'] = "Pendaftaran berhasil disetujui!";
        $_SESSION['msg_type'] = "success";

    } elseif ($action === 'reject') {

        // Jika ingin stored procedure tolak, buat juga SP-nya
        $stmt = $pdo->prepare("UPDATE pendaftaran SET status_mahasiswa = 'Ditolak' WHERE id_pendaftaran = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['message'] = "Pendaftaran ditolak.";
        $_SESSION['msg_type'] = "danger";
    }

    header("Location: approval.php");
    exit;
}