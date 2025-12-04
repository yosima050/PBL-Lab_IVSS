<?php
session_start();
require_once __DIR__ . '/db.php';

// 1. CEK LOGIN & ROLE
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_sistem') {
    $_SESSION['message'] = "Akses Ditolak!";
    $_SESSION['msg_type'] = "danger";
    header("Location: pendaftaran.php");
    exit;
}

$id_user_login = $_SESSION['user_id'];

// 2. PROSES UPDATE (Langsung proses karena konfirmasi sudah dilakukan di Modal)
if (isset($_GET['id']) && isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $id_pendaftaran = $_GET['id'];

    try {
        // Cek status saat ini
        $stmtCheck = $pdo->prepare("SELECT status_mahasiswa FROM pendaftaran WHERE id_pendaftaran = :id");
        $stmtCheck->execute(['id' => $id_pendaftaran]);
        $status = $stmtCheck->fetchColumn();

        // Validasi: Hanya yang 'Pending' yang boleh diteruskan
        if ($status !== 'Pending') {
            throw new Exception("Pendaftaran ini sudah diproses atau bukan status Pending.");
        }

        // Lakukan Update
        $sql = "UPDATE pendaftaran 
                SET status_mahasiswa = 'Menunggu', 
                    diteruskan_oleh = :id_admin 
                WHERE id_pendaftaran = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_admin' => $id_user_login,
            'id' => $id_pendaftaran
        ]);

        $_SESSION['message'] = "Sukses! Data pendaftaran #$id_pendaftaran berhasil diteruskan ke Ketua Lab.";
        $_SESSION['msg_type'] = "success";

    } catch (Exception $e) {
        $_SESSION['message'] = "Gagal: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
}

// 3. KEMBALIKAN KE HALAMAN ADMIN
header("Location: pendaftaran.php");
exit;
?>