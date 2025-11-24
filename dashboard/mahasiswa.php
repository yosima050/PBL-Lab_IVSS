<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login & Role
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['admin_sistem'])) {
    echo "Akses Ditolak!";
    exit;
}

$username = $_SESSION['nama_users'] ?? 'User';

$action = $_GET['action'] ?? '';

/* ============================
   DELETE
============================ */
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM pendaftaran WHERE id_pendaftaran = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['message'] = "Data mahasiswa berhasil dihapus.";
        $_SESSION['msg_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Gagal menghapus: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
    header("Location: mahasiswa.php");
    exit;
}

/* ============================
   UPDATE (EDIT)
============================ */
if ($action === 'edit' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("UPDATE pendaftaran 
            SET nama_mahasiswa = :nama,
                nim = :nim,
                prodi = :prodi,
                nama_dosen = :dosen,
                status_mahasiswa = :status
            WHERE id_pendaftaran = :id");

        $stmt->execute([
            'nama' => $_POST['nama_mahasiswa'],
            'nim' => $_POST['nim'],
            'prodi' => $_POST['prodi'],
            'dosen' => $_POST['nama_dosen'],
            'status' => $_POST['status_mahasiswa'],
            'id' => $id
        ]);

        $_SESSION['message'] = "Data berhasil diperbarui.";
        $_SESSION['msg_type'] = "success";

    } catch (PDOException $e) {
        $_SESSION['message'] = "Gagal update: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }

    header("Location: mahasiswa.php");
    exit;
}

/* ============================
   READ ALL DATA
============================ */
try {
    $stmt = $pdo->query("SELECT * FROM pendaftaran ORDER BY id_pendaftaran DESC");
    $mahasiswa = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Mahasiswa</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">

<?php
$role = $_SESSION['role'] ?? null;
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

include __DIR__ . '/sidebar.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                <span class="mr-2 text-gray-600 small">Halo, <b><?= htmlspecialchars($username) ?></b></span>
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
    <h1 class="h3 mb-2 text-gray-800">Data Mahasiswa</h1>
    <p class="mb-4">Pengelolaan data mahasiswa yang telah terdaftar.</p>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Prodi</th>
                            <th>Dosen Pembimbing</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($mahasiswa as $m): ?>
                        <tr>
                            <td>#<?= $m['id_pendaftaran'] ?></td>
                            <td><?= htmlspecialchars($m['nama_mahasiswa']) ?></td>
                            <td><?= htmlspecialchars($m['nim']) ?></td>
                            <td><?= htmlspecialchars($m['prodi']) ?></td>
                            <td><?= htmlspecialchars($m['nama_dosen']) ?></td>
                            <td><span class="badge badge-info"><?= $m['status_mahasiswa'] ?></span></td>

                            <td class="text-center">

                                <!-- Tombol Edit -->
                                <button class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#editModal<?= $m['id_pendaftaran'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Tombol Delete -->
                                <a href="?action=delete&id=<?= $m['id_pendaftaran'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>

                            </td>
                        </tr>

                        <!-- ============================
                             MODAL EDIT
                        ============================= -->
                        <div class="modal fade" id="editModal<?= $m['id_pendaftaran'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="?action=edit" method="POST">
                                    <input type="hidden" name="id" value="<?= $m['id_pendaftaran'] ?>">

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Data Mahasiswa</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama</label>
                                                <input type="text" name="nama_mahasiswa" class="form-control"
                                                    value="<?= $m['nama_mahasiswa'] ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label>NIM</label>
                                                <input type="text" name="nim" class="form-control"
                                                    value="<?= $m['nim'] ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Prodi</label>
                                                <input type="text" name="prodi" class="form-control"
                                                    value="<?= $m['prodi'] ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Dosen Pembimbing</label>
                                                <input type="text" name="nama_dosen" class="form-control"
                                                    value="<?= $m['nama_dosen'] ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status_mahasiswa" class="form-control">
                                                    <option <?= $m['status_mahasiswa'] == "Pending" ? "selected" : "" ?>>Pending</option>
                                                    <option <?= $m['status_mahasiswa'] == "Diterima" ? "selected" : "" ?>>Diterima</option>
                                                    <option <?= $m['status_mahasiswa'] == "Ditolak" ? "selected" : "" ?>>Ditolak</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
</div>

<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center">
            <span>Copyright &copy; LAB IVSS</span>
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

<!-- Logout confirmation modal (sama seperti file dashboard.php) -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Yakin ingin keluar?</h5>
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

</body>
</html>
