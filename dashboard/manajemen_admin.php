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

// --- variabel untuk sidebar.php (role + badge counters) ---
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

/* =======================
   READ DATA USERS (JOIN TABLE)
   Menggunakan JOIN langsung agar mendapatkan nama_role dan nama_users
========================== */
try {
    // Admins: role id 1,2,3
    // Kita ambil u.* (semua dari users termasuk nama_users) dan r.nama_role
    $sqlAdmin = "SELECT u.*, r.nama_role 
                 FROM users u 
                 JOIN role r ON u.id_role = r.id_role 
                 WHERE u.id_role IN (1,2,3) 
                 ORDER BY u.id_users ASC";
    $stmt = $pdo->query($sqlAdmin);
    $admins = $stmt->fetchAll();

    // Regular users: role id 4,5
    $sqlUser = "SELECT u.*, r.nama_role 
                FROM users u 
                JOIN role r ON u.id_role = r.id_role 
                WHERE u.id_role IN (4,5) 
                ORDER BY u.id_users ASC";
    $stmt = $pdo->query($sqlUser);
    $users = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Data Anggota Lab</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <?php include __DIR__ . '/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
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

                    <h1 class="h3 mb-2 text-gray-800">Manajemen Data Anggota Lab</h1>
                    <p class="mb-4">Kelola data administrator, dosen, dan mahasiswa</p>

                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">
                        <i class="fas fa-plus"></i> Tambah Admin
                    </button>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            
                            <div class="card mb-4 border-left-primary">
                                <div class="card-header">
                                    <h5 class="mb-0 text-primary font-weight-bold">Daftar Admin Lab & Ketua Lab</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="dataTableAdmins">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Role</th> <th>Nama Lengkap</th> <th>Email</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $no = 1; ?>
                                                <?php foreach ($admins as $u): ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td><span class="badge badge-info"><?= htmlspecialchars($u['nama_role']) ?></span></td>
                                                        
                                                        <td><?= htmlspecialchars($u['nama_users']) ?></td>
                                                        
                                                        <td><?= htmlspecialchars($u['email_users']) ?></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-warning btn-sm" style="margin: 1.5px;" data-toggle="modal" data-target="#editModal<?= $u['id_users'] ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="process_manajemen_admin.php?delete=<?= $u['id_users'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    <div class="modal fade" id="editModal<?= $u['id_users'] ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <form action="process_manajemen_admin.php" method="POST">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h5 class="modal-title">Edit Admin</h5>
                                                                        <button class="close" data-dismiss="modal"><span>×</span></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="id_users" value="<?= $u['id_users'] ?>">

                                                                        <label>Role:</label>
                                                                        <select name="id_role" class="form-control mb-3" required>
                                                                            <option value="1" <?= $u['id_role'] == 1 ? 'selected' : '' ?>>Admin Sistem</option>
                                                                            <option value="2" <?= $u['id_role'] == 2 ? 'selected' : '' ?>>Admin Berita</option>
                                                                            <option value="3" <?= $u['id_role'] == 3 ? 'selected' : '' ?>>Ketua Lab</option>
                                                                        </select>

                                                                        <label>Nama Lengkap:</label>
                                                                        <input type="text" name="nama_users" class="form-control mb-3" value="<?= htmlspecialchars($u['nama_users']) ?>" required>

                                                                        <label>Email:</label>
                                                                        <input type="email" name="email_users" class="form-control mb-3" value="<?= htmlspecialchars($u['email_users']) ?>" required>

                                                                        <label>Password (opsional):</label>
                                                                        <input type="password" name="password" class="form-control">
                                                                        <small class="text-muted">Kosongkan jika tidak ingin mengganti password.</small>
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

                            <div class="card border-left-success">
                                <div class="card-header">
                                    <h5 class="mb-0 text-success font-weight-bold">Daftar Dosen Lab & Mahasiswa Lab</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="dataTableUsers">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Role</th> <th>Nama Lengkap</th> <th>Email</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $no = 1; ?>
                                                <?php foreach ($users as $u): ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td><span class="badge badge-secondary"><?= htmlspecialchars($u['nama_role']) ?></span></td>
                                                        <td><?= htmlspecialchars($u['nama_users']) ?></td>
                                                        <td><?= htmlspecialchars($u['email_users']) ?></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-warning btn-sm" style="margin: 1.5px;" data-toggle="modal" data-target="#editModal<?= $u['id_users'] ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="process_manajemen_admin.php?delete=<?= $u['id_users'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    <div class="modal fade" id="editModal<?= $u['id_users'] ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <form action="process_manajemen_admin.php" method="POST">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-success text-white">
                                                                        <h5 class="modal-title">Edit User</h5>
                                                                        <button class="close" data-dismiss="modal"><span>×</span></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="id_users" value="<?= $u['id_users'] ?>">

                                                                        <label>Role:</label>
                                                                        <select name="id_role" class="form-control mb-3" required>
                                                                            <option value="4" <?= $u['id_role'] == 4 ? 'selected' : '' ?>>Dosen</option>
                                                                            <option value="5" <?= $u['id_role'] == 5 ? 'selected' : '' ?>>Mahasiswa</option>
                                                                        </select>

                                                                        <label>Nama Lengkap:</label>
                                                                        <input type="text" name="nama_users" class="form-control mb-3" value="<?= htmlspecialchars($u['nama_users']) ?>" required>

                                                                        <label>Email:</label>
                                                                        <input type="email" name="email_users" class="form-control mb-3" value="<?= htmlspecialchars($u['email_users']) ?>" required>

                                                                        <label>Password (opsional):</label>
                                                                        <input type="password" name="password" class="form-control">
                                                                        <small class="text-muted">Kosongkan jika tidak ingin mengganti password.</small>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                        <button type="submit" name="update" class="btn btn-success">Simpan</button>
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
    </div>

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="process_manajemen_admin.php" method="POST">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah Admin Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <label>Role:</label>
                        <select name="id_role" class="form-control mb-3" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="1">Admin Sistem</option>
                            <option value="2">Admin Berita</option>
                            <option value="3">Ketua Lab</option>
                        </select>

                        <label>Nama Lengkap:</label>
                        <input type="text" name="nama_users" class="form-control mb-3" placeholder="Masukkan nama lengkap" required>

                        <label>Email:</label>
                        <input type="email" name="email_users" class="form-control mb-3" placeholder="email@contoh.com" required>

                        <label>Password:</label>
                        <input type="password" name="password" class="form-control" required>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="create" class="btn btn-primary">Simpan</button>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>

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
            $('#dataTableAdmins').DataTable();
            $('#dataTableUsers').DataTable();
        });
    </script>

</body>
</html>