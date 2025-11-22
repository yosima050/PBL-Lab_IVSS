<?php
session_start();
require '../dashboard/db.php';

// Ambil username dari session
$username = $_SESSION['nama'] ?? 'Admin';

// ---------------------------
// DELETE
// ---------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
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

    if (!empty($_FILES['foto_berita']['name'])) {
        $foto = $_FILES['foto_berita']['name'];
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

    $_SESSION['message'] = "Berita berhasil diupdate!";
    $_SESSION['msg_type'] = "success";

    header("Location: berita_pengumuman.php");
    exit;
}

// ---------------------------
// TAMBAH – INSERT
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manajemen Berita - LAB IVSS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?>"><?= $_SESSION['message'] ?></div>
                    <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
                <?php endif; ?>

                <?php
                if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
                    $id = $_GET['id'];
                    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id_berita = :id");
                    $stmt->execute(['id' => $id]);
                    $d = $stmt->fetch();
                ?>
                <!-- Form Edit Berita -->
                <div class="card shadow mb-4">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Edit Berita</h6></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_berita" value="<?= $d['id_berita'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $d['foto_berita'] ?>">

                            <label>Judul Berita</label>
                            <input type="text" name="judul_berita" class="form-control" value="<?= $d['judul_berita'] ?>">

                            <label>Isi Berita</label>
                            <textarea name="isi_berita" class="form-control" rows="5"><?= $d['isi_berita'] ?></textarea>

                            <label>Kategori</label>
                            <input type="text" name="kategori_berita" class="form-control" value="<?= $d['kategori_berita'] ?>">

                            <label>URL Link (Opsional)</label>
                            <input type="url" name="link_berita" class="form-control" value="<?= $d['link_berita'] ?>">

                            <label>Foto</label><br>
                            <img src="../uploads/<?= $d['foto_berita'] ?>" width="120">
                            <input type="file" name="foto_berita" class="form-control mt-2">

                            <button name="update" class="btn btn-warning mt-3">Update</button>
                            <a href="berita_pengumuman.php" class="btn btn-secondary mt-3">Kembali</a>
                        </form>
                    </div>
                </div>

                <?php
                } elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah') {
                ?>
                <!-- Form Tambah Berita -->
                <div class="card shadow mb-4">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Tambah Berita</h6></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">

                            <label>Judul Berita</label>
                            <input type="text" name="judul_berita" class="form-control" required>

                            <label>Isi Berita</label>
                            <textarea name="isi_berita" class="form-control" rows="5" required></textarea>

                            <label>Kategori</label>
                            <input type="text" name="kategori_berita" class="form-control">

                            <label>Foto Berita</label>
                            <input type="file" name="foto_berita" class="form-control" required>

                            <button name="tambah" class="btn btn-primary mt-3">Simpan</button>
                            <a href="berita_pengumuman.php" class="btn btn-secondary mt-3">Kembali</a>

                        </form>
                    </div>
                </div>

                <?php
                } elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah_tautan') {
                ?>
                <!-- Form Tambah Tautan Berita -->
                <div class="card shadow mb-4">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-info">Tambah Tautan Berita</h6></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">

                            <label>Judul Berita</label>
                            <input type="text" name="judul_berita" class="form-control" required>

                            <label>URL Link</label>
                            <input type="url" name="link_berita" class="form-control" placeholder="https://example.com" required>

                            <label>Foto Berita</label>
                            <input type="file" name="foto_berita" class="form-control" required>

                            <button name="tambah_tautan" class="btn btn-info mt-3">Simpan</button>
                            <a href="berita_pengumuman.php" class="btn btn-secondary mt-3">Kembali</a>
                        </form>
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
                    <span>Copyright &copy; LAB IVSS 2023</span>
                </div>
            </div>
        </footer>

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
