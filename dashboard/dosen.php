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

/* ============================
   READ DATA DOSEN
============================ */
try {
    $stmt = $pdo->query("SELECT * FROM mv_dosen ORDER BY id_dosen ASC");
    $dosen = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

include __DIR__ . '/sidebar.php';

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

    <!-- FLASH MESSAGES -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <!-- DATA DOSEN -->
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
                                $path = $d['foto_dosen'];
                                $realPath = __DIR__ . '/' . $path;

                                if (!empty($path) && file_exists($realPath)):
                                    echo '<img src="' . $path . '" width="60" class="rounded">';
                                else:
                                    echo '<img src="img/no_image.png" width="60" class="rounded">';
                                endif;
                                ?>
                            </td>

                            <td><?= htmlspecialchars($d['nama_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['nip']) ?></td>
                            <td><?= htmlspecialchars($d['bidang_riset']) ?></td>
                            <td><?= htmlspecialchars($d['jabatan_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['prodi_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['nidn_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['email_dosen']) ?></td>

                            <td><a href="<?= htmlspecialchars($d['link_linkedin']) ?>" target="_blank">LinkedIn</a></td>
                            <td><a href="<?= htmlspecialchars($d['link_google_scholar']) ?>" target="_blank">Scholar</a></td>
                            <td><a href="<?= htmlspecialchars($d['link_sinta']) ?>" target="_blank">Sinta</a></td>

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
      </form>
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
  </div>
</div>
  
</div>



</body>
</html>