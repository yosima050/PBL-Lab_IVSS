<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login & Role
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya Admin Sistem yang boleh akses CRUD pendaftaran
if ($_SESSION['role'] !== 'admin_sistem') {
    echo "Akses Ditolak!";
    exit;
}

$username = $_SESSION['nama_users'] ?? 'User';

// CRUD Operations
$action = $_GET['action'] ?? '';

// UPDATE
if ($action === 'update' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $sql = "UPDATE pendaftaran 
                    SET id_users = :id_users, nim = :nim, nama_mahasiswa = :nama_mahasiswa, prodi = :prodi,
                        email_mahasiswa = :email_mahasiswa, status_mahasiswa = :status_mahasiswa,
                        nama_dosen = :nama_dosen, password = :password
                    WHERE id_pendaftaran = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id_users' => $_POST['id_users'],
                'nim' => $_POST['nim'],
                'nama_mahasiswa' => $_POST['nama_mahasiswa'],
                'prodi' => $_POST['prodi'],
                'email_mahasiswa' => $_POST['email_mahasiswa'],
                'status_mahasiswa' => $_POST['status_mahasiswa'],
                'nama_dosen' => $_POST['nama_dosen'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'id' => $id
            ]);

            $_SESSION['message'] = "Data berhasil diperbarui.";
            $_SESSION['msg_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Gagal memperbarui data: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
        }
        header("Location: pendaftaran_admin.php");
        exit;
    }
}

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM pendaftaran WHERE id_pendaftaran = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['message'] = "Data berhasil dihapus.";
        $_SESSION['msg_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Gagal menghapus: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
    header("Location: pendaftaran_admin.php");
    exit;
}

// READ
try {
    $stmt = $pdo->query("SELECT * FROM pendaftaran ORDER BY id_pendaftaran DESC");
    $pendaftar = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pendaftaran - Admin Sistem</title>
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
    <h1 class="h3 mb-2 text-gray-800">Kelola Pendaftaran Anggota</h1>
    <p class="mb-4">Admin Sistem dapat mengedit, menghapus, dan meneruskan pendaftaran ke Ketua Lab.</p>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>"> <?= $_SESSION['message'] ?> </div>
        <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <!-- Tombol tambah DIHAPUS -->

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
                        <?php foreach ($pendaftar as $p): ?>
                        <tr>
                            <td>#<?= $p['id_pendaftaran'] ?></td>
                            <td><?= htmlspecialchars($p['nama_mahasiswa']) ?></td>
                            <td><?= htmlspecialchars($p['nim']) ?></td>
                            <td><?= htmlspecialchars($p['prodi']) ?></td>
                            <td><?= htmlspecialchars($p['nama_dosen']) ?></td>
                            <td><span class="badge badge-info"> <?= $p['status_mahasiswa'] ?> </span></td>
                            <td class="text-center">
                                <a href="pendaftaran_edit.php?id=<?= $p['id_pendaftaran'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?= $p['id_pendaftaran'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                                <a href="pendaftaran_forward.php?id=<?= $p['id_pendaftaran'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-share"></i> Teruskan</a>
                            </td>
                        </tr>
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
</body>
</html>
