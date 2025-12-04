<?php 
session_start();
require_once __DIR__ . '/db.php';

// 1. CEK LOGIN & ROLE
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_sistem') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['nama_users'] ?? 'User';
$uploadDir = __DIR__ . '/../uploads/';

// ============================
// LOGIKA HAPUS (DELETE)
// ============================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    
    // Ambil foto lama untuk dihapus
    $stmt = $pdo->prepare("SELECT foto_dosen FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id]);
    $fotoLama = $stmt->fetchColumn();

    if ($fotoLama && file_exists($uploadDir . $fotoLama)) {
        @unlink($uploadDir . $fotoLama);
    }

    // Hapus data
    $stmt = $pdo->prepare("DELETE FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id]);

    // Refresh Materialized View (Agar Frontend sinkron)
    $pdo->query("REFRESH MATERIALIZED VIEW mv_dosen");

    $_SESSION['flash'] = "Data dosen berhasil dihapus!";
    header("Location: dosen.php");
    exit;
}

/* ============================
   READ DATA DOSEN (TABEL UTAMA)
============================ */
try {
    // Gunakan tabel 'dosen' (bukan mv_dosen) untuk CRUD Dashboard
    $stmt = $pdo->query("SELECT * FROM dosen ORDER BY id_dosen ASC");
    $dosen = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Dosen - Admin Sistem</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">

    <?php
    // supply role + counters used by sidebar, then include centralized sidebar.php
    $role = $_SESSION['role'] ?? null;
    $pendingCount = 0;
    $waitingApproval = 0;
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

    include __DIR__ . '/sidebar.php';
    ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                            <span class="mr-2 text-gray-600 small">Halo, <b><?= htmlspecialchars($username) ?></b></span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid">

                <?php if (!empty($_SESSION['flash'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <h1 class="h3 mb-2 text-gray-800">Manajemen Data Dosen</h1>
                <p class="mb-4">Kelola data dosen peneliti di laboratorium IVSS.</p>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Dosen</h6>
                        <a href="dosen_tambah.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Dosen
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="80">Foto</th>
                                        <th>Nama</th>
                                        <th>NIP / NIDN</th>
                                        <th>Bidang Riset</th>
                                        <th>Jabatan</th>
                                        <th class="text-center" width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dosen as $d): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php 
                                            // Tampilkan foto dari folder uploads
                                            $foto = !empty($d['foto_dosen']) ? $d['foto_dosen'] : 'default_profile.jpg';
                                            ?>
                                            <img src="../uploads/<?= htmlspecialchars($foto) ?>" width="60" class="rounded" onerror="this.src='../Asset/default_profile.jpg'">
                                        </td>
                                        
                                        <td><?= htmlspecialchars($d['nama_dosen']) ?></td>
                                        
                                        <td>
                                            <small class="d-block text-muted">NIP: <?= htmlspecialchars($d['nip']) ?></small>
                                            <small class="d-block text-muted">NIDN: <?= htmlspecialchars($d['nidn_dosen']) ?></small>
                                        </td>
                                        
                                        <td><?= htmlspecialchars($d['bidang_riset']) ?></td>
                                        <td><?= htmlspecialchars($d['jabatan_dosen']) ?></td>
                                        
                                        <td class="text-center">
                                            <a href="dosen_edit.php?id=<?= $d['id_dosen'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="dosen.php?aksi=hapus&id=<?= $d['id_dosen'] ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus data dosen ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
                <div class="copyright text-center my-auto">
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