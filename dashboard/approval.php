<?php
session_start();
require_once __DIR__ . '/db.php';

// 1. Cek Login & Role
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Pastikan hanya Ketua Lab (atau Admin Sistem jika perlu) yang bisa akses
// Sesuai UC-07, Admin Sistem meneruskan, Ketua Lab yang approve.
// Jika Anda ingin Admin Sistem juga bisa lihat (readonly), sesuaikan logika ini.
if ($_SESSION['role'] !== 'ketua_lab' && $_SESSION['role'] !== 'admin_sistem') {
    // Redirect atau tampilkan error akses ditolak
    echo "Akses Ditolak!";
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['nama_users'] ?? 'User';

// 2. Query Data Pendaftar
// Ambil pendaftar yang statusnya 'Menunggu Ketua Lab' (atau 'Pending' jika admin sistem langsung forward)
// Mari asumsikan statusnya 'Pending' atau 'Menunggu Ketua Lab' sesuai alur Anda.
// Sesuai diskusi sebelumnya, status awal 'Pending'. Admin Sistem meneruskan -> 'Menunggu Ketua Lab'.
// Jadi Ketua Lab hanya melihat yang 'Menunggu Ketua Lab'.

$statusFilter = 'Menunggu Ketua Lab'; 
// CATATAN: Jika Admin Sistem belum mengubah status, dan Ketua Lab ingin lihat semua 'Pending', ubah jadi 'Pending'.
// Untuk saat ini kita gunakan 'Pending' agar Anda bisa melihat data registrasi yang baru masuk untuk dites.
$statusFilter = 'Pending'; // <-- UBAH INI jika sudah ada alur Admin Sistem

try {
    $sql = "SELECT * FROM pendaftaran WHERE status_mahasiswa = :status ORDER BY id_pendaftaran DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $statusFilter]);
    $pendaftarList = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Approval Anggota - Lab IVSS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for tables -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        
        <!-- Include Sidebar -->
        <?php include 'sidebar.php'; ?>

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

                <!-- Page Content -->
                <div class="container-fluid">

                    <h1 class="h3 mb-2 text-gray-800">Persetujuan Anggota Baru</h1>
                    <p class="mb-4">Berikut adalah daftar mahasiswa yang mengajukan pendaftaran anggota Lab IVSS.</p>

                    <!-- Tampilkan Pesan Sukses/Error -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show" role="alert">
                            <?= $_SESSION['message'] ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php 
                        unset($_SESSION['message']); 
                        unset($_SESSION['msg_type']);
                        ?>
                    <?php endif; ?>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Menunggu Persetujuan (<?= count($pendaftarList) ?>)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Prodi</th>
                                            <th>Dosen Pembimbing</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendaftarList as $row): ?>
                                        <tr>
                                            <!-- Asumsi id_pendaftaran auto increment, bisa jadi penanda urutan/waktu kasar jika tidak ada created_at -->
                                            <td>#<?= $row['id_pendaftaran'] ?></td> 
                                            <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                                            <td><?= htmlspecialchars($row['nim']) ?></td>
                                            <td><?= htmlspecialchars($row['prodi']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_dosen']) ?></td>
                                            <td>
                                                <span class="badge badge-warning"><?= htmlspecialchars($row['status_mahasiswa']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <!-- Tombol Aksi -->
                                                <div class="btn-group" role="group">
                                                    <!-- Tombol Setuju -->
                                                    <form action="approval_process.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?= $row['id_pendaftaran'] ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-success btn-sm mr-1" onclick="return confirm('Yakin ingin menyetujui mahasiswa ini? Akun user akan dibuat otomatis.')">
                                                            <i class="fas fa-check"></i> Setuju
                                                        </button>
                                                    </form>

                                                    <!-- Tombol Tolak -->
                                                    <form action="approval_process.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?= $row['id_pendaftaran'] ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menolak pendaftaran ini?')">
                                                            <i class="fas fa-times"></i> Tolak
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        
                                        <?php if(count($pendaftarList) == 0): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada pendaftaran baru yang menunggu persetujuan.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

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

    <!-- Logout Modal (Sama seperti dashboard) -->
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

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <!-- Page level plugins (DataTables) -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
</body>
</html>