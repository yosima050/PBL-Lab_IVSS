<?php
session_start();
require_once __DIR__ . '/db.php';

// 1. Cek Login & Role
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'ketua_lab' && $_SESSION['role'] !== 'admin_sistem') {
    echo "Akses Ditolak!";
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['nama_users'] ?? 'User';

// --- TAMBAHAN: LOGIKA BADGE COUNTER (Agar Sidebar Muncul Angkanya) ---
$pendingCount = 0;
$waitingApproval = 0;

try {
    // Hitung Pendaftar Baru (Pending)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Pending'");
    $stmt->execute();
    $pendingCount = (int)$stmt->fetchColumn();
} catch (Exception $e) { $pendingCount = 0; }

try {
    // Hitung Menunggu Validasi (Menunggu)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Menunggu'");
    $stmt->execute();
    $waitingApproval = (int)$stmt->fetchColumn();
} catch (Exception $e) { $waitingApproval = 0; }
// ----------------------------------------------------------------------


// 2. Query Data Pendaftar (Status: Menunggu)
$statusFilter = 'Menunggu'; 

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
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        
        <?php include 'sidebar.php'; ?>

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

                    <h1 class="h3 mb-2 text-gray-800">Persetujuan Anggota Baru</h1>
                    <p class="mb-4">Berikut adalah daftar mahasiswa yang mengajukan pendaftaran anggota Lab IVSS.</p>

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

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Menunggu Persetujuan (<?= count($pendaftarList) ?>)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th> <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Prodi</th>
                                            <th>Dosen Pembimbing</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1; // Inisialisasi nomor
                                        foreach ($pendaftarList as $row): 
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td> <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                                            <td><?= htmlspecialchars($row['nim']) ?></td>
                                            <td><?= htmlspecialchars($row['prodi']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_dosen']) ?></td>
                                            <td>
                                                <span class="badge badge-warning"><?= htmlspecialchars($row['status_mahasiswa']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <form action="approval_process.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?= $row['id_pendaftaran'] ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-success btn-sm mr-1" onclick="return confirm('Yakin ingin menyetujui mahasiswa ini? Akun user akan dibuat otomatis.')">
                                                            <i class="fas fa-check"></i> Setuju
                                                        </button>
                                                    </form>

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
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
</body>
</html>