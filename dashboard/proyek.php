<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Role yang boleh akses
if (!in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    echo "Akses Ditolak!";
    exit;
}

$username = $_SESSION['nama_users'] ?? 'User';

// Ambil data proyek dosen
try {
    $stmt = $pdo->query("
        SELECT 
            p.id_proyek,
            p.judul_proyek,
            p.deskripsi_proyek,
            p.tahun_proyek,
            p.tipe_proyek,
            d.id_dosen,
            d.nama_dosen
        FROM proyek p
        JOIN detail_proyek_dosen dpd ON p.id_proyek = dpd.id_proyek
        JOIN dosen d ON dpd.id_dosen = d.id_dosen
        ORDER BY p.id_proyek DESC
    ");
    $proyekDosen = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error Dosen: " . $e->getMessage());
}

// Ambil data proyek mahasiswa
try {
    $stmt2 = $pdo->query("
        SELECT 
            dpm.id_proyek,
            p.judul_proyek,
            p.deskripsi_proyek,
            p.tahun_proyek,
            p.tipe_proyek,
            m.id_mahasiswa,
            u.nama_users AS nama_mahasiswa,
            dpm.nama_penulis_proyek_mahasiswa,
            dpm.tanggal_mulai_proyek_mahasiswa,
            dpm.tanggal_selesai_proyek_mahasiswa,
            dpm.kategori_proyek_mahasiswa,
            dpm.lokasi_proyek_mahasiswa
        FROM detail_proyek_mahasiswa dpm
        JOIN proyek p ON dpm.id_proyek = p.id_proyek
        JOIN mahasiswa m ON dpm.id_mahasiswa = m.id_mahasiswa
        JOIN users u ON m.id_users = u.id_users
        ORDER BY dpm.id_proyek DESC;
    ");
    $proyekMahasiswa = $stmt2->fetchAll();
} catch (PDOException $e) {
    die("Error Mahasiswa: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Proyek</title>

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
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown">
                                <span class="mr-2 text-gray-600 small">
                                    Halo, <b><?= htmlspecialchars($username) ?></b>
                                </span>
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

                    <h1 class="h3 mb-2 text-gray-800">Data Proyek</h1>
                    <p class="mb-4">Kelola data proyek dosen dan mahasiswa.</p>

                    <!-- Tombol Tambah -->
                    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#createProyekDosen">
                        <i class="fas fa-plus"></i> Tambah Proyek Dosen
                    </button>

                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createProyekMahasiswa">
                        <i class="fas fa-plus"></i> Tambah Proyek Mahasiswa
                    </button>

                    <!-- ===================================================== -->
                    <!-- =============== TABEL PROYEK DOSEN ================== -->
                    <!-- ===================================================== -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">Data Proyek Dosen</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableDosen">
                                    <thead>
                                        <tr>
                                            <th>ID Proyek</th>
                                            <th>Dosen</th>
                                            <th>Judul</th>
                                            <th>Tahun</th>
                                            <th>Tipe</th>
                                            <th>Deskripsi</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($proyekDosen as $d): ?>
                                        <tr>
                                            <td><?= $d['id_proyek'] ?></td>
                                            <td><?= htmlspecialchars($d['nama_dosen']) ?></td>
                                            <td><?= htmlspecialchars($d['judul_proyek']) ?></td>
                                            <td><?= $d['tahun_proyek'] ?></td>
                                            <td><?= htmlspecialchars($d['tipe_proyek']) ?></td>
                                            <td><?= htmlspecialchars($d['deskripsi_proyek']) ?></td>

                                            <td class="text-center">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#editProyekDosen<?= $d['id_proyek'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Tombol Delete -->
                                                <a href="process_proyek.php?delete=<?= $d['id_proyek'] ?>"
                                                onclick="return confirm('Hapus proyek dosen ini?')"
                                                class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ===================================================== -->
                    <!-- ============ TABEL PROYEK MAHASISWA ================= -->
                    <!-- ===================================================== -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">Data Proyek Mahasiswa</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableMahasiswa">
                                    <thead>
                                        <tr>
                                            <th>ID Proyek</th>
                                            <th>Mahasiswa</th>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Lokasi</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Penulis</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <?php foreach ($proyekMahasiswa as $p): ?> 
                                        <tr> 
                                            <td><?= $p['id_proyek'] ?></td> 
                                            <td><?= htmlspecialchars($p['nama_mahasiswa']) ?></td> 
                                            <td><?= htmlspecialchars($p['judul_proyek']) ?></td> 
                                            <td><?= htmlspecialchars($p['kategori_proyek_mahasiswa']) ?></td> 
                                            <td><?= htmlspecialchars($p['lokasi_proyek_mahasiswa']) ?></td> 
                                            <td><?= $p['tanggal_mulai_proyek_mahasiswa'] ?></td> 
                                            <td><?= $p['tanggal_selesai_proyek_mahasiswa'] ?></td> 
                                            <td><?= htmlspecialchars($p['nama_penulis_proyek_mahasiswa']) ?></td>

                                            <td class="text-center">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#editProyekMahasiswa<?= $p['id_proyek'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Tombol Delete -->
                                                <a href="process_proyek.php?delete=<?= $p['id_proyek'] ?>"
                                                    onclick="return confirm('Hapus proyek mahasiswa ini?')"
                                                    class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr> 
                                        <?php endforeach ?> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="text-center">
                            <span>Copyright © LAB IVSS</span>
                        </div>
                    </div>
                </footer>

            </div>
        </div>
    </div>

    <!-- ================================================= -->
    <!-- ============ MODAL TAMBAH PROYEK DOSEN ========== -->
    <!-- ================================================= -->
    <div class="modal fade" id="createProyekDosen" tabindex="-1">
        <div class="modal-dialog">
            <form action="process_proyek.php" method="POST">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Proyek Dosen</h5>
                        <button class="close" data-dismiss="modal"><span>×</span></button>
                    </div>

                    <div class="modal-body">
                        <label>ID Dosen:</label>
                        <input type="number" name="id_dosen" class="form-control mb-3" required>

                        <label>Judul Proyek:</label>
                        <input type="text" name="judul" class="form-control mb-3" required>

                        <label>Deskripsi:</label>
                        <textarea name="deskripsi" class="form-control mb-3" required></textarea>

                        <label>Tahun Proyek:</label>
                        <input type="number" name="tahun" class="form-control mb-3" required>

                        <label>Tipe Proyek:</label>
                        <input type="text" name="tipe" class="form-control mb-3" required>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="create_dosen" class="btn btn-success">Simpan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- ============ MODAL EDIT PROYEK DOSEN ================= -->
    <?php foreach ($proyekDosen as $d): ?>
    <div class="modal fade" id="editProyekDosen<?= $d['id_proyek'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <form action="process_proyek.php" method="POST">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Proyek Dosen</h5>
                        <button class="close" data-dismiss="modal"><span>×</span></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="edit_id_proyek" value="<?= $d['id_proyek'] ?>">

                        <label>Dosen (ID):</label>
                        <input type="number" name="edit_id_dosen"
                            value="<?= $d['id_dosen'] ?>"
                            class="form-control mb-3" required>

                        <label>Judul Proyek:</label>
                        <input type="text" name="edit_judul"
                            value="<?= htmlspecialchars($d['judul_proyek']) ?>"
                            class="form-control mb-3" required>

                        <label>Deskripsi:</label>
                        <textarea name="edit_deskripsi" class="form-control mb-3" required><?= htmlspecialchars($d['deskripsi_proyek']) ?></textarea>

                        <label>Tahun Proyek:</label>
                        <input type="number" name="edit_tahun"
                            value="<?= $d['tahun_proyek'] ?>"
                            class="form-control mb-3" required>

                        <label>Tipe Proyek:</label>
                        <input type="text" name="edit_tipe"
                            value="<?= $d['tipe_proyek'] ?>"
                            class="form-control mb-3" required>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="update_dosen" class="btn btn-warning">Update</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <?php endforeach ?>


    <!-- ============ MODAL TAMBAH PROYEK MAHASISWA ================= -->
    <div class="modal fade" id="createProyekMahasiswa" tabindex="-1">
        <div class="modal-dialog">
            <form action="process_proyek.php" method="POST">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Proyek Mahasiswa</h5>
                        <button class="close" data-dismiss="modal"><span>×</span></button>
                    </div>

                    <div class="modal-body">
                        <label>ID Mahasiswa:</label>
                        <input type="number" name="id_mahasiswa" class="form-control mb-3" required>

                        <label>Judul Proyek:</label>
                        <input type="text" name="judul" class="form-control mb-3" required>

                        <label>Deskripsi:</label>
                        <textarea name="deskripsi" class="form-control mb-3" required></textarea>

                        <label>Tahun Proyek:</label>
                        <input type="number" name="tahun" class="form-control mb-3" required>

                        <label>Tipe Proyek:</label>
                        <input type="text" name="tipe" class="form-control mb-3" required>

                        <label>Tanggal Mulai:</label>
                        <input type="date" name="tgl_mulai" class="form-control mb-3" required>

                        <label>Tanggal Selesai:</label>
                        <input type="date" name="tgl_selesai" class="form-control mb-3" required>

                        <label>Nama Penulis:</label>
                        <input type="text" name="nama_penulis" class="form-control mb-3" required>

                        <label>Kategori:</label>
                        <input type="text" name="kategori" class="form-control mb-3" required>

                        <label>Lokasi:</label>
                        <input type="text" name="lokasi" class="form-control mb-3" required>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="create_mahasiswa" class="btn btn-primary">Simpan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- ============ MODAL EDIT PROYEK MAHASISWA ================= -->
    <?php foreach ($proyekMahasiswa as $p): ?>
        <div class="modal fade" id="editProyekMahasiswa<?= $p['id_proyek'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <form action="process_proyek.php" method="POST">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Edit Proyek Mahasiswa</h5>
                            <button class="close" data-dismiss="modal"><span>×</span></button>
                        </div>

                        <div class="modal-body">

                            <input type="hidden" name="edit_id_proyek_mhs" value="<?= $p['id_proyek'] ?>">

                            <label>ID Mahasiswa:</label>
                            <input type="number" name="edit_id_mahasiswa"
                                value="<?= $p['id_mahasiswa'] ?>"
                                class="form-control mb-3" required>

                            <label>Judul Proyek:</label>
                            <input type="text" name="edit_judul_mhs"
                                value="<?= htmlspecialchars($p['judul_proyek']) ?>"
                                class="form-control mb-3" required>

                            <label>Deskripsi:</label>
                            <textarea name="edit_deskripsi_mhs" class="form-control mb-3" required><?= htmlspecialchars($p['deskripsi_proyek']) ?></textarea>

                            <label>Tahun Proyek:</label>
                            <input type="number" name="edit_tahun_mhs"
                                value="<?= $p['tahun_proyek'] ?>"
                                class="form-control mb-3" required>

                            <label>Tipe Proyek:</label>
                            <input type="text" name="edit_tipe_mhs"
                                value="<?= $p['tipe_proyek'] ?>"
                                class="form-control mb-3" required>

                            <label>Tanggal Mulai:</label>
                            <input type="date" name="edit_tgl_mulai"
                                value="<?= $p['tanggal_mulai_proyek_mahasiswa'] ?>"
                                class="form-control mb-3" required>

                            <label>Tanggal Selesai:</label>
                            <input type="date" name="edit_tgl_selesai"
                                value="<?= $p['tanggal_selesai_proyek_mahasiswa'] ?>"
                                class="form-control mb-3" required>

                            <label>Nama Penulis:</label>
                            <input type="text" name="edit_nama_penulis_mhs"
                                value="<?= htmlspecialchars($p['nama_penulis_proyek_mahasiswa']) ?>"
                                class="form-control mb-3" required>

                            <label>Kategori:</label>
                            <input type="text" name="edit_kategori_mhs"
                                value="<?= htmlspecialchars($p['kategori_proyek_mahasiswa']) ?>"
                                class="form-control mb-3" required>

                            <label>Lokasi:</label>
                            <input type="text" name="edit_lokasi_mhs"
                                value="<?= htmlspecialchars($p['lokasi_proyek_mahasiswa']) ?>"
                                class="form-control mb-3" required>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" name="update_mahasiswa" class="btn btn-warning">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach ?>


    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tableDosen').DataTable();
            $('#tableMahasiswa').DataTable();
        });
    </script>

</body>
</html>
