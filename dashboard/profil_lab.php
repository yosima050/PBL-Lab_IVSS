<?php
session_start();
// --- VALIDASI LOGIN ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin_aktivitas yang boleh akses
if ($_SESSION['role'] !== 'admin_berita') {
    echo "<script>
            alert('AKSES DITOLAK! Halaman ini hanya untuk Admin berita.');
            window.location='dashboard.php';
          </script>";
    exit;
}

require_once __DIR__ . '/db.php';

$username = $_SESSION['nama_users'] ?? 'Admin';

// --- mulai: variabel untuk sidebar.php (role + badge counters) ---
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
// --- selesai: variabel untuk sidebar ---

// ------------------------
// AMBIL DATA PROFIL LAB DARI MATERIALIZED VIEW
// ------------------------
// Ambil dari Materialized View
$stmt = $pdo->query("SELECT * FROM mv_profil_lab LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika MV kosong → refresh dan ambil ulang
if (!$profil) {

    // Refresh MV agar mengambil data terbaru dari tabel profil_lab
    $pdo->query("REFRESH MATERIALIZED VIEW mv_profil_lab");

    // Ambil ulang setelah refresh
    $stmt = $pdo->query("SELECT * FROM mv_profil_lab LIMIT 1");
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

    header("Location: profil_lab.php");
    exit;
}

// ------------------------
// PROSES UPDATE DATA
// ------------------------
if (isset($_POST['update'])) {

    $visi   = trim($_POST['visi']);
    $misi   = trim($_POST['misi']);
    $narasi = trim($_POST['narasi']);

    // Validasi sederhana
    if ($visi == "" || $misi == "" || $narasi == "") {
        $_SESSION['msg'] = "Semua field wajib diisi!";
        $_SESSION['type'] = "danger";
        header("Location: profil_lab.php");
        exit;
    }

    // Update data
    $update = $pdo->prepare("
        UPDATE profil_lab SET 
            visi = :visi,
            misi = :misi,
            narasi = :narasi,
            updated_at = NOW()
        WHERE id_profil_lab = 1
    ");

    $update->execute([
        'visi' => $visi,
        'misi' => $misi,
        'narasi' => $narasi
    ]);

    $pdo->query("REFRESH MATERIALIZED VIEW mv_profil_lab");

    $_SESSION['msg'] = "Profil berhasil diperbarui!";
    $_SESSION['type'] = "success";

    header("Location: profil_lab.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Lab</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column"> 
        <div id="content">
            
            <!-- TOPBAR -->
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

                <h1 class="h3 mb-4 text-gray-800">Profil Lab</h1>
                <!-- ALERT -->
                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-<?= $_SESSION['type'] ?>">
                        <?= $_SESSION['msg'] ?>
                    </div>
                <?php unset($_SESSION['msg'], $_SESSION['type']); ?>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-body">

                    <form action="" method="POST">

                        <div class="form-group">
                            <label><strong>Visi:</strong></label>
                            <textarea name="visi" class="form-control" rows="3"><?= $profil['visi'] ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><strong>Misi:</strong></label>
                            <textarea name="misi" class="form-control" rows="4"><?= $profil['misi'] ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><strong>Narasi Profil Lab:</strong></label>
                            <textarea name="narasi" class="form-control" rows="5"><?= $profil['narasi'] ?></textarea>
                        </div>

                        <p class="text-muted">
                            Terakhir diperbarui: 
                            <strong><?= date('d M Y H:i', strtotime($profil['updated_at'])) ?></strong>
                        </p>

                        <button type="submit" name="update" class="btn btn-primary">Update Profil</button>
                    </form>

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
</div>

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

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function(){
    $('#dataTable').DataTable();
});
</script>

</body>
</html>
