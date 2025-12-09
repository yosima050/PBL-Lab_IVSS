<?php 
session_start();
require_once __DIR__ . '/db.php';

// CEK LOGIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_sistem') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['nama_users'] ?? 'User';
$uploadDir = __DIR__ . '/../uploads/';

// ============================
// LOGIKA HAPUS
// ============================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT foto_dosen FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id]);
    $fotoLama = $stmt->fetchColumn();

    if ($fotoLama && file_exists($uploadDir . $fotoLama)) {
        @unlink($uploadDir . $fotoLama);
    }

    $stmt = $pdo->prepare("DELETE FROM dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id]);

    $pdo->query("REFRESH MATERIALIZED VIEW mv_dosen");

    $_SESSION['flash'] = "Data dosen berhasil dihapus!";
    header("Location: dosen.php");
    exit;
}

// READ DATA UTAMA
$stmt = $pdo->query("SELECT * FROM dosen ORDER BY id_dosen ASC");
$dosen = $stmt->fetchAll();
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
$role = $_SESSION['role'] ?? null;
$pendingCount = 0;
$waitingApproval = 0;

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Pending'");
    $stmt->execute();
    $pendingCount = (int)$stmt->fetchColumn();
} catch (Exception $e) {}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Menunggu'");
    $stmt->execute();
    $waitingApproval = (int)$stmt->fetchColumn();
} catch (Exception $e) {}

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

                    <!-- TOMBOL MODAL -->
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahDosen">
                        <i class="fas fa-plus"></i> Tambah Dosen
                    </button>
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
                                        $foto = !empty($d['foto_dosen']) ? '../uploads/' . $d['foto_dosen'] : '../Asset/default_profile.jpg';
                                        ?>
                                        <img src="<?= htmlspecialchars($foto) ?>" width="60" class="rounded"
                                             onerror="this.src='../Asset/default_profile.jpg'">
                                    </td>

                                    <td><?= htmlspecialchars($d['nama_dosen']) ?></td>

                                    <td>
                                        <small class="d-block text-muted">NIP: <?= htmlspecialchars($d['nip']) ?></small>
                                        <small class="d-block text-muted">NIDN: <?= htmlspecialchars($d['nidn_dosen']) ?></small>
                                    </td>

                                    <td><?= htmlspecialchars($d['bidang_riset']) ?></td>
                                    <td><?= htmlspecialchars($d['jabatan_dosen']) ?></td>

                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm btnEditDosen" style="margin: 1.5px;"
                                                data-id="<?= $d['id_dosen'] ?>"
                                                data-nama="<?= htmlspecialchars($d['nama_dosen']) ?>"
                                                data-nip="<?= htmlspecialchars($d['nip']) ?>"
                                                data-nidn="<?= htmlspecialchars($d['nidn_dosen']) ?>"
                                                data-bidang="<?= htmlspecialchars($d['bidang_riset']) ?>"
                                                data-jabatan="<?= htmlspecialchars($d['jabatan_dosen']) ?>"
                                                data-foto="<?= htmlspecialchars($d['foto_dosen']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <a href="dosen.php?aksi=hapus&id=<?= $d['id_dosen'] ?>"
                                           onclick="return confirm('Yakin ingin menghapus?')"
                                           class="btn btn-danger btn-sm">
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

<!-- ============================= -->
<!-- MODAL TAMBAH DOSEN -->
<!-- ============================= -->

<div class="modal fade" id="modalTambahDosen" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Dosen Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                <span>&times;</span>
                </button>
            </div>

            <form action="dosen_process.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">

                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_dosen" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>NIP:</label>
                    <input type="text" name="nip" class="form-control">
                </div>

                <div class="form-group">
                    <label>NIDN:</label>
                    <input type="text" name="nidn_dosen" class="form-control">
                </div>

                <div class="form-group">
                    <label>Bidang Riset:</label>
                    <input type="text" name="bidang_riset" class="form-control">
                </div>

                <div class="form-group">
                    <label>Jabatan:</label>
                    <input type="text" name="jabatan_dosen" class="form-control">
                </div>

                <div class="form-group">
                    <label>Foto Dosen:</label>
                    <input type="file" name="foto_dosen" class="form-control-file">
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

            </form>
        </div>
    </div>
</div>

<!-- ============================= -->
<!-- MODAL EDIT DOSEN -->
<!-- ============================= -->

<div class="modal fade" id="modalEditDosen" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Data Mahasiswa</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                <span>&times;</span>
                </button>
            </div>

            <form action="dosen_process.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">

                    <input type="hidden" name="id_dosen" id="edit_id">

                    <div class="form-group">
                        <label>Nama Lengkap:</label>
                        <input type="text" name="nama_dosen" id="edit_nama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>NIP:</label>
                        <input type="text" name="nip" id="edit_nip" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>NIDN:</label>
                        <input type="text" name="nidn_dosen" id="edit_nidn" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Bidang Riset:</label>
                        <input type="text" name="bidang_riset" id="edit_bidang" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Jabatan:</label>
                        <input type="text" name="jabatan_dosen" id="edit_jabatan" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Foto Sekarang:</label><br>
                        <img id="edit_foto_preview" src="" width="100" class="rounded mb-2">
                        <br>
                        <label>Ganti Foto (opsional):</label>
                        <input type="file" name="foto_dosen" class="form-control-file">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>

            </form>
        </div>
    </div>
</div>


<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

<script>
$(document).on('click', '.btnEditDosen', function() {
    $('#edit_id').val($(this).data('id'));
    $('#edit_nama').val($(this).data('nama'));
    $('#edit_nip').val($(this).data('nip'));
    $('#edit_nidn').val($(this).data('nidn'));
    $('#edit_bidang').val($(this).data('bidang'));
    $('#edit_jabatan').val($(this).data('jabatan'));

    let foto = $(this).data('foto');
    if (foto) {
        $('#edit_foto_preview').attr('src', foto);
    } else {
        $('#edit_foto_preview').attr('src', '../Asset/default_profile.jpg');
    }

    $('#modalEditDosen').modal('show');
});
</script>


</body>
</html>
