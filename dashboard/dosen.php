<?php 
session_start();
require_once __DIR__ . '/db.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['admin_sistem'])) {
    echo "Akses Ditolak!";
    exit;
}

$username = $_SESSION['nama_users'] ?? 'User';

<<<<<<< HEAD
=======
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

>>>>>>> 90301ecd3d451330be25094abe264ab394e9b779
/* ============================
   READ DATA DOSEN
============================ */
try {
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
    <title>Data Dosen</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">

<?php include __DIR__ . '/sidebar.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
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
    <h1 class="h3 mb-2 text-gray-800">Data Dosen</h1>
    <p class="mb-4">Daftar dosen beserta profil lengkap.</p>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Bidang Riset</th>
                            <th>Jabatan</th>
                            <th>Prodi</th>
                            <th>NIDN</th>
                            <th>Email</th>
                            <th>LinkedIn</th>
                            <th>Google Scholar</th>
                            <th>Sinta</th>
                            <th>Pendidikan</th>
                            <th>Sertifikasi</th>
                            <th>Mata Kuliah</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($dosen as $d): ?>
                        <tr>

                            <!-- FOTO -->
                            <td class="text-center">
                                <?php 
                                $path = "../Asset/" . ($d['foto_dosen'] ?? '');
                                if (!empty($d['foto_dosen']) && file_exists($path)): ?>
                                    <img src="<?= $path ?>" width="60" class="rounded">
                                <?php else: ?>
                                    <img src="img/no_image.png" width="60" class="rounded">
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($d['nama_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['nip']) ?></td>
                            <td><?= htmlspecialchars($d['bidang_riset']) ?></td>
                            <td><?= htmlspecialchars($d['jabatan_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['prodi_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['nidn_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['email_dosen']) ?></td>

                            <td><a href="<?= $d['link_linkedin'] ?>" target="_blank">LinkedIn</a></td>
                            <td><a href="<?= $d['link_google_scholar'] ?>" target="_blank">Scholar</a></td>
                            <td><a href="<?= $d['link_sinta'] ?>" target="_blank">Sinta</a></td>

                            <td><?= nl2br(htmlspecialchars($d['pendidikan_dosen'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($d['sertifikasi_dosen'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($d['mata_kuliah_dosen'])) ?></td>

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
            <span>&copy; LAB IVSS</span>
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

<!-- Modal Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
  <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title">Yakin ingin keluar?</h5>
          <button class="close" data-dismiss="modal"><span>×</span></button>
      </div>
      <div class="modal-body">Klik "Logout" untuk keluar.</div>
      <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <a class="btn btn-primary" href="logout.php">Logout</a>
      </div>
  </div></div>
</div>

</body>
</html>