<?php
session_start();
<<<<<<< HEAD
require '../dashboard/db.php';

// Ambil username dari session
$username = $_SESSION['nama'] ?? 'Admin';
=======

// ============================================================
// 1. VALIDASI KEAMANAN (Cek Login & Role)
// ============================================================

// Cek apakah user sudah login?
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah role user adalah 'admin_berita'?
// Jika bukan (misal: admin_sistem atau ketua_lab mencoba akses), tendang ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_berita') {
    header("Location: login.php");
    exit;
}

// ============================================================
// 2. LOGIKA PROGRAM
// ============================================================

// Sesuaikan path ini. Jika file ini ada di folder dashboard, gunakan __DIR__ . '/db.php'
// Kode asli Anda: require '../dashboard/db.php'; 
// Saya ganti ke yang lebih aman relatif terhadap file ini:
require_once __DIR__ . '/db.php';

// Ambil username dari session
$username = $_SESSION['nama_users'] ?? $_SESSION['nama'] ?? 'Admin';
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1

// ---------------------------
// DELETE FASILITAS
// ---------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
<<<<<<< HEAD
=======
    
    // (Opsional) Hapus foto lama dari folder
    $stmt = $pdo->prepare("SELECT foto_fasilitas FROM fasilitas WHERE id_fasilitas = :id");
    $stmt->execute(['id' => $id]);
    $fotoLama = $stmt->fetchColumn();
    if ($fotoLama && file_exists("../uploads/" . $fotoLama)) {
        unlink("../uploads/" . $fotoLama);
    }

>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
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
<<<<<<< HEAD
    $id      = $_POST['id_fasilitas'];
    $nama    = $_POST['nama_fasilitas'];
    $deskripsi = $_POST['deskripsi_fasilitas'];

    if (!empty($_FILES['foto_fasilitas']['name'])) {
        $foto = $_FILES['foto_fasilitas']['name'];
=======
    $id        = $_POST['id_fasilitas'];
    $nama      = $_POST['nama_fasilitas'];
    $deskripsi = $_POST['deskripsi_fasilitas'];

    if (!empty($_FILES['foto_fasilitas']['name'])) {
        $foto = time() . '_' . $_FILES['foto_fasilitas']['name']; // Tambah time() agar unik
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
        $tmp  = $_FILES['foto_fasilitas']['tmp_name'];
        move_uploaded_file($tmp, "../uploads/".$foto);
    } else {
        $foto = $_POST['foto_lama'];
    }

    $stmt = $pdo->prepare("UPDATE fasilitas SET 
        nama_fasilitas = :nama,
        deskripsi_fasilitas = :deskripsi,
        foto_fasilitas = :foto
        WHERE id_fasilitas = :id");

    $stmt->execute([
<<<<<<< HEAD
        'nama' => $nama,
        'deskripsi' => $deskripsi,
        'foto' => $foto,
        'id'   => $id
=======
        'nama'      => $nama,
        'deskripsi' => $deskripsi,
        'foto'      => $foto,
        'id'        => $id
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
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
<<<<<<< HEAD
    $nama       = $_POST['nama_fasilitas'];
    $deskripsi  = $_POST['deskripsi_fasilitas'];

    $foto = $_FILES['foto_fasilitas']['name'];
    $tmp  = $_FILES['foto_fasilitas']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);

    $stmt = $pdo->prepare("INSERT INTO fasilitas (nama_fasilitas, deskripsi_fasilitas, foto_fasilitas) VALUES (:nama, :deskripsi, :foto)");
=======
    $nama      = $_POST['nama_fasilitas'];
    $deskripsi = $_POST['deskripsi_fasilitas'];

    $foto = time() . '_' . $_FILES['foto_fasilitas']['name']; // Tambah time() agar unik
    $tmp  = $_FILES['foto_fasilitas']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);
    
    // Ambil ID User yang login (untuk audit trail, jika kolom id_users ada di tabel fasilitas)
    $id_users = $_SESSION['user_id'];

    // Sesuaikan query INSERT. Jika tabel fasilitas punya kolom 'id_users', tambahkan.
    // Di sini saya asumsikan struktur tabel sesuai kode lama Anda.
    $stmt = $pdo->prepare("INSERT INTO fasilitas (nama_fasilitas, deskripsi_fasilitas, foto_fasilitas, id_users) VALUES (:nama, :deskripsi, :foto, :uid)");
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1

    $stmt->execute([
        'nama'      => $nama,
        'deskripsi' => $deskripsi,
<<<<<<< HEAD
        'foto'      => $foto
=======
        'foto'      => $foto,
        'uid'       => $id_users
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
    ]);

    $_SESSION['message'] = "Fasilitas berhasil ditambahkan!";
    $_SESSION['msg_type'] = "success";

    header("Location: fasilitas_lab.php");
    exit;
}
<<<<<<< HEAD
=======

// Variabel untuk Sidebar
$role = $_SESSION['role'] ?? null;
$pendingCount = $waitingApproval = 0;

// Hitung badge (Opsional untuk sidebar)
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
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
?>

<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< HEAD
=======
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
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

            <!-- Topbar -->
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
            <!-- End Topbar -->

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Fasilitas Lab</h1>

                <?php if (isset($_SESSION['message'])): ?>
<<<<<<< HEAD
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?>"><?= $_SESSION['message'] ?></div>
=======
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                    <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
                <?php endif; ?>

                <?php
<<<<<<< HEAD
                // Halaman Edit
=======
                // --- HALAMAN EDIT ---
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
                    $id = $_GET['id'];
                    $stmt = $pdo->prepare("SELECT * FROM fasilitas WHERE id_fasilitas = :id");
                    $stmt->execute(['id' => $id]);
                    $f = $stmt->fetch();
<<<<<<< HEAD
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Edit Fasilitas</h6></div>
=======
                    
                    if ($f) {
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Edit Fasilitas</h6>
                        <a href="fasilitas_lab.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_fasilitas" value="<?= $f['id_fasilitas'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $f['foto_fasilitas'] ?>">

<<<<<<< HEAD
                            <label>Nama Fasilitas</label>
                            <input type="text" name="nama_fasilitas" class="form-control" value="<?= $f['nama_fasilitas'] ?>">

                            <label>Deskripsi</label>
                            <textarea name="deskripsi_fasilitas" class="form-control" rows="5"><?= $f['deskripsi_fasilitas'] ?></textarea>

                            <label>Foto</label><br>
                            <img src="../uploads/<?= $f['foto_fasilitas'] ?>" width="120">
                            <input type="file" name="foto_fasilitas" class="form-control mt-2">

                            <button name="update" class="btn btn-warning mt-3">Update</button>
                            <a href="fasilitas_lab.php" class="btn btn-secondary mt-3">Kembali</a>
=======
                            <div class="form-group">
                                <label>Nama Fasilitas</label>
                                <input type="text" name="nama_fasilitas" class="form-control" value="<?= htmlspecialchars($f['nama_fasilitas']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi_fasilitas" class="form-control" rows="5" required="<?= htmlspecialchars($f['deskripsi_fasilitas']) ?>"><?= htmlspecialchars($f['deskripsi_fasilitas']) ?></textarea>
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
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                        </form>
                    </div>
                </div>

                <?php
<<<<<<< HEAD
                // Halaman Tambah
                } elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah') {
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Tambah Fasilitas</h6></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <label>Nama Fasilitas</label>
                            <input type="text" name="nama_fasilitas" class="form-control" required>

                            <label>Deskripsi</label>
                            <textarea name="deskripsi_fasilitas" class="form-control" rows="5"></textarea>

                            <label>Foto Fasilitas</label>
                            <input type="file" name="foto_fasilitas" class="form-control" required>

                            <button name="tambah" class="btn btn-primary mt-3">Simpan</button>
                            <a href="fasilitas_lab.php" class="btn btn-secondary mt-3">Kembali</a>
=======
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
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                        </form>
                    </div>
                </div>

                <?php
<<<<<<< HEAD
                // Halaman list
=======
                // --- HALAMAN LIST (DEFAULT) ---
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                } else {
                    $stmt = $pdo->query("SELECT * FROM fasilitas ORDER BY id_fasilitas DESC");
                    $fasilitas = $stmt->fetchAll();
                ?>
                <div class="card shadow mb-4">
<<<<<<< HEAD
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Data Fasilitas Lab</h6>
                        <a href="fasilitas_lab.php?aksi=tambah" class="btn btn-primary btn-sm">+ Tambah Fasilitas</a>
                    </div>
                    <div class="card-body">
                        <table id="dataTable" class="table table-bordered">
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
                                    <td><img src="../uploads/<?= $f['foto_fasilitas'] ?>" width="70"></td>
                                    <td><?= $f['nama_fasilitas'] ?></td>
                                    <td><?= $f['deskripsi_fasilitas'] ?></td>
                                    <td>
                                        <a href="fasilitas_lab.php?aksi=edit&id=<?= $f['id_fasilitas'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="fasilitas_lab.php?aksi=hapus&id=<?= $f['id_fasilitas'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus fasilitas ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
=======
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
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                    </div>
                </div>
                <?php } ?>

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

<<<<<<< HEAD
=======
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

>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<<<<<<< HEAD
=======
<script src="js/sb-admin-2.min.js"></script>

>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
