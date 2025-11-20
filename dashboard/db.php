<?php
// Sesuaikan credential DB di bawah
$DB_HOST = 'localhost';
$DB_PORT = 5432;
$DB_NAME = 'PBL';
$DB_USER = 'user kalian';
$DB_PASS = 'password user kalian';

// Gunakan DSN untuk PostgreSQL (pgSQL)
$dsn = "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, // Tambahan opsi yang disarankan untuk PGSQL
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    // echo "Koneksi Berhasil!"; // Untuk tes saja
} catch (PDOException $e) {
    // Tampilkan error spesifik untuk debugging
    die("Koneksi Gagal: " . $e->getMessage());
}
?>