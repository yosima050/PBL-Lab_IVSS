<?php
session_start();
require_once __DIR__ . '/db.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Ambil Role & Data User
$role = $_SESSION['role']; // Asumsi value: 'admin_sistem', 'admin_berita', 'ketua_lab'
$username = $_SESSION['nama_users'] ?? 'User';

// 3. QUERY DATA (Disesuaikan dengan Role)
// Kita hanya menjalankan query yang relevan dengan role yang login agar efisien

// --- DATA UNTUK ADMIN SISTEM ---
if ($role == 'admin_sistem') {
    // Pendaftar Pending (tabel: pendaftaran)
    $stmt = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Pending'");
    $pendingCount = $stmt->fetchColumn();

    // Total Anggota Aktif (tabel: mahasiswa)
    $stmt = $pdo->query("SELECT COUNT(*) FROM mahasiswa JOIN pendaftaran ON mahasiswa.id_pendaftaran = pendaftaran.id_pendaftaran WHERE pendaftaran.status_mahasiswa = 'Aktif'");
    $totalMembers = $stmt->fetchColumn();

    // Total Dosen (tabel: dosen)
    $stmt = $pdo->query("SELECT COUNT(*) FROM dosen");
    $totalDosen = $stmt->fetchColumn();

    // Total User Akun (tabel: users)
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmt->fetchColumn();
}

// --- DATA UNTUK ADMIN BERITA ---
if ($role == 'admin_berita') {
    // Total Berita
    $totalNews = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
    // Total Aktivitas
    $totalActivities = $pdo->query("SELECT COUNT(*) FROM aktivitas")->fetchColumn();
    // Total Fasilitas
    $totalFacilities = $pdo->query("SELECT COUNT(*) FROM fasilitas")->fetchColumn();
}

// --- DATA UNTUK KETUA LAB ---
if ($role == 'ketua_lab') {
    // Menunggu Persetujuan Ketua Lab (Status sudah diteruskan oleh Admin Sistem)
    // Asumsi: Admin Sistem mengubah status jadi 'Menunggu Ketua Lab' atau kolom 'diteruskan_oleh' terisi
    $stmt = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Menunggu Ketua Lab'"); 
    $waitingApproval = $stmt->fetchColumn();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>LAB IVSS - Dashboard</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="sidebar-brand-text mx-3">LAB IVSS</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item active">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider">

            <?php if ($role == 'admin_sistem') : ?>
                <div class="sidebar-heading">Administrasi</div>
                
                <li class="nav-item">
                    <a class="nav-link" href="pendaftaran.php">
                        <i class="fas fa-fw fa-user-plus"></i>
                        <span>Pendaftaran Baru</span>
                        <?php if($pendingCount > 0): ?>
                            <span class="badge badge-danger badge-counter"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseData" aria-expanded="true">
                        <i class="fas fa-fw fa-database"></i>
                        <span>Data Master</span>
                    </a>
                    <div id="collapseData" class="collapse" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="mahasiswa.php">Data Mahasiswa</a>
                            <a class="collapse-item" href="dosen.php">Data Dosen & Riset</a>
                            <a class="collapse-item" href="users.php">Manajemen User</a>
                        </div>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($role == 'admin_berita') : ?>
                <div class="sidebar-heading">Konten Publik</div>

                <li class="nav-item">
                    <a class="nav-link" href="berita.php">
                        <i class="fas fa-fw fa-newspaper"></i>
                        <span>Berita & Pengumuman</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="aktivitas.php">
                        <i class="fas fa-fw fa-camera"></i>
                        <span>Aktivitas & Galeri</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="fasilitas.php">
                        <i class="fas fa-fw fa-tools"></i>
                        <span>Fasilitas Lab</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role == 'ketua_lab') : ?>
                <div class="sidebar-heading">Persetujuan</div>

                <li class="nav-item">
                    <a class="nav-link" href="approval.php">
                        <i class="fas fa-fw fa-check-circle"></i>
                        <span>Approval Anggota</span>
                        <?php if($waitingApproval > 0): ?>
                            <span class="badge badge-danger badge-counter"><?= $waitingApproval ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
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
                                    Halo, <b><?= htmlspecialchars($username) ?></b> (<?= str_replace('_', ' ', strtoupper($role)) ?>)
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <?php if ($role == 'admin_sistem') : ?>
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendaftar Pending</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $pendingCount ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-user-clock fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Mahasiswa Aktif</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalMembers ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Dosen Peneliti</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalDosen ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>


                    <?php if ($role == 'admin_berita') : ?>
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Berita</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalNews ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-newspaper fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Aktivitas</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalActivities ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-camera fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="tambah_berita.php" class="btn btn-primary btn-icon-split btn-lg mb-3">
                                <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                                <span class="text">Tulis Berita Baru</span>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
                </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; LAB IVSS 2023</span>
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
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>