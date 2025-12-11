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
                        status_mahasiswa = :status_mahasiswa, nama_dosen = :nama_dosen
                    WHERE id_pendaftaran = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id_users' => $_POST['id_users'],
                'nim' => $_POST['nim'],
                'nama_mahasiswa' => $_POST['nama_mahasiswa'],
                'prodi' => $_POST['prodi'],
                'status_mahasiswa' => $_POST['status_mahasiswa'],
                'nama_dosen' => $_POST['nama_dosen'],
                'id' => $id
            ]);

            $_SESSION['message'] = "Data berhasil diperbarui.";
            $_SESSION['msg_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Gagal memperbarui data: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
        }
        header("Location: pendaftaran.php");
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
    header("Location: pendaftaran.php");
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

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Prodi</th>
                            <th>Dosen Pembimbing</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        foreach ($pendaftar as $p): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p['nama_mahasiswa']) ?></td>
                            <td><?= htmlspecialchars($p['nim']) ?></td>
                            <td><?= htmlspecialchars($p['prodi']) ?></td>
                            <td><?= htmlspecialchars($p['nama_dosen']) ?></td>
                            <td>
                                <?php 
                                    $status = $p['status_mahasiswa'];
                                    $badgeClass = [
                                        'Pending'   => 'badge-warning',
                                        'Menunggu'  => 'badge-info',
                                        'Diterima'  => 'badge-success',
                                        'Ditolak'   => 'badge-danger'
                                    ];
                                ?>
                                <span class="badge <?= $badgeClass[$status] ?? 'badge-secondary' ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </td>
                            <td class="text-center">

                                <button class="btn btn-warning btn-sm btn-edit" style="margin: 1.5px;"
                                    data-id="<?= $p['id_pendaftaran'] ?>"
                                    data-id_users="<?= $p['id_users'] ?>"
                                    data-nama="<?= htmlspecialchars($p['nama_mahasiswa']) ?>"
                                    data-nim="<?= htmlspecialchars($p['nim']) ?>"
                                    data-prodi="<?= htmlspecialchars($p['prodi']) ?>"
                                    data-status="<?= htmlspecialchars($p['status_mahasiswa']) ?>"
                                    data-dosen="<?= htmlspecialchars($p['nama_dosen']) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <a href="?action=delete&id=<?= $p['id_pendaftaran'] ?>" 
                                class="btn btn-danger btn-sm" 
                                onclick="return confirm('Hapus data ini?')">
                                <i class="fas fa-trash"></i>
                                </a>

                                <button class="btn btn-primary btn-sm btn-forward"
                                        data-id="<?= $p['id_pendaftaran'] ?>"
                                        data-toggle="modal"
                                        data-target="#forwardModal">
                                    <i class="fas fa-share"></i> Teruskan
                                </button>

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

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Pendaftaran</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <form method="POST" id="editForm">
        <div class="modal-body">

          <input type="hidden" name="id_users" id="edit_id_users">

          <div class="form-group">
            <label>Nama Mahasiswa</label>
            <input type="text" name="nama_mahasiswa" id="edit_nama" class="form-control">
          </div>

          <div class="form-group">
            <label>NIM</label>
            <input type="text" name="nim" id="edit_nim" class="form-control">
          </div>

          <div class="form-group">
            <label>Prodi</label>
            <input type="text" name="prodi" id="edit_prodi" class="form-control">
          </div>

          <div class="form-group">
            <label>Dosen Pembimbing</label>
            <input type="text" name="nama_dosen" id="edit_dosen" class="form-control">
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="status_mahasiswa" id="edit_status" class="form-control">
              <option value="Pending">Pending</option>
              <option value="Menunggu">Menunggu</option>
              <option value="Diterima">Diterima</option>
              <option value="Ditolak">Ditolak</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        </div>

      </form>

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

<!-- Forward modal markup -->
<div class="modal fade" id="forwardModal" tabindex="-1" role="dialog" aria-labelledby="forwardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="forwardModalLabel">Konfirmasi Teruskan</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin meneruskan data pendaftaran <b id="pendaftaran-id-display"></b> ke Ketua Lab untuk persetujuan?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <a id="btn-confirm-forward" href="pendaftaran.php" class="btn btn-primary">Ya, Teruskan</a>
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

<!-- Forward modal wiring script (after jQuery is loaded) -->
<script>
    $(document).ready(function() {
        // Saat tombol class 'btn-forward' diklik
        $('.btn-forward').on('click', function() {
            // Ambil ID dari atribut data-id
            var id = $(this).data('id');
            
            // Tampilkan ID di dalam teks modal (opsional, biar keren)
            $('#pendaftaran-id-display').text('#' + id);
            
            // Update link href pada tombol "Ya, Teruskan"
            var url = 'pendaftaran_forward.php?id=' + id + '&confirm=yes';
            $('#btn-confirm-forward').attr('href', url);
        });
    });
</script>

<script>
$(document).ready(function() {

    $(".btn-edit").on("click", function() {

        const id = $(this).data("id");

        $("#edit_id_users").val($(this).data("id_users"));
        $("#edit_nama").val($(this).data("nama"));
        $("#edit_nim").val($(this).data("nim"));
        $("#edit_prodi").val($(this).data("prodi"));
        $("#edit_dosen").val($(this).data("dosen"));
        $("#edit_status").val($(this).data("status"));
        
        // Set action form ke URL UPDATE
        $("#editForm").attr("action", "pendaftaran.php?action=update&id=" + id);

        $("#editModal").modal("show");
    });

});
</script>
</body>
</html>
