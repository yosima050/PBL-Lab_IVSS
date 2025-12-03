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

// --- Badge Counter Sidebar ---
$role = $_SESSION['role'] ?? null;
$pendingCount = 0;
$waitingApproval = 0;

// Get data publikasi
try {
    $stmt = $pdo->query("
        SELECT 
            p.id_publikasi,
            p.id_users,
            u.nama_users,
            p.judul_publikasi,
            p.tahun_publikasi,
            p.link_publikasi
        FROM publikasi p
        JOIN users u ON p.id_users = u.id_users
        ORDER BY p.id_publikasi DESC
    ");
    $publikasi = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Publikasi</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <?php include __DIR__ . '/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Topbar -->
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

                    <h1 class="h3 mb-2 text-gray-800">Data Publikasi</h1>
                    <p class="mb-4">Kelola data publikasi penelitian.</p>

                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">
                        <i class="fas fa-plus"></i> Tambah Publikasi
                    </button>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Penulis (User)</th>
                                            <th>Judul</th>
                                            <th>Tahun</th>
                                            <th>Link</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($publikasi as $p): ?>
                                        <tr>
                                            <td><?= $p['id_publikasi'] ?></td>
                                            <td><?= htmlspecialchars($p['nama_users']) ?></td>
                                            <td><?= htmlspecialchars($p['judul_publikasi']) ?></td>
                                            <td><?= htmlspecialchars($p['tahun_publikasi']) ?></td>
                                            <td>
                                                <a href="<?= htmlspecialchars($p['link_publikasi']) ?>" target="_blank">
                                                    Kunjungi
                                                </a>
                                            </td>

                                            <td class="text-center">

                                                <!-- Edit -->
                                                <button 
                                                    class="btn btn-warning btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#editModal<?= $p['id_publikasi'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Delete -->
                                                <a href="process_publikasi.php?delete=<?= $p['id_publikasi'] ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Hapus publikasi ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>

                                            </td>
                                        </tr>

                                        <!-- ===== MODAL EDIT ===== -->
                                        <div class="modal fade" id="editModal<?= $p['id_publikasi'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="process_publikasi.php" method="POST">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Publikasi</h5>
                                                            <button class="close" data-dismiss="modal"><span>×</span></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_publikasi" value="<?= $p['id_publikasi'] ?>">

                                                            <label>Judul:</label>
                                                            <input type="text" name="judul_publikasi" class="form-control mb-3"
                                                                value="<?= $p['judul_publikasi'] ?>" required>

                                                            <label>Tahun:</label>
                                                            <input type="number" name="tahun_publikasi" class="form-control mb-3"
                                                                value="<?= $p['tahun_publikasi'] ?>" required>

                                                            <label>Link Publikasi:</label>
                                                            <input type="text" name="link_publikasi" class="form-control"
                                                                value="<?= $p['link_publikasi'] ?>" required>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" name="update" class="btn btn-primary">Simpan</button>
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

                <!-- FOOTER -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="text-center">
                            <span>Copyright &copy; LAB IVSS</span>
                        </div>
                    </div>
                </footer>

            </div>
        </div>
    </div>

    <!-- ===== MODAL CREATE ===== -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="process_publikasi.php" method="POST">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Publikasi</h5>
                        <button class="close" data-dismiss="modal"><span>×</span></button>
                    </div>

                    <div class="modal-body">

                        <label>ID User (Penulis):</label>
                        <input type="number" name="id_users" class="form-control mb-3" required>

                        <label>Judul Publikasi:</label>
                        <input type="text" name="judul_publikasi" class="form-control mb-3" required>

                        <label>Tahun Publikasi:</label>
                        <input type="number" name="tahun_publikasi" class="form-control mb-3" required>

                        <label>Link Publikasi:</label>
                        <input type="text" name="link_publikasi" class="form-control" required>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="create" class="btn btn-primary">Simpan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yakin ingin keluar?</h5>
                <button class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">Klik Logout untuk keluar.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div></div>
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