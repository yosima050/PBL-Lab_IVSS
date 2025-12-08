<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dosen.php");
    exit;
}

$id     = $_POST['id_dosen'] ?? null; // jika ada = edit, kalau kosong = tambah
$nama   = $_POST['nama_dosen'];
$nip    = $_POST['nip'];
$nidn   = $_POST['nidn_dosen'];
$bidang = $_POST['bidang_riset'];
$jabatan = $_POST['jabatan_dosen'];

$uploadDirDash = __DIR__ . '/../uploads/';
$uploadDirRoot = __DIR__ . '/uploads/';
$fotoName = null;

// helper: pastikan folder ada
foreach ([$uploadDirDash, $uploadDirRoot] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

// Jika EDIT → ambil foto lama
if ($id) {
    $stmt = $pdo->prepare("SELECT foto_dosen FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id]);
    $fotoLama = $stmt->fetchColumn();
    $fotoName = $fotoLama;
}

// Upload foto baru jika ada
if (!empty($_FILES['foto_dosen']['name'])) {
    $ext = pathinfo($_FILES['foto_dosen']['name'], PATHINFO_EXTENSION);
    $fotoName = 'dosen_' . time() . '.' . $ext;

    // simpan ke folder dashboard
    if (move_uploaded_file($_FILES['foto_dosen']['tmp_name'], $uploadDirDash . $fotoName)) {
        // salin ke folder frontend
        @copy($uploadDirDash . $fotoName, $uploadDirRoot . $fotoName);
    } else {
        $fotoName = $fotoLama ?? null;
    }

    // Hapus foto lama di kedua lokasi
    if ($id && $fotoLama) {
        if (file_exists($uploadDirDash . $fotoLama)) @unlink($uploadDirDash . $fotoLama);
        if (file_exists($uploadDirRoot . $fotoLama)) @unlink($uploadDirRoot . $fotoLama);
    }
}

/*==================================================
|   MODE TAMBAH
====================================================*/
if (!$id) {
    $stmt = $pdo->prepare("
        INSERT INTO dosen (nama_dosen, nip, nidn_dosen, bidang_riset, jabatan_dosen, foto_dosen)
        VALUES (:nama, :nip, :nidn, :bidang, :jabatan, :foto)
    ");

    $stmt->execute([
        'nama' => $nama,
        'nip' => $nip,
        'nidn' => $nidn,
        'bidang' => $bidang,
        'jabatan' => $jabatan,
        'foto' => $fotoName
    ]);

    $message = "Data dosen berhasil ditambahkan!";
}

/*==================================================
|   MODE EDIT
====================================================*/
else {
    $stmt = $pdo->prepare("
        UPDATE dosen 
        SET nama_dosen = :nama,
            nip = :nip,
            nidn_dosen = :nidn,
            bidang_riset = :bidang,
            jabatan_dosen = :jabatan,
            foto_dosen = :foto
        WHERE id_dosen = :id
    ");

    $stmt->execute([
        'nama' => $nama,
        'nip' => $nip,
        'nidn' => $nidn,
        'bidang' => $bidang,
        'jabatan' => $jabatan,
        'foto' => $fotoName,
        'id' => $id
    ]);

    $message = "Data dosen berhasil diperbarui!";
}

$pdo->query("REFRESH MATERIALIZED VIEW mv_dosen");

$_SESSION['flash'] = $message;
header("Location: dosen.php");
exit;
?>
