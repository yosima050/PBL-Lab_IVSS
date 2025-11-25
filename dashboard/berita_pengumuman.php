<?php
session_start();
<<<<<<< HEAD
require '../dashboard/db.php';

// Ambil username dari session
$username = $_SESSION['nama'] ?? 'Admin';
=======

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
// -------------------------------------

// Sesuaikan path db.php dengan struktur folder Anda
// Jika file ini satu folder dengan db.php, gunakan __DIR__ . '/db.php'
require_once __DIR__ . '/db.php'; 

// Ambil username dari session (sesuaikan dengan login.php: 'nama_users')
$username = $_SESSION['nama_users'] ?? 'Admin';
$id_user_login = $_SESSION['user_id']; // ID user yang sedang login
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1

// ---------------------------
// DELETE
// ---------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
<<<<<<< HEAD
=======
    
    // Ambil data foto lama untuk dihapus dari folder (Opsional tapi disarankan)
    $stmt = $pdo->prepare("SELECT foto_berita FROM berita WHERE id_berita = :id");
    $stmt->execute(['id' => $id]);
    $fotoLama = $stmt->fetchColumn();
    
    if ($fotoLama && file_exists("../uploads/" . $fotoLama)) {
        unlink("../uploads/" . $fotoLama); // Hapus file fisik
    }

>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
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

<<<<<<< HEAD
    if (!empty($_FILES['foto_berita']['name'])) {
        $foto = $_FILES['foto_berita']['name'];
=======
    // Cek apakah ada upload foto baru?
    if (!empty($_FILES['foto_berita']['name'])) {
        $foto = time() . '_' . $_FILES['foto_berita']['name']; // Tambah time() biar unik
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
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

<<<<<<< HEAD
    $_SESSION['message'] = "Berita berhasil diupdate!";
=======
    $_SESSION['message'] = "Berita berhasil diperbarui!";
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
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

<<<<<<< HEAD
    $foto = $_FILES['foto_berita']['name'];
    $tmp  = $_FILES['foto_berita']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);

    $author  = $_SESSION['nama'] ?? 'Admin';
    $id_users = $_SESSION['id_users'] ?? 1;
=======
    // Upload Foto
    $foto = time() . '_' . $_FILES['foto_berita']['name'];
    $tmp  = $_FILES['foto_berita']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);

    $author   = $username; // Gunakan nama dari session login
    $id_users = $id_user_login; // Gunakan ID dari session login
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1

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

<<<<<<< HEAD
    $foto = $_FILES['foto_berita']['name'];
    $tmp  = $_FILES['foto_berita']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);

    $author  = $_SESSION['nama'] ?? 'Admin';
    $id_users = $_SESSION['id_users'] ?? 1;
=======
    $foto = time() . '_' . $_FILES['foto_berita']['name'];
    $tmp  = $_FILES['foto_berita']['tmp_name'];
    move_uploaded_file($tmp, "../uploads/" . $foto);

    $author   = $username;
    $id_users = $id_user_login;
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1

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

<<<<<<< HEAD
?>
<!DOCTYPE html>
<html lang="en">
<head>
=======
$role = $_SESSION['role'] ?? null;

// Hitung badge (Opsional, untuk sidebar)
$pendingCount = $waitingApproval = 0;
// ... (Kode hitung badge tetap sama) ...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
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
<<<<<<< HEAD
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?>"><?= $_SESSION['message'] ?></div>
                    <?php unset($_SESSION['message'], $_SESSION['msg_type']); ?>
                <?php endif; ?>

                <?php
=======
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
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
                    $id = $_GET['id'];
                    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id_berita = :id");
                    $stmt->execute(['id' => $id]);
                    $d = $stmt->fetch();
<<<<<<< HEAD
                ?>
                <!-- Form Edit Berita -->
                <div class="card shadow mb-4">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Edit Berita</h6></div>
=======
                    
                    // Cek apakah data ditemukan
                    if (!$d) {
                        echo '<div class="alert alert-danger">Data tidak ditemukan!</div>';
                    } else {
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Edit Berita</h6>
                        <a href="berita_pengumuman.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_berita" value="<?= $d['id_berita'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $d['foto_berita'] ?>">

<<<<<<< HEAD
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
=======
                            <div class="form-group">
                                <label>Judul Berita</label>
                                <input type="text" name="judul_berita" class="form-control" value="<?= htmlspecialchars($d['judul_berita']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Isi Berita</label>
                                <textarea name="isi_berita" class="form-control" rows="5"><?= htmlspecialchars($d['isi_berita']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori_berita" class="form-control">
                                    <option value="Berita" <?= $d['kategori_berita'] == 'Berita' ? 'selected' : '' ?>>Berita</option>
                                    <option value="Pengumuman" <?= $d['kategori_berita'] == 'Pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                                    <option value="Tautan" <?= $d['kategori_berita'] == 'Tautan' ? 'selected' : '' ?>>Tautan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>URL Link (Opsional)</label>
                                <input type="url" name="link_berita" class="form-control" value="<?= htmlspecialchars($d['link_berita']) ?>">
                            </div>

                            <div class="form-group">
                                <label>Foto Saat Ini</label><br>
                                <?php if(!empty($d['foto_berita'])): ?>
                                    <img src="../uploads/<?= $d['foto_berita'] ?>" width="150" class="img-thumbnail mb-2">
                                <?php endif; ?>
                                <input type="file" name="foto_berita" class="form-control-file">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
                            </div>

                            <button type="submit" name="update" class="btn btn-warning">Update Data</button>
                            <a href="berita_pengumuman.php" class="btn btn-secondary">Batal</a>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                        </form>
                    </div>
                </div>

<<<<<<< HEAD
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

=======
                <?php 
                    }
                // --- FORM TAMBAH ---
                } elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah') {
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Tambah Berita Baru</h6>
                        <a href="berita_pengumuman.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
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
                            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                            <a href="berita_pengumuman.php" class="btn btn-secondary">Batal</a>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                        </form>
                    </div>
                </div>

<<<<<<< HEAD
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
=======
                <?php 
                // --- FORM TAMBAH TAUTAN ---
                } elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'tambah_tautan') {
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-info">Tambah Tautan Berita</h6>
                        <a href="berita_pengumuman.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Judul Berita/Link</label>
                                <input type="text" name="judul_berita" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>URL Link</label>
                                <input type="url" name="link_berita" class="form-control" placeholder="https://example.com" required>
                            </div>
                            <div class="form-group">
                                <label>Foto Thumbnail</label>
                                <input type="file" name="foto_berita" class="form-control-file" required>
                            </div>
                            <button type="submit" name="tambah_tautan" class="btn btn-info">Simpan Tautan</button>
                            <a href="berita_pengumuman.php" class="btn btn-secondary">Batal</a>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
                        </form>
                    </div>
                </div>

<<<<<<< HEAD
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
=======
                <?php 
                // --- TABEL DATA (DEFAULT) ---
                } else { 
                    $stmt = $pdo->query("SELECT * FROM berita ORDER BY created_at_berita DESC");
                    $data = $stmt->fetchAll();
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Berita</h6>
                        <div>
                            <a href="berita_pengumuman.php?aksi=tambah" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Berita</a>
                            <a href="berita_pengumuman.php?aksi=tambah_tautan" class="btn btn-info btn-sm"><i class="fas fa-link"></i> Tambah Tautan</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Foto</th>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Author</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($data as $d): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?php if(!empty($d['foto_berita'])): ?>
                                                <img src="../uploads/<?= htmlspecialchars($d['foto_berita']) ?>" width="80" class="img-thumbnail">
                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($d['judul_berita']) ?>
                                            <?php if(!empty($d['link_berita'])): ?>
                                                <br><a href="<?= htmlspecialchars($d['link_berita']) ?>" target="_blank" class="badge badge-light text-info"><i class="fas fa-external-link-alt"></i> Link</a>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge badge-secondary"><?= htmlspecialchars($d['kategori_berita']) ?></span></td>
                                        <td><?= htmlspecialchars($d['author']) ?></td>
                                        <td><?= date('d M Y', strtotime($d['created_at_berita'])) ?></td>
                                        <td>
                                            <a href="berita_pengumuman.php?aksi=edit&id=<?= $d['id_berita'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="berita_pengumuman.php?aksi=hapus&id=<?= $d['id_berita'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus berita ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
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

<<<<<<< HEAD
=======
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

>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<<<<<<< HEAD
=======
<script src="js/sb-admin-2.min.js"></script>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> d75a146cd181a649b06c01c6c905354ce40a84e1
