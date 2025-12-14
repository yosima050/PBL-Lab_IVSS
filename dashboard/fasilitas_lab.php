<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah role user adalah 'admin_berita'?
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_berita') {
    header("Location: login.php");
    exit;
}

// ============================================================
// 2. LOGIKA PROGRAM
// ============================================================

require_once __DIR__ . '/db.php';

$username = $_SESSION['nama_users'] ?? $_SESSION['nama'] ?? 'Admin';

// Set Path Upload (Keluar dari folder dashboard)
$uploadDir = __DIR__ . '/../uploads/';

// Buat folder jika belum ada
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// ---------------------------
// DELETE FASILITAS
// ---------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    
    // Hapus foto lama
    $stmt = $pdo->prepare("SELECT foto_fasilitas FROM fasilitas WHERE id_fasilitas = :id");
    $stmt->execute(['id' => $id]);
    $fotoLama = $stmt->fetchColumn();
    
    // Gunakan path absolut untuk unlink
    if ($fotoLama && file_exists($uploadDir . $fotoLama)) {
        unlink($uploadDir . $fotoLama);
    }

    $stmt = $pdo->prepare("DELETE FROM fasilitas WHERE id_fasilitas = :id");
    $stmt->execute(['id' => $id]);

    $_SESSION['message'] = "Fasilitas berhasil dihapus!";
    $_SESSION['msg_type'] = "success";

    header("Location: fasilitas_lab.php");
    exit;
}

// ---------------------------
// EDIT – UPDATE FASILITAS
// ---------------------------
if (isset($_POST['update'])) {
    $id        = $_POST['id_fasilitas'];
    $nama      = $_POST['nama_fasilitas'];
    $deskripsi = $_POST['deskripsi_fasilitas'];
    $foto      = $_POST['foto_lama']; // Default pakai foto lama

    // Jika ada upload foto baru
    if (!empty($_FILES['foto_fasilitas']['name'])) {
        $ext = pathinfo($_FILES['foto_fasilitas']['name'], PATHINFO_EXTENSION);
        $fotoBaru = time() . '_' . uniqid() . '.' . $ext;
        $tmp = $_FILES['foto_fasilitas']['tmp_name'];
        
        if (move_uploaded_file($tmp, $uploadDir . $fotoBaru)) {
            // Hapus foto lama jika ada dan beda file
            if ($foto && file_exists($uploadDir . $foto)) {
                unlink($uploadDir . $foto);
            }
            $foto = $fotoBaru;
        }
    }

    $stmt = $pdo->prepare("UPDATE fasilitas SET 
        nama_fasilitas = :nama,
        deskripsi_fasilitas = :deskripsi,
        foto_fasilitas = :foto
        WHERE id_fasilitas = :id");

    $stmt->execute([
        'nama'      => $nama,
        'deskripsi' => $deskripsi,
        'foto'      => $foto,
        'id'        => $id
    ]);

    $_SESSION['message'] = "Fasilitas berhasil diupdate!";
    $_SESSION['msg_type'] = "success";

    header("Location: fasilitas_lab.php");
    exit;
}

// ---------------------------
// TAMBAH – INSERT FASILITAS
// ---------------------------
if (isset($_POST['tambah'])) {
    $nama      = $_POST['nama_fasilitas'];
    $deskripsi = $_POST['deskripsi_fasilitas'];
    $foto      = null;

    if (!empty($_FILES['foto_fasilitas']['name'])) {
        $ext = pathinfo($_FILES['foto_fasilitas']['name'], PATHINFO_EXTENSION);
        $foto = time() . '_' . uniqid() . '.' . $ext;
        $tmp = $_FILES['foto_fasilitas']['tmp_name'];
        
        move_uploaded_file($tmp, $uploadDir . $foto);
    }
    
    $id_users = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO fasilitas (id_users, nama_fasilitas, deskripsi_fasilitas, foto_fasilitas) 
                           VALUES (:uid, :nama, :deskripsi, :foto)");

    $stmt->execute([
        'uid'       => $id_users,
        'nama'      => $nama,
        'deskripsi' => $deskripsi,
        'foto'      => $foto
    ]);

    $_SESSION['message'] = "Fasilitas berhasil ditambahkan!";
    $_SESSION['msg_type'] = "success";

    header("Location: fasilitas_lab.php");
    exit;
}

// Variabel untuk Sidebar
$role = $_SESSION['role'] ?? null;
$pendingCount = $waitingApproval = 0;

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Pending'");
    $stmt->execute();
    $pendingCount = (int) $stmt->fetchColumn();
} catch (Exception $e) { $pendingCount = 0; }

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Menunggu'");
    $stmt->execute();
    $waitingApproval = (int) $stmt->fetchColumn();
} catch (Exception $e) { $waitingApproval = 0; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Fasilitas Lab - LAB IVSS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

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
                <h1 class="h3 mb-4 text-gray-800">Fasilitas Lab</h1>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
                <?php endif; ?>

                <?php
                // --- HALAMAN EDIT ---
                if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
                    $id = $_GET['id'];
                    $stmt = $pdo->prepare("SELECT * FROM fasilitas WHERE id_fasilitas = :id");
                    $stmt->execute(['id' => $id]);
                    $f = $stmt->fetch();
                    
                    if ($f) {
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Edit Fasilitas</h6>
                        <a href="fasilitas_lab.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_fasilitas" value="<?= $f['id_fasilitas'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $f['foto_fasilitas'] ?>">

                            <div class="form-group">
                                <label>Nama Fasilitas</label>
                                <input type="text" name="nama_fasilitas" class="form-control" value="<?= htmlspecialchars($f['nama_fasilitas']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi_fasilitas" class="form-control" rows="5" required><?= htmlspecialchars($f['deskripsi_fasilitas']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Foto Saat Ini</label><br>
                                <?php if(!empty($f['foto_fasilitas'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($f['foto_fasilitas']) ?>" width="150" class="img-thumbnail mb-2">
                                <?php endif; ?>
                                <input type="file" name="foto_fasilitas" class="form-control-file">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
                            </div>

                            <button type="submit" name="update" class="btn btn-warning">Update</button>
                            <a href="fasilitas_lab.php" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>

                <?php
                    } else {
                        echo '<div class="alert alert-danger">Data tidak ditemukan.</div>';
                    }

                // --- HALAMAN TAMBAH ---
                } elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah') {
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Tambah Fasilitas</h6>
                        <a href="fasilitas_lab.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Nama Fasilitas</label>
                                <input type="text" name="nama_fasilitas" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi_fasilitas" class="form-control" rows="5" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Foto Fasilitas</label>
                                <input type="file" name="foto_fasilitas" class="form-control-file" required>
                            </div>

                            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                            <a href="fasilitas_lab.php" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>

                <?php
                // --- HALAMAN LIST (DEFAULT) ---
                } else {
                    $stmt = $pdo->query("SELECT * FROM fasilitas ORDER BY id_fasilitas DESC");
                    $fasilitas = $stmt->fetchAll();
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Data Fasilitas Lab</h6>
                        <a href="fasilitas_lab.php?aksi=tambah" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Fasilitas</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Foto</th>
                                        <th>Nama Fasilitas</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach ($fasilitas as $f): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?php if(!empty($f['foto_fasilitas'])): ?>
                                                <img src="../uploads/<?= htmlspecialchars($f['foto_fasilitas']) ?>" width="80" class="img-thumbnail">
                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($f['nama_fasilitas']) ?></td>
                                        <td><?= htmlspecialchars($f['deskripsi_fasilitas']) ?></td>
                                        <td>
                                            <a href="fasilitas_lab.php?aksi=edit&id=<?= $f['id_fasilitas'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="fasilitas_lab.php?aksi=hapus&id=<?= $f['id_fasilitas'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus fasilitas ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; LAB IVSS</span>
                </div>
            </div>
        </footer>

    </div>
</div>

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
<script src="js/sb-admin-2.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

</body>
</html>