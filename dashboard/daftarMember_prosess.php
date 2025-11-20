<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nama            = trim($_POST['name']);
    $nim             = trim($_POST['nim']);
    $prodi           = trim($_POST['prodi']);
    $dosenPembimbing = trim($_POST['dosenPembimbing']);
    $email           = trim($_POST['email']);
    $password        = trim($_POST['password']);

    // Validasi input kosong ...

    try {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO pendaftaran (nim, nama_mahasiswa, prodi, email_mahasiswa, status_mahasiswa, nama_dosen, password_sementara) 
                VALUES (:nim, :nama, :prodi, :email, 'Pending', :dosen, :pass)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nim'   => $nim,
            'nama'  => $nama,
            'prodi' => $prodi,
            'email' => $email,
            'dosen' => $dosenPembimbing,
            'pass'  => $hashed_password
        ]);

        // ... (Lanjut ke commit, session success, redirect) ...
        
        $pdo->commit();
        $_SESSION['success_register'] = true;
        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        // ... (Error handling) ...
    }
}