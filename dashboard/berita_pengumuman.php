<?php
session_start();

// --- 1. VALIDASI AKSES (KEAMANAN) ---
// Cek apakah user sudah login?
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah role user adalah Admin Berita?
if ($_SESSION['role'] !== 'admin_berita') {
    // Jika bukan, tampilkan pesan dan kembalikan ke dashboard atau login
    echo "<script>
            alert('AKSES DITOLAK! Halaman ini hanya untuk Admin Berita.');
            window.location = 'dashboard.php';
          </script>";
    exit;
}
// Jika file ini satu folder dengan db.php, gunakan __DIR__ . '/db.php'
require_once __DIR__ . '/db.php'; 

// Ambil username dari session (sesuaikan dengan login.php: 'nama_users')
$username = $_SESSION['nama_users'] ?? 'Admin';
$id_user_login = $_SESSION['user_id']; // ID user yang sedang login

// ---------------------------
// DELETE
// ---------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    
    // Ambil data foto lama untuk dihapus dari folder (Opsional tapi disarankan)
    $stmt = $pdo->prepare("SELECT foto_berita FROM berita WHERE id_berita = :id");
    $stmt->execute(['id' => $id]);
    $fotoLama = $stmt->fetchColumn();
    
    if ($fotoLama && file_exists("../uploads/" . $fotoLama)) {
        unlink("../uploads/" . $fotoLama); // Hapus file fisik
    }

    $stmt = $pdo->prepare("DELETE FROM berita WHERE id_berita = :id");
    $stmt->execute(['id' => $id]);

    $_SESSION['message'] = "Berita berhasil dihapus!";
    $_SESSION['msg_type'] = "success";

    header("Location: berita_pengumuman.php");
    exit;
}

// ---------------------------
// EDIT – UPDATE
// ---------------------------
if (isset($_POST['update'])) {
    $id        = $_POST['id_berita'];
    $judul     = $_POST['judul_berita'];
    $isi       = $_POST['isi_berita'];
    $kategori  = $_POST['kategori_berita'];
    $link      = $_POST['link_berita'] ?? '';

    // Cek apakah ada upload foto baru?
    if (!empty($_FILES['foto_berita']['name'])) {
        $foto = time() . '_' . $_FILES['foto_berita']['name']; // Tambah time() biar unik
        $tmp  = $_FILES['foto_berita']['tmp_name'];
        move_uploaded_file($tmp, "../uploads/".$foto);
    } else {
        $foto = $_POST['foto_lama'];
    }

    $stmt = $pdo->prepare("UPDATE berita SET 
        judul_berita = :judul,
        isi_berita = :isi,
        kategori_berita = :kategori,
        foto_berita = :foto,
        link_berita = :link
        WHERE id_berita = :id");

    $stmt->execute([
        'judul' => $judul,
        'isi'   => $isi,
        'kategori' => $kategori,
        'foto'  => $foto,
        'link'  => $link,
        'id'    => $id
    ]);

    $_SESSION['message'] = "Berita berhasil diperbarui!";
    $_SESSION['msg_type'] = "success";

    header("Location: berita_pengumuman.php");
    exit;
}

// ---------------------------
// TAMBAH – BERITA
// ---------------------------
if (isset($_POST['tambah'])) {
    $judul    = $_POST['judul_berita'];
    $isi      = $_POST['isi_berita'];
    $kategori = $_POST['kategori_berita'];

    $foto = $_FILES['foto_berita']['name'];
    $tmp  = $_FILES['foto_berita']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);

    $author  = $_SESSION['nama'] ?? 'Admin';
    $id_users = $_SESSION['id_users'] ?? 1;

    $stmt = $pdo->prepare("INSERT INTO berita 
        (judul_berita, isi_berita, kategori_berita, foto_berita, author, id_users, created_at_berita)
        VALUES (:judul, :isi, :kategori, :foto, :author, :id_users, NOW())");

    $stmt->execute([
        'judul'    => $judul,
        'isi'      => $isi,
        'kategori' => $kategori,
        'foto'     => $foto,
        'author'   => $author,
        'id_users' => $id_users
    ]);

    $_SESSION['message'] = "Berita berhasil ditambahkan!";
    $_SESSION['msg_type'] = "success";

    header("Location: berita_pengumuman.php");
    exit;
}

// ---------------------------
// TAMBAH – TAUTAN BERITA
// ---------------------------
if (isset($_POST['tambah_tautan'])) {
    $judul = $_POST['judul_berita'];
    $link  = $_POST['link_berita'];

    $foto = $_FILES['foto_berita']['name'];
    $tmp  = $_FILES['foto_berita']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);

    $author  = $_SESSION['nama'] ?? 'Admin';
    $id_users = $_SESSION['id_users'] ?? 1;

    $stmt = $pdo->prepare("INSERT INTO berita 
        (judul_berita, isi_berita, kategori_berita, foto_berita, author, id_users, created_at_berita, link_berita)
        VALUES (:judul, '', 'Tautan', :foto, :author, :id_users, NOW(), :link)");

    $stmt->execute([
        'judul'    => $judul,
        'foto'     => $foto,
        'author'   => $author,
        'id_users' => $id_users,
        'link'     => $link
    ]);

    $_SESSION['message'] = "Tautan berita berhasil ditambahkan!";
    $_SESSION['msg_type'] = "info";

    header("Location: berita_pengumuman.php");
    exit;
}

$role = $_SESSION['role'] ?? null;

// Hitung badge (Opsional, untuk sidebar)
$pendingCount = $waitingApproval = 0;
// ... (Kode hitung badge tetap sama) ...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Berita & Pengumuman</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
</head>

<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>
    <!-- End Sidebar -->

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
            <!-- End Topbar -->

            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">Berita / Pengumuman</h1>
                <p class="mb-4">Kelola berita dan pengumuman.</p>
                <!-- ALERT -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
                <?php endif; ?>

                <!-- KONTEN UTAMA (Tabel/Form) SESUAI LOGIKA SEBELUMNYA -->
                <?php
                // --- FORM EDIT ---
                if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
                    $id = $_GET['id'];
                    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id_berita = :id");
                    $stmt->execute(['id' => $id]);
                    $d = $stmt->fetch();
                    
                    // Cek apakah data ditemukan
                    if (!$d) {
                        echo '<div class="alert alert-danger">Data tidak ditemukan!</div>';
                    } else {
                ?>
               <div class="modal fade" id="modalEdit" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title">Edit Berita</h5>
                                <button class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_berita" value="<?= $edit['id_berita'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $edit['foto_berita'] ?>">

                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Judul Berita</label>
                                    <input type="text" name="judul_berita" class="form-control"
                                        value="<?= htmlspecialchars($edit['judul_berita']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Isi Berita</label>
                                    <textarea name="isi_berita" class="form-control" rows="5"><?= htmlspecialchars($edit['isi_berita']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="kategori_berita" class="form-control">
                                        <option value="Berita" <?= $edit['kategori_berita']=='Berita'?'selected':'' ?>>Berita</option>
                                        <option value="Pengumuman" <?= $edit['kategori_berita']=='Pengumuman'?'selected':'' ?>>Pengumuman</option>
                                        <option value="Tautan" <?= $edit['kategori_berita']=='Tautan'?'selected':'' ?>>Tautan</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>URL Link (Opsional)</label>
                                    <input type="url" name="link_berita" class="form-control"
                                        value="<?= htmlspecialchars($edit['link_berita']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>Foto Saat Ini</label><br>
                                    <img src="../uploads/<?= $edit['foto_berita'] ?>" width="120" class="img-thumbnail mb-2">
                                    <input type="file" name="foto_berita" class="form-control-file">
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button class="btn btn-warning" name="update">Update Data</button>
                            </div>

                            </form>
                        </div>
                    </div>
                </div>

                <?php 
                    }
                // --- FORM TAMBAH ---
                } elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah') {
                ?>
                <div class="modal fade" id="modalTambah" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Tambah Berita Baru</h5>
                                <button class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-body">

                                <div class="form-group">
                                    <label>Judul Berita</label>
                                    <input type="text" name="judul_berita" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Isi Berita</label>
                                    <textarea name="isi_berita" class="form-control" rows="5" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="kategori_berita" class="form-control">
                                    <option value="Berita">Berita</option>
                                    <option value="Pengumuman">Pengumuman</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Foto Berita</label>
                                    <input type="file" name="foto_berita" class="form-control-file" required>
                                </div>

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button class="btn btn-primary" name="tambah">Simpan</button>
                                </div>
                            </form>
                        </div>
                </div>
                </div>

                <?php 
                // --- FORM TAMBAH TAUTAN ---
                } elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah_tautan') {
                ?>
                <div class="modal fade" id="modalTautan" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">Tambah Tautan Berita</h5>
                                <button class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-body">

                                    <div class="form-group">
                                        <label>Judul Berita/Link</label>
                                        <input type="text" name="judul_berita" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>URL Link</label>
                                        <input type="url" name="link_berita" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Foto Thumbnail</label>
                                        <input type="file" name="foto_berita" class="form-control-file" required>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button class="btn btn-info" name="tambah_tautan">Simpan Tautan</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>


                <?php
                } else {
                    $stmt = $pdo->query("SELECT * FROM berita ORDER BY created_at_berita DESC");
                    $data = $stmt->fetchAll();
                ?>
                <!-- Tabel Data Berita -->
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Data Berita</h6>
                        <div>
                            <a href="berita_pengumuman.php?aksi=tambah" class="btn btn-primary btn-sm">+ Tambah Berita</a>
                            <a href="berita_pengumuman.php?aksi=tambah_tautan" class="btn btn-info btn-sm">+ Tautan Berita</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="dataTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Author</th>
                                    <th>Tanggal</th>
                                    <th>Tautan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($data as $d): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><img src="../uploads/<?= $d['foto_berita'] ?>" width="70"></td>
                                    <td><?= $d['judul_berita'] ?></td>
                                    <td><?= $d['kategori_berita'] ?></td>
                                    <td><?= $d['author'] ?></td>
                                    <td><?= $d['created_at_berita'] ?></td>
                                    <td>
                                        <?php if (!empty($d['link_berita'])): ?>
                                            <a href="<?= htmlspecialchars($d['link_berita']) ?>" target="_blank" class="btn btn-info btn-sm">Lihat Tautan</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="berita_pengumuman.php?aksi=edit&id=<?= $d['id_berita'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="berita_pengumuman.php?aksi=hapus&id=<?= $d['id_berita'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus berita ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; LAB IVSS</span>
                </div>
            </div>
        </footer>

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
<script src="js/sb-admin-2.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

</body>
</html>
