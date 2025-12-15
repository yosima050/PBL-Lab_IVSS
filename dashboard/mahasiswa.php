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
        // Hapus hanya dari pendaftaran (sesuai logika awal)
        // Catatan: Idealnya hapus juga dari tabel users/mahasiswa jika perlu bersih total
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
   UPDATE (EDIT) - Data & Keaktifan
============================ */
if ($action === 'edit' && isset($_POST['id_pendaftaran'])) {
    $id_pendaftaran = $_POST['id_pendaftaran'];
    $id_mahasiswa   = $_POST['id_mahasiswa'];

    try {
        $pdo->beginTransaction();

        // 1. Update Data Diri di Tabel Pendaftaran
        $stmt1 = $pdo->prepare("UPDATE pendaftaran 
            SET nama_mahasiswa = :nama,
                nim = :nim,
                prodi = :prodi,
                nama_dosen = :dosen,
                email_mahasiswa = :email
            WHERE id_pendaftaran = :id");

        $stmt1->execute([
            'nama'  => $_POST['nama_mahasiswa'],
            'nim'   => $_POST['nim'],
            'prodi' => $_POST['prodi'],
            'dosen' => $_POST['nama_dosen'],
            'email' => $_POST['email'],
            'id'    => $id_pendaftaran
        ]);

        // 2. Update Status Keaktifan di Tabel Mahasiswa
        $stmt2 = $pdo->prepare("UPDATE mahasiswa 
            SET keaktifan_mahasiswa = :keaktifan 
            WHERE id_mahasiswa = :id_mhs");

        $stmt2->execute([
            'keaktifan' => $_POST['keaktifan_mahasiswa'],
            'id_mhs'    => $id_mahasiswa
        ]);

        $pdo->commit();
        $_SESSION['message'] = "Data dan status keaktifan berhasil diperbarui.";
        $_SESSION['msg_type'] = "success";

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Gagal update: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }

    header("Location: mahasiswa.php");
    exit;
}

/* ============================
   READ DATA (HANYA YANG DITERIMA)
============================ */
// Ambil Keyword Pencarian
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

try {
    // Query Join: Pendaftaran + Mahasiswa
    // Filter: status_mahasiswa = 'Diterima'
    $query = "SELECT *
          FROM view_mahasiswa
          WHERE nama_mahasiswa ILIKE :kwd
             OR nim ILIKE :kwd
          ORDER BY nama_mahasiswa ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['kwd' => $search_param]);
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
    <title>Data Mahasiswa - Lab IVSS</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">

<?php
// Sidebar Counters
$role = $_SESSION['role'] ?? null;
$pendingCount = 0; $waitingApproval = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Pending'");
    $stmt->execute(); $pendingCount = (int) $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Menunggu'");
    $stmt->execute(); $waitingApproval = (int) $stmt->fetchColumn();
} catch (Exception $e) {}

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
    <p class="mb-4">Daftar mahasiswa yang telah diterima menjadi anggota Lab IVSS.</p>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show">
            <?= $_SESSION['message'] ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Anggota Lab</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Prodi</th>
                            <th>Email</th>
                            <th>Dosen Pembimbing</th>
                            <th class="text-center">Keaktifan</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($mahasiswa as $m): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($m['nama_mahasiswa']) ?></td>
                            <td><?= htmlspecialchars($m['nim']) ?></td>
                            <td><?= htmlspecialchars($m['prodi']) ?></td>
                            <td><?= htmlspecialchars($m['email_mahasiswa'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($m['nama_dosen']) ?></td>
                            
                            <td class="text-center">
                                <?php 
                                    $statusAktif = $m['keaktifan_mahasiswa'] ?? 'Aktif';
                                    $badgeColor = ($statusAktif == 'Aktif') ? 'badge-success' : 'badge-secondary';
                                ?>
                                <span class="badge <?= $badgeColor ?>" style="font-size: 0.9em;">
                                    <?= htmlspecialchars($statusAktif) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" style="margin: 2px;"
                                    data-toggle="modal"
                                    data-target="#editModal<?= $m['id_pendaftaran'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <a href="?action=delete&id=<?= $m['id_pendaftaran'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus data mahasiswa ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?= $m['id_pendaftaran'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="?action=edit" method="POST">
                                    <input type="hidden" name="id_pendaftaran" value="<?= $m['id_pendaftaran'] ?>">
                                    <input type="hidden" name="id_mahasiswa" value="<?= $m['id_mahasiswa'] ?>">

                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Edit Data & Status Mahasiswa</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-primary">Status Keaktifan</label>
                                                <select name="keaktifan_mahasiswa" class="form-control">
                                                    <option value="Aktif" <?= ($m['keaktifan_mahasiswa'] ?? 'Aktif') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="Alumni" <?= ($m['keaktifan_mahasiswa'] ?? '') == 'Alumni' ? 'selected' : '' ?>>Alumni</option>
                                                </select>
                                                <small class="text-muted">Ubah ke 'Alumni' jika sudah lulus.</small>
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <label>Nama</label>
                                                <input type="text" name="nama_mahasiswa" class="form-control" value="<?= $m['nama_mahasiswa'] ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>NIM</label>
                                                <input type="text" name="nim" class="form-control" value="<?= $m['nim'] ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($m['email_mahasiswa'] ?? '') ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Prodi</label>
                                                <input type="text" name="prodi" class="form-control" value="<?= $m['prodi'] ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Dosen Pembimbing</label>
                                                <input type="text" name="nama_dosen" class="form-control" value="<?= $m['nama_dosen'] ?>" required>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Yakin ingin keluar?</h5>
        <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
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
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

</body>
</html>