<?php
session_start();
require '../dashboard/db.php';

// Ambil username dari session
$username = $_SESSION['nama'] ?? 'Admin';

// ---------------------------
// DELETE FASILITAS
// ---------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
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
    $id      = $_POST['id_fasilitas'];
    $nama    = $_POST['nama_fasilitas'];
    $deskripsi = $_POST['deskripsi_fasilitas'];

    if (!empty($_FILES['foto_fasilitas']['name'])) {
        $foto = $_FILES['foto_fasilitas']['name'];
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
        'nama' => $nama,
        'deskripsi' => $deskripsi,
        'foto' => $foto,
        'id'   => $id
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
    $nama       = $_POST['nama_fasilitas'];
    $deskripsi  = $_POST['deskripsi_fasilitas'];

    $foto = $_FILES['foto_fasilitas']['name'];
    $tmp  = $_FILES['foto_fasilitas']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);

    $stmt = $pdo->prepare("INSERT INTO fasilitas (nama_fasilitas, deskripsi_fasilitas, foto_fasilitas) VALUES (:nama, :deskripsi, :foto)");

    $stmt->execute([
        'nama'      => $nama,
        'deskripsi' => $deskripsi,
        'foto'      => $foto
    ]);

    $_SESSION['message'] = "Fasilitas berhasil ditambahkan!";
    $_SESSION['msg_type'] = "success";

    header("Location: fasilitas_lab.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?>"><?= $_SESSION['message'] ?></div>
                    <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
                <?php endif; ?>

                <?php
                // Halaman Edit
                if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
                    $id = $_GET['id'];
                    $stmt = $pdo->prepare("SELECT * FROM fasilitas WHERE id_fasilitas = :id");
                    $stmt->execute(['id' => $id]);
                    $f = $stmt->fetch();
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Edit Fasilitas</h6></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_fasilitas" value="<?= $f['id_fasilitas'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $f['foto_fasilitas'] ?>">

                            <label>Nama Fasilitas</label>
                            <input type="text" name="nama_fasilitas" class="form-control" value="<?= $f['nama_fasilitas'] ?>">

                            <label>Deskripsi</label>
                            <textarea name="deskripsi_fasilitas" class="form-control" rows="5"><?= $f['deskripsi_fasilitas'] ?></textarea>

                            <label>Foto</label><br>
                            <img src="../uploads/<?= $f['foto_fasilitas'] ?>" width="120">
                            <input type="file" name="foto_fasilitas" class="form-control mt-2">

                            <button name="update" class="btn btn-warning mt-3">Update</button>
                            <a href="fasilitas_lab.php" class="btn btn-secondary mt-3">Kembali</a>
                        </form>
                    </div>
                </div>

                <?php
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
                        </form>
                    </div>
                </div>

                <?php
                // Halaman list
                } else {
                    $stmt = $pdo->query("SELECT * FROM fasilitas ORDER BY id_fasilitas DESC");
                    $fasilitas = $stmt->fetchAll();
                ?>
                <div class="card shadow mb-4">
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

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

</body>
</html>
