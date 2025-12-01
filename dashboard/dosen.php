<?php 
session_start();
require_once __DIR__ . '/db.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['admin_sistem'])) {
    echo "Akses Ditolak!";
    exit;
}

$username = $_SESSION['nama_users'] ?? 'User';

/* ============================
   HANDLERS: CREATE / UPDATE / DELETE RISET
   Semua proses CRUD riset (proyek + detail_proyek_dosen)
   dilakukan via POST dengan field 'action'
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        if ($action === 'tambah_riset') {
            // Ambil input
            $judul = trim($_POST['judul_proyek'] ?? '');
            $deskripsi = trim($_POST['deskripsi_proyek'] ?? '');
            $tahun = trim($_POST['tahun_proyek'] ?? '');
            $tipe = trim($_POST['tipe_proyek'] ?? '');
            $nama_penulis = trim($_POST['nama_penulis_proyek_dosen'] ?? '');
            $kategori = trim($_POST['kategori_proyek_dosen'] ?? '');
            $tanggal_mulai = trim($_POST['tanggal_mulai_proyek_dosen'] ?? null);
            $tanggal_selesai = trim($_POST['tanggal_selesai_proyek_dosen'] ?? null);
            $id_dosen = !empty($_POST['id_dosen']) ? (int)$_POST['id_dosen'] : null;

            // Insert ke tabel proyek
            $stmt = $pdo->prepare("INSERT INTO proyek (judul_proyek, deskripsi_proyek, tahun_proyek, tipe_proyek) VALUES (?, ?, ?, ?)");
            $stmt->execute([$judul, $deskripsi, $tahun, $tipe]);
            $newId = $pdo->lastInsertId();

            // Insert detail_proyek_dosen (jika ingin mengaitkan dengan dosen)
            $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_dosen (id_dosen, id_proyek, tanggal_mulai_proyek_dosen, tanggal_selesai_proyek_dosen, nama_penulis_proyek_dosen, kategori_proyek_dosen) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->execute([$id_dosen, $newId, $tanggal_mulai ?: null, $tanggal_selesai ?: null, $nama_penulis, $kategori]);

            $_SESSION['flash'] = "Riset berhasil ditambahkan.";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }

        if ($action === 'edit_riset') {
            $id = (int)($_POST['id_proyek'] ?? 0);
            $judul = trim($_POST['judul_proyek'] ?? '');
            $deskripsi = trim($_POST['deskripsi_proyek'] ?? '');
            $tahun = trim($_POST['tahun_proyek'] ?? '');
            $tipe = trim($_POST['tipe_proyek'] ?? '');
            $nama_penulis = trim($_POST['nama_penulis_proyek_dosen'] ?? '');
            $kategori = trim($_POST['kategori_proyek_dosen'] ?? '');
            $tanggal_mulai = trim($_POST['tanggal_mulai_proyek_dosen'] ?? null);
            $tanggal_selesai = trim($_POST['tanggal_selesai_proyek_dosen'] ?? null);
            $id_dosen = !empty($_POST['id_dosen']) ? (int)$_POST['id_dosen'] : null;

            // Update proyek
            $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek = ?, deskripsi_proyek = ?, tahun_proyek = ?, tipe_proyek = ? WHERE id_proyek = ?");
            $stmt->execute([$judul, $deskripsi, $tahun, $tipe, $id]);

            // Cek apakah ada detail_proyek_dosen untuk id_proyek ini
            $check = $pdo->prepare("SELECT id_detail_proyek_dosen FROM detail_proyek_dosen WHERE id_proyek = ?");
            $check->execute([$id]);
            $exists = $check->fetch();

            if ($exists) {
                // Update detail
                $stmt2 = $pdo->prepare("UPDATE detail_proyek_dosen SET id_dosen = ?, tanggal_mulai_proyek_dosen = ?, tanggal_selesai_proyek_dosen = ?, nama_penulis_proyek_dosen = ?, kategori_proyek_dosen = ? WHERE id_proyek = ?");
                $stmt2->execute([$id_dosen, $tanggal_mulai ?: null, $tanggal_selesai ?: null, $nama_penulis, $kategori, $id]);
            } else {
                // Insert detail baru
                $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_dosen (id_dosen, id_proyek, tanggal_mulai_proyek_dosen, tanggal_selesai_proyek_dosen, nama_penulis_proyek_dosen, kategori_proyek_dosen) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt2->execute([$id_dosen, $id, $tanggal_mulai ?: null, $tanggal_selesai ?: null, $nama_penulis, $kategori]);
            }

            $_SESSION['flash'] = "Riset berhasil diupdate.";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }

        if ($action === 'hapus_riset') {
            $id = (int)($_POST['id_proyek'] ?? 0);

            // Hapus detail dulu
            $stmt = $pdo->prepare("DELETE FROM detail_proyek_dosen WHERE id_proyek = ?");
            $stmt->execute([$id]);

            // Hapus proyek
            $stmt2 = $pdo->prepare("DELETE FROM proyek WHERE id_proyek = ?");
            $stmt2->execute([$id]);

            $_SESSION['flash'] = "Riset berhasil dihapus.";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }

    } catch (PDOException $e) {
        // Simpel error handling - tampilkan pesan dan tetap di halaman
        $_SESSION['flash_error'] = "Error DB: " . $e->getMessage();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

/* ============================
   READ DATA DOSEN
============================ */
try {
    $stmt = $pdo->query("SELECT * FROM mv_dosen ORDER BY id_dosen ASC");
    $dosen = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

/* ============================
   READ DATA RISET (JOIN)
============================ */
try {
    $sql = "
        SELECT p.*, d.id_detail_proyek_dosen, d.id_dosen, d.tanggal_mulai_proyek_dosen, d.tanggal_selesai_proyek_dosen, d.nama_penulis_proyek_dosen, d.kategori_proyek_dosen
        FROM proyek p
        LEFT JOIN detail_proyek_dosen d ON d.id_proyek = p.id_proyek
        ORDER BY p.id_proyek DESC
    ";
    $stmtR = $pdo->query($sql);
    $riset = $stmtR->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Dosen & Riset</title>

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

    <!-- FLASH MESSAGES -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <!-- DATA DOSEN -->
    <h1 class="h3 mb-2 text-gray-800">Data Dosen</h1>
    <p class="mb-4">Daftar dosen beserta profil lengkap.</p>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Bidang Riset</th>
                            <th>Jabatan</th>
                            <th>Prodi</th>
                            <th>NIDN</th>
                            <th>Email</th>
                            <th>LinkedIn</th>
                            <th>Google Scholar</th>
                            <th>Sinta</th>
                            <th>Pendidikan</th>
                            <th>Sertifikasi</th>
                            <th>Mata Kuliah</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($dosen as $d): ?>
                        <tr>

                            <!-- FOTO -->
                            <td class="text-center">
                                <?php
                                $path = $d['foto_dosen'];
                                $realPath = __DIR__ . '/' . $path;

                                if (!empty($path) && file_exists($realPath)):
                                    echo '<img src="' . $path . '" width="60" class="rounded">';
                                else:
                                    echo '<img src="img/no_image.png" width="60" class="rounded">';
                                endif;
                                ?>
                            </td>

                            <td><?= htmlspecialchars($d['nama_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['nip']) ?></td>
                            <td><?= htmlspecialchars($d['bidang_riset']) ?></td>
                            <td><?= htmlspecialchars($d['jabatan_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['prodi_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['nidn_dosen']) ?></td>
                            <td><?= htmlspecialchars($d['email_dosen']) ?></td>

                            <td><a href="<?= htmlspecialchars($d['link_linkedin']) ?>" target="_blank">LinkedIn</a></td>
                            <td><a href="<?= htmlspecialchars($d['link_google_scholar']) ?>" target="_blank">Scholar</a></td>
                            <td><a href="<?= htmlspecialchars($d['link_sinta']) ?>" target="_blank">Sinta</a></td>

                            <td><?= nl2br(htmlspecialchars($d['pendidikan_dosen'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($d['sertifikasi_dosen'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($d['mata_kuliah_dosen'])) ?></td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>

            </div>
        </div>
    </div>

    <!-- DATA RISET -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 text-gray-800">Data Riset</h1>
        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#tambahRisetModal"><i class="fas fa-plus"></i> Tambah Riset</button>
    </div>
    <p class="mb-4">Daftar produk riset, publikasi, dan proyek aktif dosen.</p>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="tabelRiset">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Tahun</th>
                            <th>Penulis/Tim</th>
                            <th>Kategori</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($riset as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['judul_proyek']) ?></td>

                            <td>
                                <?php if ($r['tipe_proyek'] == 'Produk Riset'): ?>
                                    <span class="badge badge-warning">Produk Riset</span>
                                <?php elseif ($r['tipe_proyek'] == 'Publikasi'): ?>
                                    <span class="badge badge-primary">Publikasi</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($r['tipe_proyek']) ?: 'Proyek' ?></span>
                                <?php endif; ?>
                            </td>

                            <td style="max-width:350px; white-space:normal;"><?= nl2br(htmlspecialchars($r['deskripsi_proyek'])) ?></td>
                            <td><?= htmlspecialchars($r['tahun_proyek']) ?></td>
                            <td><?= htmlspecialchars($r['nama_penulis_proyek_dosen']) ?></td>
                            <td><?= htmlspecialchars($r['kategori_proyek_dosen']) ?></td>
                            <td><?= htmlspecialchars($r['tanggal_mulai_proyek_dosen']) ?></td>
                            <td><?= htmlspecialchars($r['tanggal_selesai_proyek_dosen']) ?></td>

                            <td class="text-center">
                                <!-- edit button with data- attributes for JS -->
                                <button class="btn btn-sm btn-primary btn-edit-riset"
                                    data-id="<?= htmlspecialchars($r['id_proyek']) ?>"
                                    data-judul="<?= htmlspecialchars($r['judul_proyek']) ?>"
                                    data-deskripsi="<?= htmlspecialchars($r['deskripsi_proyek']) ?>"
                                    data-tahun="<?= htmlspecialchars($r['tahun_proyek']) ?>"
                                    data-tipe="<?= htmlspecialchars($r['tipe_proyek']) ?>"
                                    data-id_dosen="<?= htmlspecialchars($r['id_dosen']) ?>"
                                    data-nama_penulis="<?= htmlspecialchars($r['nama_penulis_proyek_dosen']) ?>"
                                    data-kategori="<?= htmlspecialchars($r['kategori_proyek_dosen']) ?>"
                                    data-tanggal_mulai="<?= htmlspecialchars($r['tanggal_mulai_proyek_dosen']) ?>"
                                    data-tanggal_selesai="<?= htmlspecialchars($r['tanggal_selesai_proyek_dosen']) ?>"
                                    >
                                    Edit
                                </button>

                                <form method="post" style="display:inline-block" onsubmit="return confirm('Yakin ingin menghapus riset ini?');">
                                    <input type="hidden" name="action" value="hapus_riset">
                                    <input type="hidden" name="id_proyek" value="<?= (int)$r['id_proyek'] ?>">
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
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
        <div class="copyright text-center">
            <span>&copy; LAB IVSS</span>
        </div>
    </div>
</footer>

</div>
</div>

<!-- Modal Tambah Riset -->
<div class="modal fade" id="tambahRisetModal" tabindex="-1" role="dialog" aria-labelledby="tambahRisetLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="post">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahRisetLabel">Tambah Riset / Publikasi / Proyek</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>×</span></button>
      </div>
      <div class="modal-body">
            <input type="hidden" name="action" value="tambah_riset">

            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul_proyek" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Tipe</label>
                <select name="tipe_proyek" class="form-control" required>
                    <option value="Produk Riset">Produk Riset</option>
                    <option value="Publikasi">Publikasi</option>
                    <option value="Proyek Aktif">Proyek Aktif</option>
                </select>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi_proyek" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tahun</label>
                    <input type="text" name="tahun_proyek" class="form-control">
                </div>
                <div class="form-group col-md-4">
                    <label>Penulis / Tim (nama)</label>
                    <input type="text" name="nama_penulis_proyek_dosen" class="form-control">
                </div>
                <div class="form-group col-md-5">
                    <label>Kategori</label>
                    <input type="text" name="kategori_proyek_dosen" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai_proyek_dosen" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai_proyek_dosen" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Hubungkan ke Dosen (opsional)</label>
                <select name="id_dosen" class="form-control">
                    <option value="">-- Pilih Dosen --</option>
                    <?php foreach ($dosen as $dd): ?>
                        <option value="<?= (int)$dd['id_dosen'] ?>"><?= htmlspecialchars($dd['nama_dosen']) ?></option>
                    <?php endforeach; ?>
                </select>
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

<!-- Modal Edit Riset -->
<div class="modal fade" id="editRisetModal" tabindex="-1" role="dialog" aria-labelledby="editRisetLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="post" id="formEditRiset">
      <div class="modal-header">
        <h5 class="modal-title" id="editRisetLabel">Edit Riset</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>×</span></button>
      </div>
      <div class="modal-body">
            <input type="hidden" name="action" value="edit_riset">
            <input type="hidden" name="id_proyek" id="edit_id_proyek">

            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul_proyek" id="edit_judul_proyek" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Tipe</label>
                <select name="tipe_proyek" id="edit_tipe_proyek" class="form-control" required>
                    <option value="Produk Riset">Produk Riset</option>
                    <option value="Publikasi">Publikasi</option>
                    <option value="Proyek Aktif">Proyek Aktif</option>
                </select>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi_proyek" id="edit_deskripsi_proyek" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tahun</label>
                    <input type="text" name="tahun_proyek" id="edit_tahun_proyek" class="form-control">
                </div>
                <div class="form-group col-md-4">
                    <label>Penulis / Tim (nama)</label>
                    <input type="text" name="nama_penulis_proyek_dosen" id="edit_nama_penulis" class="form-control">
                </div>
                <div class="form-group col-md-5">
                    <label>Kategori</label>
                    <input type="text" name="kategori_proyek_dosen" id="edit_kategori" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai_proyek_dosen" id="edit_tanggal_mulai" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai_proyek_dosen" id="edit_tanggal_selesai" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Hubungkan ke Dosen (opsional)</label>
                <select name="id_dosen" id="edit_id_dosen" class="form-control">
                    <option value="">-- Pilih Dosen --</option>
                    <?php foreach ($dosen as $dd): ?>
                        <option value="<?= (int)$dd['id_dosen'] ?>"><?= htmlspecialchars($dd['nama_dosen']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
      </form>
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
    $('#tabelRiset').DataTable();

    // tombol edit -> isi modal dengan data dari data-attributes
    $('.btn-edit-riset').on('click', function() {
        var btn = $(this);
        $('#edit_id_proyek').val(btn.data('id'));
        $('#edit_judul_proyek').val(btn.data('judul'));
        $('#edit_deskripsi_proyek').val(btn.data('deskripsi'));
        $('#edit_tahun_proyek').val(btn.data('tahun'));
        $('#edit_tipe_proyek').val(btn.data('tipe'));
        $('#edit_id_dosen').val(btn.data('id_dosen') || '');
        $('#edit_nama_penulis').val(btn.data('nama_penulis') || '');
        $('#edit_kategori').val(btn.data('kategori') || '');
        $('#edit_tanggal_mulai').val(btn.data('tanggal_mulai') || '');
        $('#edit_tanggal_selesai').val(btn.data('tanggal_selesai') || '');

        $('#editRisetModal').modal('show');
    });
});
</script>

<!-- Modal Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
  <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title">Yakin ingin keluar?</h5>
          <button class="close" data-dismiss="modal"><span>×</span></button>
      </div>
      <div class="modal-body">Klik "Logout" untuk keluar.</div>
      <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <a class="btn btn-primary" href="logout.php">Logout</a>
      </div>
  </div></div>
</div>

</body>
</html>
