<?php
session_start();

// Aktifkan pelaporan error PHP untuk development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/db.php'; // Sesuaikan path ini ke db.php Anda

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Akses harus lewat POST
    header("Location: ../index.php");
    exit;
}

// Tangkap input dan simpan old untuk repopulate jika error
$_SESSION['old'] = $_POST;

$nama            = trim($_POST['name'] ?? '');
$nim             = trim($_POST['nim'] ?? '');
$prodi           = trim($_POST['prodi'] ?? '');
$dosenPembimbing = trim($_POST['dosenPembimbing'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password        = trim($_POST['password'] ?? '');

// Validasi singkat (bisa diperluas)
$errors = [];
if ($nama === '') $errors[] = 'Nama harus diisi.';
if ($nim === '') $errors[] = 'NIM harus diisi.';
if ($prodi === '') $errors[] = 'Prodi harus diisi.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
if ($password === '' || strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

if (!empty($errors)) {
    // Simpan error spesifik agar daftarMember.php menampilkan alert dan repopulate form
    $_SESSION['register_errors'] = $errors;
    header("Location: daftarMember.php");
    exit;
}

try {
    if (!isset($pdo)) {
        throw new Exception("Koneksi database tidak tersedia. Cek db.php.");
    }

    $pdo->beginTransaction();

    // Cek duplikat NIM atau email (sesuaikan nama tabel/kolom)
    $chk = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE nim = :nim OR email_mahasiswa = :email");
    $chk->execute([':nim' => $nim, ':email' => $email]);
    if ($chk->fetchColumn() > 0) {
        $pdo->rollBack();
        // redirect kembali ke form dengan pesan error
        $_SESSION['register_errors'] = ['NIM atau Email sudah terdaftar.'];
        header("Location: daftarMember.php");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO pendaftaran 
            (nim, nama_mahasiswa, prodi, email_mahasiswa, status_mahasiswa, nama_dosen, password_sementara) 
            VALUES 
            (:nim, :nama, :prodi, :email, 'Pending', :dosen, :pass)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':nim'   => $nim,
        ':nama'  => $nama,
        ':prodi' => $prodi,
        ':email' => $email,
        ':dosen' => $dosenPembimbing,
        ':pass'  => $hashed_password
    ]);

    if (!$result) {
        throw new Exception("Gagal menyimpan data pendaftaran.");
    }

    $pdo->commit();

    // Hapus old input, set flash sukses, redirect ke index
    unset($_SESSION['old']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Pendaftaran berhasil dikirim. Silakan tunggu konfirmasi dari admin.'];
    header("Location: ../index.php");
    exit;

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Simpan pesan error agar form menampilkan alert, tetap di daftarMember.php
    $msg = 'Terjadi error saat memproses pendaftaran.';
    $_SESSION['register_errors'] = [$msg];
    // Untuk debugging development, dapat tambahkan detail ke session jika diperlukan:
    // $_SESSION['register_errors'][] = $e->getMessage();

    header("Location: daftarMember.php");
    exit;
}
?>