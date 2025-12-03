<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin_berita') {
    echo "<script>alert('AKSES DITOLAK!'); window.location='dashboard.php';</script>";
    exit;
}

require_once __DIR__ . '/db.php';
$username = $_SESSION['nama_users'] ?? 'Admin';

$role = $_SESSION['role'] ?? null;
$pendingCount = 0;
$waitingApproval = 0;

$stmt = $pdo->query("SELECT * FROM profil_lab ORDER BY id_profil_lab DESC LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profil) {
    $profil = ['id_profil_lab' => 0, 'visi' => '', 'misi' => '', 'narasi' => ''];
}

$id_target = $profil['id_profil_lab']; 


if (isset($_POST['update'])) {

    $visi   = trim($_POST['visi']);
    $misi   = trim($_POST['misi']);
    $narasi = trim($_POST['narasi']);
    
    $id_to_update = $_POST['id_profil_lab'];

    if ($visi == "" || $misi == "" || $narasi == "") {
        $_SESSION['msg'] = "Semua field wajib diisi!";
        $_SESSION['type'] = "danger";
        header("Location: profil_lab.php");
        exit;
    }

    try {
        $update = $pdo->prepare("
            UPDATE profil_lab SET 
                visi = :visi,
                misi = :misi,
                narasi = :narasi,
                updated_at = NOW()
            WHERE id_profil_lab = :id
        ");

        $update->execute([
            'visi'   => $visi,
            'misi'   => $misi,
            'narasi' => $narasi,
            'id'     => $id_to_update
        ]);

        if ($update->rowCount() > 0) {
            $pdo->query("REFRESH MATERIALIZED VIEW mv_profil_lab");
            $_SESSION['msg'] = "Profil berhasil diperbarui!";
            $_SESSION['type'] = "success";
        } else {
            $_SESSION['msg'] = "Data disimpan, tetapi tidak ada perubahan yang terdeteksi (atau ID salah).";
            $_SESSION['type'] = "warning";
        }

    } catch (PDOException $e) {
        $_SESSION['msg'] = "Gagal Update: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }

    header("Location: profil_lab.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Lab - Admin</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column"> 
        <div id="content">
            
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><span class="nav-link text-gray-600">Halo, <b><?= htmlspecialchars($username) ?></b></span></li>
                </ul>
            </nav>

            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">Edit Profil Lab</h1>

                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-<?= $_SESSION['type'] ?> alert-dismissible fade show">
                        <?= $_SESSION['msg'] ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php unset($_SESSION['msg'], $_SESSION['type']); ?>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Profil (ID: <?= $id_target ?>)</h6>
                    </div>
                    <div class="card-body">

                        <form action="" method="POST">
                            <input type="hidden" name="id_profil_lab" value="<?= $id_target ?>">

                            <div class="form-group">
                                <label><strong>Visi:</strong></label>
                                <textarea name="visi" class="form-control" rows="3"><?= htmlspecialchars($profil['visi']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label><strong>Misi:</strong></label>
                                <small class="text-muted">(Gunakan Enter untuk poin baru)</small>
                                <textarea name="misi" class="form-control" rows="6"><?= htmlspecialchars($profil['misi']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label><strong>Narasi Profil Lab:</strong></label>
                                <textarea name="narasi" class="form-control" rows="5"><?= htmlspecialchars($profil['narasi']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <p class="text-muted small">
                                    Terakhir diperbarui: 
                                    <strong><?= !empty($profil['updated_at']) ? date('d M Y H:i', strtotime($profil['updated_at'])) : '-' ?></strong>
                                </p>
                            </div>

                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
        
        <footer class="sticky-footer bg-white">
            <div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; LAB IVSS</span></div></div>
        </footer>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>