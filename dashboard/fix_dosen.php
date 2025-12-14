<?php
session_start();
require_once __DIR__ . '/db.php'; // Sesuaikan path jika file ini ada di luar folder dashboard

// Cek Admin (Opsional, matikan jika tidak bisa login)
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_sistem') {
//     die("Harap login sebagai Admin Sistem.");
// }

echo "<h3>Memulai PERBAIKAN TOTAL Akun Dosen...</h3><hr>";

try {
    $pdo->beginTransaction();

    // 1. Ambil SEMUA data dosen
    $stmt = $pdo->query("SELECT * FROM dosen ORDER BY id_dosen ASC");
    $dosenList = $stmt->fetchAll();

    $count = 0;

    foreach ($dosenList as $d) {
        $idDosen = $d['id_dosen'];
        $nama    = $d['nama_dosen'];
        
        // 2. Siapkan Data Akun Baru
        // Gunakan email asli jika ada, jika tidak buat dummy unik
        // Tambahkan rand() agar email pasti unik dan tidak bentrok dengan data lama
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama));
        $email     = !empty($d['email_dosen']) ? $d['email_dosen'] : $cleanName . rand(100,999) . '@polinema.ac.id';
        
        // Cek apakah email sudah ada di tabel users? Jika ada, modifikasi sedikit
        $cekEmail = $pdo->prepare("SELECT count(*) FROM users WHERE email_users = ?");
        $cekEmail->execute([$email]);
        if ($cekEmail->fetchColumn() > 0) {
            $email = $cleanName . '_' . time() . rand(10,99) . '@polinema.ac.id';
        }

        $password = password_hash('123456', PASSWORD_DEFAULT); // Password Default

        // 3. Buat User Baru (Role 4 = Dosen)
        $stmtUser = $pdo->prepare("INSERT INTO users (id_role, nama_users, email_users, password, id_dosen) 
                                   VALUES (4, :nama, :email, :pass, :iddosen) RETURNING id_users");
        $stmtUser->execute([
            'nama' => $nama,
            'email' => $email,
            'pass' => $password,
            'iddosen' => $idDosen
        ]);
        $newUserId = $stmtUser->fetchColumn();

        // 4. Update Tabel Dosen dengan User ID yang BARU dan UNIK
        $stmtUpdate = $pdo->prepare("UPDATE dosen SET id_users = :idu, email_dosen = :email WHERE id_dosen = :idd");
        $stmtUpdate->execute([
            'idu' => $newUserId,
            'email' => $email, // Update email di tabel dosen juga biar sinkron
            'idd' => $idDosen
        ]);

        echo "Perbaikan Berhasil: <b>$nama</b> -> User ID Baru: <span style='color:blue'>$newUserId</span> (Email: $email)<br>";
        $count++;
    }

    // 5. Refresh View
    $pdo->query("REFRESH MATERIALIZED VIEW mv_dosen");

    $pdo->commit();

    echo "<hr><h3 style='color:green'>SUKSES! $count Dosen telah dibuatkan akun baru yang unik.</h3>";
    echo "<p>Silakan login dengan email di atas dan password: <b>123456</b></p>";
    echo "<a href='dashboard/dosen.php'>Kembali ke Dashboard Dosen</a>";

} catch (Exception $e) {
    $pdo->rollBack();
    die("<h3 style='color:red'>GAGAL: " . $e->getMessage() . "</h3>");
}
?>