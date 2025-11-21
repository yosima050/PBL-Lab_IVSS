<?php
// PASTIKAN TIDAK ADA SPASI KOSONG SEBELUM <?php
session_start();

// Aktifkan pelaporan error PHP agar terlihat di layar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/db.php'; // Sesuaikan path ini ke db.php Anda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Tangkap input
    $nama            = trim($_POST['name'] ?? '');
    $nim             = trim($_POST['nim'] ?? '');
    $prodi           = trim($_POST['prodi'] ?? '');
    $dosenPembimbing = trim($_POST['dosenPembimbing'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = trim($_POST['password'] ?? '');

    echo "<h2>🔍 Menerima Data Input:</h2>";
    echo "Nama: $nama <br>NIM: $nim <br>Email: $email <br>";

    try {
        // Cek Koneksi
        if (!$pdo) {
            die("❌ GAGAL: Variabel \$pdo tidak ditemukan. Cek file db.php!");
        }

        $pdo->beginTransaction();

        // 1. Cek Duplikat
        // Pastikan nama tabel 'pendaftaran' BENAR ada di database
        $chk = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE nim = :nim OR email_mahasiswa = :email");
        $chk->execute([':nim' => $nim, ':email' => $email]);
        if ($chk->fetchColumn() > 0) {
            throw new Exception("NIM atau Email sudah terdaftar sebelumnya.");
        }

        // 2. Hash Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. Insert Data
        // Pastikan nama kolom di bawah ini PERSIS SAMA dengan di phpMyAdmin
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

        if ($result) {
            $pdo->commit();
            // --- SUKSES ---
            echo "<div style='background-color: #d4edda; padding: 20px; border: 1px solid green; margin-top: 20px;'>";
            echo "<h1>✅ BERHASIL!</h1>";
            echo "<p>Data sukses masuk ke database.</p>";
            echo "<p>Silakan klik tombol di bawah untuk kembali ke Index:</p>";
            // Tombol Manual untuk tes path
            echo "<a href='../index.php' style='padding:10px 20px; background:blue; color:white; text-decoration:none;'>KE DASHBOARD (../index.php)</a>";
            echo "</div>";
            exit; // Berhenti di sini
        } else {
            throw new Exception("Execute mengembalikan false (Gagal Insert).");
        }

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // --- ERROR ---
        echo "<div style='background-color: #f8d7da; padding: 20px; border: 1px solid red; margin-top: 20px;'>";
        echo "<h1>❌ TERJADI ERROR</h1>";
        echo "<strong>Pesan Error:</strong> " . $e->getMessage() . "<br>";
        echo "<strong>Di File:</strong> " . $e->getFile() . " <strong>Baris:</strong> " . $e->getLine();
        echo "</div>";
        exit;
    }
} else {
    echo "Akses halaman ini harus menggunakan method POST (Klik tombol Daftar).";
}
?>