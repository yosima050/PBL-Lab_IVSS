<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dosen.php");
    exit;
}

// 1. TANGKAP SEMUA INPUT
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

// 2. PROSES UPLOAD FOTO (DUAL FOLDER)
$uploadDirDash = __DIR__ . '/../uploads/';
$uploadDirRoot = __DIR__ . '/uploads/'; // Sesuaikan dengan struktur folder Anda (folder root)

// Buat folder jika belum ada
foreach ([$uploadDirDash, $uploadDirRoot] as $dir) {
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
}

// Cek Foto Lama
$fotoName = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT foto_dosen FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id]);
    $fotoLama = $stmt->fetchColumn();
    $fotoName = $fotoLama;
}

if (!empty($_FILES['foto_dosen']['name'])) {
    $ext = pathinfo($_FILES['foto_dosen']['name'], PATHINFO_EXTENSION);
    $fotoName = 'dosen_' . time() . '.' . $ext;

    // Upload
    if (move_uploaded_file($_FILES['foto_dosen']['tmp_name'], $uploadDirDash . $fotoName)) {
        // Copy ke folder root agar frontend bisa akses
        @copy($uploadDirDash . $fotoName, $uploadDirRoot . $fotoName);
        
        // Hapus foto lama
        if ($fotoLama) {
            @unlink($uploadDirDash . $fotoLama);
            @unlink($uploadDirRoot . $fotoLama);
        }
    }
}

// 3. EXECUTE QUERY
if (!$id) {
    // === INSERT ===
    $sql = "INSERT INTO dosen (
                nama_dosen, nip, nidn_dosen, jabatan_dosen, prodi_dosen, 
                bidang_riset, pendidikan_dosen, sertifikasi_dosen, mata_kuliah_dosen,
                email_dosen, alamat_kantor, link_linkedin, link_google_scholar, link_sinta, foto_dosen
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nama, $nip, $nidn, $jabatan, $prodi, 
        $bidang, $pendidikan, $sertifikasi, $matkul,
        $email, $alamat, $linkedin, $scholar, $sinta, $fotoName
    ]);
    
    $message = "Dosen berhasil ditambahkan!";
} else {
    // === UPDATE ===
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
    
    $message = "Data dosen berhasil diperbarui!";
}

// 4. REFRESH MATERIALIZED VIEW (Jika Pakai)
$pdo->query("REFRESH MATERIALIZED VIEW mv_dosen");

$_SESSION['flash'] = $message;
header("Location: dosen.php");
exit;
?>