<?php
session_start();

// --- VALIDASI LOGIN ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}
// Cek Role
if (!in_array($_SESSION['role'], ['admin_sistem', 'admin_berita', 'ketua_lab'])) { 
    echo "<script>alert('AKSES DITOLAK!'); window.location='dashboard.php';</script>"; exit;
}

require_once __DIR__ . '/db.php';
$username = $_SESSION['nama_users'] ?? 'Admin';

// Variabel untuk sidebar.php
$role = $_SESSION['role'] ?? null;
$pendingCount = 0;
$waitingApproval = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Pending'");
    $stmt->execute();
    $pendingCount = (int) $stmt->fetchColumn();
} catch (Exception $e) {
    $pendingCount = 0;
}
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Menunggu'");
    $stmt->execute();
    $waitingApproval = (int) $stmt->fetchColumn();
} catch (Exception $e) {
    $waitingApproval = 0;
}

// Konfigurasi Folder Upload
$uploadDir = __DIR__ . '/../uploads/'; 
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);

// =======================================================
// 1. FITUR BARU: HAPUS ITEM GALERI (SATUAN)
// =======================================================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_galeri') {
    $id_galeri = $_GET['id_galeri'];
    $id_aktivitas = $_GET['id_aktivitas']; // Untuk redirect kembali ke halaman edit

    try {
        // Ambil nama file sebelum dihapus
        $stmt = $pdo->prepare("SELECT foto_galeri, video_galeri FROM galeri WHERE id_galeri = :id");
        $stmt->execute(['id' => $id_galeri]);
        $item = $stmt->fetch();

        if ($item) {
            // Hapus file fisik
            if (!empty($item['foto_galeri']) && file_exists($uploadDir . $item['foto_galeri'])) {
                @unlink($uploadDir . $item['foto_galeri']);
            }
            if (!empty($item['video_galeri']) && file_exists($uploadDir . $item['video_galeri'])) {
                @unlink($uploadDir . $item['video_galeri']);
            }

            // Hapus record dari database
            $del = $pdo->prepare("DELETE FROM galeri WHERE id_galeri = :id");
            $del->execute(['id' => $id_galeri]);

            $_SESSION['message'] = "Item galeri berhasil dihapus.";
            $_SESSION['msg_type'] = "success";
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Gagal menghapus item: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }

    // Redirect kembali ke mode EDIT aktivitas tersebut
    header("Location: aktivitas.php?aksi=edit&id=" . $id_aktivitas);
    exit;
}

// =======================================================
// 2. HAPUS SELURUH AKTIVITAS
// =======================================================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    try {
        // Hapus semua file terkait di galeri
        $stmt = $pdo->prepare("SELECT foto_galeri, video_galeri FROM galeri WHERE id_aktivitas = :id");
        $stmt->execute(['id' => $id]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($files as $f) {
            if (!empty($f['foto_galeri']) && file_exists($uploadDir . $f['foto_galeri'])) @unlink($uploadDir . $f['foto_galeri']);
            if (!empty($f['video_galeri']) && file_exists($uploadDir . $f['video_galeri'])) @unlink($uploadDir . $f['video_galeri']);
        }

        // Hapus Database
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM galeri WHERE id_aktivitas = :id")->execute(['id' => $id]);
        $pdo->prepare("DELETE FROM aktivitas WHERE id_aktivitas = :id")->execute(['id' => $id]);
        $pdo->commit();

        $_SESSION['message'] = "Aktivitas dan semua galeri berhasil dihapus!";
        $_SESSION['msg_type'] = "success";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Gagal hapus: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
    header("Location: aktivitas.php"); exit;
}

// =======================================================
// FUNGSI HELPER UPLOAD
// =======================================================
function processUploads($pdo, $id_aktivitas, $uploadDir) {
    // A. HANDLE FOTO (Multiple)
    if (!empty($_FILES['foto_galeri']['name'][0])) {
        $count = count($_FILES['foto_galeri']['name']);
        $stmtFoto = $pdo->prepare("INSERT INTO galeri (id_aktivitas, foto_galeri, judul_foto) VALUES (:id, :file, :judul)");

        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['foto_galeri']['error'][$i] == 0) {
                $ext = strtolower(pathinfo($_FILES['foto_galeri']['name'][$i], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $newName = 'img_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['foto_galeri']['tmp_name'][$i], $uploadDir . $newName)) {
                        $stmtFoto->execute(['id' => $id_aktivitas, 'file' => $newName, 'judul' => $_POST['judul_aktivitas']]);
                        
                        // Set foto pertama sebagai thumbnail aktivitas jika belum ada
                        // (Opsional, tergantung logika Anda mau update thumbnail utama atau tidak)
                        $pdo->prepare("UPDATE aktivitas SET foto_galeri = :f WHERE id_aktivitas = :id AND (foto_galeri IS NULL OR foto_galeri = '')")
                            ->execute(['f' => $newName, 'id' => $id_aktivitas]);
                    }
                }
            }
        }
    }

    // B. HANDLE VIDEO (Multiple) - FITUR BARU
    if (!empty($_FILES['video_galeri']['name'][0])) {
        $countV = count($_FILES['video_galeri']['name']);
        $stmtVideo = $pdo->prepare("INSERT INTO galeri (id_aktivitas, video_galeri, judul_foto) VALUES (:id, :file, :judul)");

        for ($i = 0; $i < $countV; $i++) {
            if ($_FILES['video_galeri']['error'][$i] == 0) {
                $ext = strtolower(pathinfo($_FILES['video_galeri']['name'][$i], PATHINFO_EXTENSION));
                // Validasi Video
                if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
                    $newName = 'vid_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['video_galeri']['tmp_name'][$i], $uploadDir . $newName)) {
                        // Simpan ke kolom video_galeri
                        $stmtVideo->execute(['id' => $id_aktivitas, 'file' => $newName, 'judul' => $_POST['judul_aktivitas'] . ' (Video)']);
                    }
                }
            }
        }
    }
}

// =======================================================
// PROSES INSERT & UPDATE
// =======================================================
if (isset($_POST['simpan_data'])) {
    try {
        $pdo->beginTransaction();

        if ($_POST['aksi_form'] == 'tambah') {
            // INSERT
            $stmt = $pdo->prepare("INSERT INTO aktivitas (judul_aktivitas, isi_aktivitas, tanggal_mulai_aktivitas, tanggal_selesai_aktivitas, tag_aktivitas, created_at_aktivitas) 
                                   VALUES (:judul, :isi, :mulai, :selesai, :tag, NOW()) RETURNING id_aktivitas");
            $stmt->execute([
                'judul' => $_POST['judul_aktivitas'], 'isi' => $_POST['isi_aktivitas'],
                'mulai' => $_POST['tanggal_mulai_aktivitas'], 'selesai' => $_POST['tanggal_selesai_aktivitas'],
                'tag' => $_POST['tag_aktivitas']
            ]);
            $id_aktivitas = $stmt->fetchColumn();
            $msg = "Aktivitas berhasil ditambahkan!";

        } else {
            // UPDATE
            $id_aktivitas = $_POST['id_aktivitas'];
            $stmt = $pdo->prepare("UPDATE aktivitas SET 
                judul_aktivitas = :judul, isi_aktivitas = :isi, 
                tanggal_mulai_aktivitas = :mulai, tanggal_selesai_aktivitas = :selesai, 
                tag_aktivitas = :tag 
                WHERE id_aktivitas = :id");
            $stmt->execute([
                'judul' => $_POST['judul_aktivitas'], 'isi' => $_POST['isi_aktivitas'],
                'mulai' => $_POST['tanggal_mulai_aktivitas'], 'selesai' => $_POST['tanggal_selesai_aktivitas'],
                'tag' => $_POST['tag_aktivitas'], 'id' => $id_aktivitas
            ]);
            $msg = "Aktivitas berhasil diperbarui!";
        }

        // Panggil Fungsi Upload (Foto & Video)
        processUploads($pdo, $id_aktivitas, $uploadDir);

        $pdo->commit();
        $_SESSION['message'] = $msg;
        $_SESSION['msg_type'] = "success";

        // Jika update, tetap di halaman edit agar bisa lihat hasil upload
        if ($_POST['aksi_form'] == 'update') {
            header("Location: aktivitas.php?aksi=edit&id=" . $id_aktivitas);
            exit;
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
    header("Location: aktivitas.php"); exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Aktivitas</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        /* Style untuk Grid Galeri di Dashboard */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .gallery-item {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            height: 120px;
            background: #f8f9fa;
        }
        .gallery-item img, .gallery-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-delete-img {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(231, 74, 59, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .btn-delete-img:hover {
            background: red;
            transform: scale(1.1);
        }
        .gallery-type-icon {
            position: absolute;
            bottom: 2px;
            left: 5px;
            color: white;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
            font-size: 14px;
        }
    </style>
</head>

<body id="page-top">
<div id="wrapper">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><span class="mr-2 text-gray-600 small">Halo, <b><?= htmlspecialchars($username) ?></b></span></li>
                </ul>
            </nav>

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Aktivitas & Galeri (Foto/Video)</h1>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php unset($_SESSION['message'], $_SESSION['msg_type']); endif; ?>

                <?php
                // =========================
                // FORM EDIT / TAMBAH (DIGABUNG LOGIKANYA)
                // =========================
                if (isset($_GET['aksi']) && ($_GET['aksi'] == 'edit' || $_GET['aksi'] == 'tambah')):
                    $isEdit = ($_GET['aksi'] == 'edit');
                    $row = [];
                    $photos = [];

                    if ($isEdit) {
                        $id = $_GET['id'];
                        $d = $pdo->prepare("SELECT * FROM aktivitas WHERE id_aktivitas = :id");
                        $d->execute(['id' => $id]);
                        $row = $d->fetch();
                        
                        // Ambil Galeri (Foto & Video)
                        $g = $pdo->prepare("SELECT * FROM galeri WHERE id_aktivitas = :id ORDER BY id_galeri DESC");
                        $g->execute(['id' => $id]);
                        $photos = $g->fetchAll();
                    }
                ?>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $isEdit ? 'Edit Aktivitas' : 'Tambah Aktivitas Baru' ?></h6>
                        <?php if ($isEdit): ?>
                            <a href="aktivitas.php?aksi=tambah" class="btn btn-sm btn-success">+ Buat Baru</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="aksi_form" value="<?= $isEdit ? 'update' : 'tambah' ?>">
                            <?php if ($isEdit): ?>
                                <input type="hidden" name="id_aktivitas" value="<?= $row['id_aktivitas'] ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Judul Aktivitas</label>
                                <input type="text" name="judul_aktivitas" class="form-control" value="<?= $row['judul_aktivitas'] ?? '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Isi Aktivitas</label>
                                <textarea name="isi_aktivitas" class="form-control" rows="5" required><?= $row['isi_aktivitas'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Tag</label>
                                    <select name="tag_aktivitas" class="form-control">
                                        <option value="Penelitian" <?= ($row['tag_aktivitas'] ?? '') == 'Penelitian' ? 'selected' : '' ?>>Penelitian</option>
                                        <option value="Riset" <?= ($row['tag_aktivitas'] ?? '') == 'Riset' ? 'selected' : '' ?>>Riset</option>
                                        <option value="Publikasi" <?= ($row['tag_aktivitas'] ?? '') == 'Publikasi' ? 'selected' : '' ?>>Publikasi</option>
                                        <option value="Pengabdian" <?= ($row['tag_aktivitas'] ?? '') == 'Pengabdian' ? 'selected' : '' ?>>Pengabdian</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Mulai</label>
                                    <input type="date" name="tanggal_mulai_aktivitas" class="form-control" value="<?= $row['tanggal_mulai_aktivitas'] ?? '' ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Selesai</label>
                                    <input type="date" name="tanggal_selesai_aktivitas" class="form-control" value="<?= $row['tanggal_selesai_aktivitas'] ?? '' ?>">
                                </div>
                            </div>

                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-success"><i class="fas fa-images"></i> Upload Foto Baru</label>
                                        <input type="file" name="foto_galeri[]" class="form-control" multiple accept="image/*" <?= $isEdit ? '' : 'required' ?>>
                                        <small class="text-muted">Format: JPG, PNG, WEBP. Bisa pilih banyak.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-danger"><i class="fas fa-video"></i> Upload Video Baru</label>
                                        <input type="file" name="video_galeri[]" class="form-control" multiple accept="video/*">
                                        <small class="text-muted">Format: MP4, WEBM. Bisa pilih banyak. <b>Max size per file tergantung config PHP.</b></small>
                                    </div>
                                </div>
                            </div>

                            <?php if ($isEdit && !empty($photos)): ?>
                                <div class="card mt-3 border-left-info">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-info">Galeri Saat Ini (Klik X untuk menghapus)</h6>
                                        <div class="gallery-grid">
                                            <?php foreach ($photos as $p): ?>
                                                <div class="gallery-item">
                                                    <a href="aktivitas.php?aksi=hapus_galeri&id_galeri=<?= $p['id_galeri'] ?>&id_aktivitas=<?= $id ?>" 
                                                       class="btn-delete-img" onclick="return confirm('Hapus item ini?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>

                                                    <?php if (!empty($p['video_galeri'])): ?>
                                                        <video src="../uploads/<?= $p['video_galeri'] ?>" controls></video>
                                                        <div class="gallery-type-icon"><i class="fas fa-video"></i> Video</div>
                                                    <?php else: ?>
                                                        <img src="../uploads/<?= $p['foto_galeri'] ?>">
                                                        <div class="gallery-type-icon"><i class="fas fa-image"></i> Foto</div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <hr>
                            <button type="submit" name="simpan_data" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Simpan Data</button>
                            <a href="aktivitas.php" class="btn btn-secondary btn-lg">Kembali</a>
                        </form>
                    </div>
                </div>

                <?php
                // =========================
                // TABEL DATA UTAMA
                // =========================
                else:
                    // Query untuk list utama
                    $stmt = $pdo->query("SELECT a.*, 
                                        (SELECT COUNT(*) FROM galeri g WHERE g.id_aktivitas = a.id_aktivitas AND g.foto_galeri IS NOT NULL) as jml_foto,
                                        (SELECT COUNT(*) FROM galeri g WHERE g.id_aktivitas = a.id_aktivitas AND g.video_galeri IS NOT NULL) as jml_video
                                        FROM aktivitas a ORDER BY created_at_aktivitas DESC");
                    $data = $stmt->fetchAll();
                ?>

                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Aktivitas</h6>
                        <a href="aktivitas.php?aksi=tambah" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Baru</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Thumbnail</th>
                                        <th width="25%">Judul</th>
                                        <th width="10%">Tag</th>
                                        <th width="15%">Media</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($data as $d): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?php if (!empty($d['foto_galeri']) && file_exists('../uploads/' . $d['foto_galeri'])): ?>
                                                <img src="../uploads/<?= $d['foto_galeri'] ?>" width="100" style="border-radius:5px; object-fit:cover; height:60px;">
                                            <?php else: ?>
                                                <div class="bg-light text-center py-3 rounded text-muted small"><i class="fas fa-image"></i> No Img</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($d['judul_aktivitas']) ?></strong>
                                            <br><small class="text-muted"><?= substr($d['isi_aktivitas'], 0, 50) ?>...</small>
                                        </td>
                                        <td><span class="badge badge-info"><?= $d['tag_aktivitas'] ?></span></td>
                                        <td>
                                            <small>
                                                <i class="fas fa-image"></i> <?= $d['jml_foto'] ?> Foto<br>
                                                <i class="fas fa-video"></i> <?= $d['jml_video'] ?> Video
                                            </small>
                                        </td>
                                        <td><small><?= $d['tanggal_mulai_aktivitas'] ?><br>s/d<br><?= $d['tanggal_selesai_aktivitas'] ?></small></td>
                                        <td>
                                            <a href="aktivitas.php?aksi=edit&id=<?= $d['id_aktivitas'] ?>" class="btn btn-warning btn-sm btn-circle" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="aktivitas.php?aksi=hapus&id=<?= $d['id_aktivitas'] ?>" class="btn btn-danger btn-sm btn-circle" onclick="return confirm('Yakin hapus aktivitas ini beserta galerinya?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <footer class="sticky-footer bg-white">
            <div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; LAB IVSS</span></div></div>
        </footer>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script> $(document).ready(function(){ $('#dataTable').DataTable(); }); </script>
</body>
</html>