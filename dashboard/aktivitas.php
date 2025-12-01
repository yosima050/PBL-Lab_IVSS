<?php
session_start();

// --- VALIDASI LOGIN ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin_aktivitas yang boleh akses
if ($_SESSION['role'] !== 'admin_berita') {
    echo "<script>
            alert('AKSES DITOLAK! Halaman ini hanya untuk Admin berita.');
            window.location='dashboard.php';
          </script>";
    exit;
}

require_once __DIR__ . '/db.php';

$username = $_SESSION['nama_users'] ?? 'Admin';

// --- Sidebar variables: make sure sidebar.php can read role and counters ---
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

// pastikan folder uploads ada (menghindari warning move_uploaded_file)
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// =======================================================
// DELETE
// =======================================================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];

        // ambil semua foto yang ada di galeri
        $stmt = $pdo->prepare("SELECT foto_galeri FROM galeri WHERE id_aktivitas = :id");
        $stmt->execute(['id' => $id]);
        $galeriFiles = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // hapus file-file foto dari folder
        foreach ($galeriFiles as $file) {
            if ($file && file_exists($uploadDir . $file)) {
            @unlink($uploadDir . $file);
            }
        }

     // 3. Hapus galeri dari database
    $stmt = $pdo->prepare("DELETE FROM galeri WHERE id_aktivitas = :id");
    $stmt->execute(['id' => $id]);

    // 4. Hapus aktivitas
    $stmt = $pdo->prepare("DELETE FROM aktivitas WHERE id_aktivitas = :id");
    $stmt->execute(['id' => $id]);

    $_SESSION['message'] = "Aktivitas berhasil dihapus!";
    $_SESSION['msg_type'] = "success";
    header("Location: aktivitas.php");
    exit;
}
// =======================================================
// UPDATE
// =======================================================
if (isset($_POST['update'])) {

    $id     = $_POST['id_aktivitas'];
    $judul  = trim($_POST['judul_aktivitas']);
    $isi    = trim($_POST['isi_aktivitas']);
    $mulai  = $_POST['tanggal_mulai_aktivitas'];  // readonly
    $selesai = $_POST['tanggal_selesai_aktivitas'];
    $tag    = $_POST['tag_aktivitas'];

    // simpan input agar tidak hilang setelah error
    $_SESSION['old_input'] = $_POST;

    // VALIDASI
    $errors = [];

    if (empty($judul)) $errors[] = "Judul aktivitas wajib diisi.";
    if (empty($mulai)) $errors[] = "Tanggal mulai wajib diisi.";
    if (empty($selesai)) $errors[] = "Tanggal selesai wajib diisi.";
    if (!empty($mulai) && !empty($selesai) && $mulai > $selesai)
        $errors[] = "Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.";

    // Jika ada error → kembali ke form edit
    if (!empty($errors)) {
        $_SESSION['form_error'] = $errors;
        header("Location: aktivitas.php?aksi=edit&id=".$id);
        exit;
    }

    // Upload foto baru (opsional)
    if (!empty($_FILES['foto_galeri']['name'])) {
        $foto = time() . "_" . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['foto_galeri']['name']));
        move_uploaded_file($_FILES['foto_galeri']['tmp_name'], $uploadDir . $foto);
    } else {
        $foto = $_POST['foto_lama'];
    }

    $stmt = $pdo->prepare("UPDATE aktivitas SET 
        judul_aktivitas = :judul,
        isi_aktivitas = :isi,
        tanggal_mulai_aktivitas = :mulai,
        tanggal_selesai_aktivitas = :selesai,
        tag_aktivitas = :tag,
        foto_galeri = :foto
        WHERE id_aktivitas = :id");

    $stmt->execute([
        'judul' => $judul,
        'isi'   => $isi,
        'mulai' => $mulai,
        'selesai' => $selesai,
        'tag'   => $tag,
        'foto'  => $foto,
        'id'    => $id
    ]);

    unset($_SESSION['old_input']);

    $_SESSION['message'] = "Aktivitas berhasil diupdate!";
    $_SESSION['msg_type'] = "warning";
    header("Location: aktivitas.php");
    exit;
}

// =======================================================
// INSERT
// =======================================================
if (isset($_POST['tambah'])) {

    $judul   = trim($_POST['judul_aktivitas']);
    $isi     = trim($_POST['isi_aktivitas']);
    $mulai   = $_POST['tanggal_mulai_aktivitas'];
    $selesai = $_POST['tanggal_selesai_aktivitas'];
    $tag     = $_POST['tag_aktivitas'];

    $_SESSION['old_input'] = $_POST;

    $errors = [];

    if (empty($judul)) $errors[] = "Judul aktivitas wajib diisi.";
    if (empty($mulai)) $errors[] = "Tanggal mulai wajib diisi.";
    if (empty($selesai)) $errors[] = "Tanggal selesai wajib diisi.";
    if (!empty($mulai) && !empty($selesai) && $mulai > $selesai)
        $errors[] = "Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.";

    if (!empty($errors)) {
        $_SESSION['form_error'] = $errors;
        header("Location: aktivitas.php?aksi=tambah");
        exit;
    }

    // Upload foto
    $foto = null;
    if (!empty($_FILES['foto_galeri']['name'])) {
        $foto = time() . "_" . preg_replace('/[^A-Za-z0-9_.-]/', '_', $_FILES['foto_galeri']['name']);
        move_uploaded_file($_FILES['foto_galeri']['tmp_name'], $uploadDir . $foto);
    }

    // insert normal (lebih simple)
    $stmt = $pdo->prepare("INSERT INTO aktivitas 
        (judul_aktivitas, isi_aktivitas, tanggal_mulai_aktivitas, tanggal_selesai_aktivitas, tag_aktivitas, foto_galeri, created_at_aktivitas)
        VALUES (:judul, :isi, :mulai, :selesai, :tag, :foto, NOW())");

    $stmt->execute([
        'judul' => $judul,
        'isi'   => $isi,
        'mulai' => $mulai,
        'selesai' => $selesai,
        'tag'   => $tag,
        'foto'  => $foto
    ]);

    unset($_SESSION['old_input']);

    $_SESSION['message'] = "Aktivitas berhasil ditambahkan!";
    $_SESSION['msg_type'] = "success";
    header("Location: aktivitas.php");
    exit;
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
</head>

<body id="page-top">
<div id="wrapper">

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- TOPBAR -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                Halo, <b><?= htmlspecialchars($username) ?></b>
                            </span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">Aktivitas & Galeri</h1>

                <!-- Session Message -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
                        <?= $_SESSION['message'] ?>
                    </div>
                <?php unset($_SESSION['message'], $_SESSION['msg_type']); endif; ?>

                <?php
                // =========================
                // FORM EDIT
                // =========================
                if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit'):

                    $id = $_GET['id'];
                    $stmt = $pdo->prepare("SELECT * FROM aktivitas WHERE id_aktivitas = :id");
                    $stmt->execute(['id' => $id]);
                    $d = $stmt->fetch();

                    // Jika foto tidak ada, gunakan placeholder
                    $fotoAktif = !empty($d['foto_galeri']) ? $d['foto_galeri'] : 'noimage.png';
                ?>

                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6>Edit Aktivitas</h6>
                    </div>
                    <div class="card-body">
                        
                        <?php if (isset($_SESSION['form_error'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['form_error'] as $e): ?>
                                    <li><?= $e ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['form_error']); endif; ?>


                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_aktivitas" value="<?= $d['id_aktivitas'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= !empty($d['foto_galeri']) ? $d['foto_galeri'] : '' ?>">

                            <div class="form-group">
                                <label>Judul Aktivitas</label>
                                <input type="text" name="judul_aktivitas" class="form-control" value="<?= $_SESSION['old_input']['judul_aktivitas'] ?? $d['judul_aktivitas'] ?>">
                            </div>

                            <div class="form-group">
                                <label>Isi Aktivitas</label>
                                <textarea name="isi_aktivitas" class="form-control" rows="4"><?= $d['isi_aktivitas'] ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai_aktivitas" class="form-control"  readonly value="<?= $_SESSION['old_input']['tanggal_mulai_aktivitas'] ?? $d['tanggal_mulai_aktivitas'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai_aktivitas" class="form-control" value="<?= $_SESSION['old_input']['tanggal_selesai_aktivitas'] ?? $d['tanggal_selesai_aktivitas'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label>Tag Aktivitas</label>
                                <select name="tag_aktivitas" class="form-control">
                                    <option value="Penelitian" <?= $d['tag_aktivitas'] == "Penelitian" ? "selected" : "" ?>>Penelitian</option>
                                    <option value="Riset" <?= $d['tag_aktivitas'] == "Riset" ? "selected" : "" ?>>Riset</option>
                                    <option value="Publikasi" <?= $d['tag_aktivitas'] == "Publikasi" ? "selected" : "" ?>>Publikasi</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Foto Lama:</label><br>

                                <?php if (!empty($d['foto_galeri'])): ?>
                                    <img src="../uploads/<?= $d['foto_galeri'] ?>" width="140" class="mb-2"><br>
                                <?php else: ?>
                                    <p><i>Tidak ada foto</i></p>
                                <?php endif; ?>

                                <label>Ganti Foto (opsional)</label>
                                <input type="file" name="foto_galeri" class="form-control">
                            </div>

                            <button type="submit" name="update" class="btn btn-warning">Update</button>
                            <a href="aktivitas.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>

                <?php
                // =========================
                // FORM TAMBAH
                // =========================
                elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah'):
                ?>

                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6>Tambah Aktivitas Baru</h6>
                    </div>
                    <div class="card-body">

                        <?php if (isset($_SESSION['form_error'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['form_error'] as $e): ?>
                                    <li><?= $e ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['form_error']); endif; ?>

                        <form method="post" enctype="multipart/form-data">
                            
                            <div class="form-group">
                                <label>Judul Aktivitas</label>
                                <input type="text" name="judul_aktivitas" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Isi Aktivitas</label>
                                <textarea name="isi_aktivitas" class="form-control" rows="4"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai_aktivitas" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai_aktivitas" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Tag Aktivitas</label>
                                <select name="tag_aktivitas" class="form-control">
                                    <option value="Penelitian">Penelitian</option>
                                    <option value="Riset">Riset</option>
                                    <option value="Publikasi">Publikasi</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Foto Aktivitas</label>
                                <input type="file" name="foto_galeri" required>
                            </div>

                            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                            <a href="aktivitas.php" class="btn btn-secondary">Batal</a>

                        </form>

                    </div>
                </div>

                <?php
                // =========================
                // TABEL DATA
                // =========================
                else:
                    $stmt = $pdo->query("SELECT * FROM aktivitas ORDER BY created_at_aktivitas DESC");
                    $data = $stmt->fetchAll();
                ?>

                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Data Aktivitas</h6>
                        <a href="aktivitas.php?aksi=tambah" class="btn btn-primary btn-sm">+ Tambah Aktivitas</a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive"> 
                            
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Judul</th>
                                    <th>Tag</th>
                                    <th>Tgl Mulai</th>
                                    <th>Tgl Selesai</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $no=1; foreach($data as $d): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?php if (!empty($d['foto_galeri'])): ?>
                                            <?php 
                                                $listFoto = explode(',', $d['foto_galeri']); 
                                                foreach ($listFoto as $f): 
                                            ?>
                                                <img src="../uploads/<?= $f ?>" width="70" style="margin-right:5px;">
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span>Tidak ada foto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $d['judul_aktivitas'] ?></td>
                                    <td><?= $d['tag_aktivitas'] ?></td>
                                    <td><?= $d['tanggal_mulai_aktivitas'] ?></td>
                                    <td><?= $d['tanggal_selesai_aktivitas'] ?></td>
                                    <td><?= $d['created_at_aktivitas'] ?></td>
                                    <td>
                                        <a href="aktivitas.php?aksi=edit&id=<?= $d['id_aktivitas'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="aktivitas.php?aksi=hapus&id=<?= $d['id_aktivitas'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                </div>

                <?php endif; ?>

            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; LAB IVSS 2023</span>
                </div>
            </div>
        </footer>
        
    </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Klik "Logout" di bawah jika Anda ingin mengakhiri sesi ini.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function(){
    $('#dataTable').DataTable();
});
</script>

</body>
</html>
