<?php
require_once __DIR__ . '/db.php';

// === FUNCTION UNTUK ADMIN SISTEM === //

function getPendingPendaftar($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Pending'");
    return $stmt->fetchColumn();
}

function getTotalMahasiswaAktif($pdo) {
    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM pendaftaran 
        WHERE status_mahasiswa = 'Aktif'
    ");
    return $stmt->fetchColumn();
}

function getTotalDosen($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM dosen");
    return $stmt->fetchColumn();
}

function getTotalUsers($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    return $stmt->fetchColumn();
}

function getTotalBerita($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM berita");
    return $stmt->fetchColumn();
}

function getTotalAktivitas($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM aktivitas");
    return $stmt->fetchColumn();
}

function getTotalFacilities($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM fasilitas");
    return $stmt->fetchColumn();
}


?>