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

    // Hapus foto
    $stmt = $pdo->prepare("SELECT foto_aktivitas FROM aktivitas WHERE id_aktivitas = :id");
    $stmt->execute(['id' => $id]);
    $foto = $stmt->fetchColumn();

    if ($foto && file_exists($uploadDir . $foto)) {
        @unlink($uploadDir . $foto);
    }

    $stmt = $pdo->prepare("DELETE FROM aktivitas WHERE id_aktivitas = :id");
    $stmt->execute(['id' => $id]);

    $_SESSION['message'] = "Aktivitas berhasil dihapus!";
    $_SESSION['msg_type'] = "success";
    header("Location: aktivitas_galeri.php");
    exit;
}

// =======================================================
// UPDATE
// =======================================================
if (isset($_POST['update'])) {
    $id     = $_POST['id_aktivitas'];
    $judul  = $_POST['judul_aktivitas'];
    $isi    = $_POST['isi_aktivitas'];
    $mulai  = $_POST['tanggal_mulai_aktivitas'];
    $selesai = $_POST['tanggal_selesai_aktivitas'];
    $tag    = $_POST['tag_aktivitas'];

    // jika upload foto baru
    if (!empty($_FILES['foto_aktivitas']['name'])) {
        $foto = time() . "_" . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['foto_aktivitas']['name']));
        move_uploaded_file($_FILES['foto_aktivitas']['tmp_name'], $uploadDir . $foto);
    } else {
        $foto = $_POST['foto_lama'];
    }

    $stmt = $pdo->prepare("UPDATE aktivitas SET 
        judul_aktivitas = :judul,
        isi_aktivitas = :isi,
        tanggal_mulai_aktivitas = :mulai,
        tanggal_selesai_aktivitas = :selesai,
        tag_aktivitas = :tag,
        foto_aktivitas = :foto
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

    $_SESSION['message'] = "Aktivitas berhasil diupdate!";
    $_SESSION['msg_type'] = "warning";
    header("Location: aktivitas_galeri.php");
    exit;
}

// =======================================================
// INSERT
// =======================================================
if (isset($_POST['tambah'])) {
    $judul   = $_POST['judul_aktivitas'] ?? '';
    $isi     = $_POST['isi_aktivitas'] ?? '';
    $mulai   = $_POST['tanggal_mulai_aktivitas'] ?? null;
    $selesai = $_POST['tanggal_selesai_aktivitas'] ?? null;
    $tag     = $_POST['tag_aktivitas'] ?? '';

    // sanitasi nama file dan simpan ke folder uploads
    $foto = null;
    if (!empty($_FILES['foto_aktivitas']['name'])) {
        $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['foto_aktivitas']['name']));
        $foto = time() . "_" . $safeName;
        $target = $uploadDir . $foto;
        if (!move_uploaded_file($_FILES['foto_aktivitas']['tmp_name'], $target)) {
            $_SESSION['message'] = "Upload gagal: tidak dapat menyimpan file.";
            $_SESSION['msg_type'] = "danger";
            header("Location: aktivitas.php");
            exit;
        }
    }

    // ambil daftar kolom yang tersedia di tabel aktivitas
    try {
        $colStmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'aktivitas' AND table_schema = CURRENT_SCHEMA()");
        $colStmt->execute();
        $columns = $colStmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        // fallback untuk MySQL (jika driver bukan Postgres)
        $colStmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'aktivitas'");
        $colStmt->execute();
        $columns = $colStmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // mapping data yang mungkin akan disimpan
    $possible = [
        'judul_aktivitas' => $judul,
        'isi_aktivitas' => $isi,
        'tanggal_mulai_aktivitas' => $mulai,
        'tanggal_selesai_aktivitas' => $selesai,
        'tag_aktivitas' => $tag,
        'foto_aktivitas' => $foto,
        'created_at_aktivitas' => date('Y-m-d H:i:s')
    ];

    // build insert sesuai kolom yang ada
    $insertCols = [];
    $placeholders = [];
    $params = [];

    foreach ($possible as $col => $val) {
        if (in_array($col, $columns)) {
            $insertCols[] = $col;
            $placeholders[] = ':' . $col;
            // gunakan null jika nilai kosong dan kolom ada
            $params[$col] = $val;
        }
    }

    if (empty($insertCols)) {
        $_SESSION['message'] = "Konfigurasi tabel aktivitas tidak mendukung insert otomatis.";
        $_SESSION['msg_type'] = "danger";
        header("Location: aktivitas.php");
        exit;
    }

    $sql = "INSERT INTO aktivitas (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $_SESSION['message'] = "Aktivitas berhasil ditambahkan!";
    $_SESSION['msg_type'] = "success";
    header("Location: aktivitas_galeri.php");
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

    <?php include 'sidebar.php'; ?>

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
                ?>

                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6>Edit Aktivitas</h6>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_aktivitas" value="<?= $d['id_aktivitas'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $d['foto_aktivitas'] ?>">

                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="judul_aktivitas" class="form-control" value="<?= $d['judul_aktivitas'] ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Isi Aktivitas</label>
                                <textarea name="isi_aktivitas" class="form-control" rows="4"><?= $d['isi_aktivitas'] ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai_aktivitas" class="form-control" value="<?= $d['tanggal_mulai_aktivitas'] ?>">
                            </div>

                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai_aktivitas" class="form-control" value="<?= $d['tanggal_selesai_aktivitas'] ?>">
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
                                <img src="../uploads/<?= $d['foto_aktivitas'] ?>" width="140"><br><br>
                                <input type="file" name="foto_aktivitas">
                            </div>

                            <button type="submit" name="update" class="btn btn-warning">Update</button>
                            <a href="aktivitas_galeri.php" class="btn btn-secondary">Batal</a>
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
                                <input type="file" name="foto_aktivitas" required>
                            </div>

                            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                            <a href="aktivitas_galeri.php" class="btn btn-secondary">Batal</a>

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
                                    <td><img src="../uploads/<?= $d['foto_aktivitas'] ?>" width="70"></td>
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
