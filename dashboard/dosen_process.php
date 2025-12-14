<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dosen.php");
    exit;
}

// Tangkap Input
$id         = $_POST['id_dosen'] ?? null;
$nama       = $_POST['nama_dosen'];
$nip        = $_POST['nip'];
$nidn       = $_POST['nidn_dosen'];
$jabatan    = $_POST['jabatan_dosen'];
$prodi      = $_POST['prodi_dosen'];
$bidang     = $_POST['bidang_riset'];
$pendidikan = $_POST['pendidikan_dosen'];
$sertifikasi= $_POST['sertifikasi_dosen'];
$matkul     = $_POST['mata_kuliah_dosen'];
$email      = $_POST['email_dosen'];
$alamat     = $_POST['alamat_kantor'];
$linkedin   = $_POST['link_linkedin'];
$scholar    = $_POST['link_google_scholar'];
$sinta      = $_POST['link_sinta'];

// Konfigurasi Upload
$uploadDirDash = __DIR__ . '/../uploads/';
$uploadDirRoot = __DIR__ . '/uploads/'; 
foreach ([$uploadDirDash, $uploadDirRoot] as $dir) {
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
}

// Cek Foto Lama (Jika Edit)
$fotoName = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT foto_dosen FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id]);
    $fotoName = $stmt->fetchColumn();
}

// Proses Upload Baru
if (!empty($_FILES['foto_dosen']['name'])) {
    $ext = pathinfo($_FILES['foto_dosen']['name'], PATHINFO_EXTENSION);
    $newFoto = 'dosen_' . time() . '.' . $ext;

    if (move_uploaded_file($_FILES['foto_dosen']['tmp_name'], $uploadDirDash . $newFoto)) {
        @copy($uploadDirDash . $newFoto, $uploadDirRoot . $newFoto);
        
        // Hapus foto lama
        if ($id && $fotoName) {
            @unlink($uploadDirDash . $fotoName);
            @unlink($uploadDirRoot . $fotoName);
        }
        $fotoName = $newFoto;
    }
}

try {
    $pdo->beginTransaction();

    /*==================================================
    |   MODE TAMBAH (INSERT USER + DOSEN)
    ====================================================*/
    if (!$id) {
        
        // 1. Buat User Baru (Role 4 = Dosen)
        // Password default: 123456 (Bisa diganti nanti)
        $defaultPass = password_hash('123456', PASSWORD_DEFAULT);
        
        $stmtUser = $pdo->prepare("INSERT INTO users (id_role, nama_users, email_users, password) 
                                   VALUES (4, :nama, :email, :pass) RETURNING id_users");
        $stmtUser->execute([
            'nama' => $nama,
            'email' => $email, // Pastikan email unik di DB
            'pass' => $defaultPass
        ]);
        $newUserId = $stmtUser->fetchColumn();

        // 2. Buat Data Dosen (Tautkan id_users)
        $sql = "INSERT INTO dosen (
                    id_users, nama_dosen, nip, nidn_dosen, jabatan_dosen, prodi_dosen, 
                    bidang_riset, pendidikan_dosen, sertifikasi_dosen, mata_kuliah_dosen,
                    email_dosen, alamat_kantor, link_linkedin, link_google_scholar, link_sinta, foto_dosen
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id_dosen";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $newUserId, $nama, $nip, $nidn, $jabatan, $prodi, 
            $bidang, $pendidikan, $sertifikasi, $matkul,
            $email, $alamat, $linkedin, $scholar, $sinta, $fotoName
        ]);
        $newDosenId = $stmt->fetchColumn();

        // 3. Update User agar punya id_dosen (Redundansi untuk kemudahan query)
        $pdo->prepare("UPDATE users SET id_dosen = ? WHERE id_users = ?")->execute([$newDosenId, $newUserId]);

        $message = "Dosen dan Akun User berhasil dibuat! (Password Default: 123456)";
    } 
    
    /*==================================================
    |   MODE EDIT (UPDATE DOSEN SAJA)
    ====================================================*/
    else {
        // Update tabel dosen
        $sql = "UPDATE dosen SET 
                    nama_dosen = ?, nip = ?, nidn_dosen = ?, jabatan_dosen = ?, prodi_dosen = ?, 
                    bidang_riset = ?, pendidikan_dosen = ?, sertifikasi_dosen = ?, mata_kuliah_dosen = ?,
                    email_dosen = ?, alamat_kantor = ?, link_linkedin = ?, link_google_scholar = ?, link_sinta = ?, foto_dosen = ?
                WHERE id_dosen = ?";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nama, $nip, $nidn, $jabatan, $prodi, 
            $bidang, $pendidikan, $sertifikasi, $matkul,
            $email, $alamat, $linkedin, $scholar, $sinta, $fotoName, $id
        ]);

        // Opsional: Update nama & email di tabel users juga agar sinkron
        $pdo->prepare("UPDATE users SET nama_users = ?, email_users = ? WHERE id_dosen = ?")
            ->execute([$nama, $email, $id]);
        
        $message = "Data dosen berhasil diperbarui!";
    }

    // Refresh View
    $pdo->query("REFRESH MATERIALIZED VIEW mv_dosen");
    
    $pdo->commit();
    $_SESSION['flash'] = $message;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = "Gagal: " . $e->getMessage(); // Tampilkan error jika email duplikat dll
}

header("Location: dosen.php");
exit;
?>