<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    header("Location: login.php"); exit;
}
$username = $_SESSION['nama_users'] ?? 'User';

// 1. AMBIL LIST DOSEN (Untuk Dropdown)
$stmt = $pdo->query("SELECT id_dosen, nama_dosen FROM dosen ORDER BY nama_dosen ASC");
$listDosen = $stmt->fetchAll();

// 2. AMBIL LIST MAHASISWA (Untuk Dropdown)
$stmt = $pdo->query("SELECT m.id_mahasiswa, u.nama_users, m.status_mahasiswa 
                     FROM mahasiswa m 
                     JOIN users u ON m.id_users = u.id_users 
                     ORDER BY u.nama_users ASC");
$listMahasiswa = $stmt->fetchAll();

// 3. QUERY PROYEK DOSEN (LENGKAP)
try {
    $stmt = $pdo->query("
        SELECT 
            p.*, 
            d.nama_dosen, 
            dpd.tanggal_mulai_proyek_dosen, dpd.tanggal_selesai_proyek_dosen, 
            dpd.nama_penulis_proyek_dosen, dpd.kategori_proyek_dosen, dpd.lokasi_proyek_dosen
        FROM proyek p
        JOIN detail_proyek_dosen dpd ON p.id_proyek = dpd.id_proyek
        LEFT JOIN dosen d ON dpd.id_dosen = d.id_dosen
        ORDER BY p.id_proyek DESC
    ");
    $proyekDosen = $stmt->fetchAll();
} catch (PDOException $e) { die("Error Dosen: " . $e->getMessage()); }

// 4. QUERY PROYEK MAHASISWA (LENGKAP)
try {
    $stmt = $pdo->query("
        SELECT 
            p.*, dpm.*, u.nama_users AS nama_mahasiswa
        FROM detail_proyek_mahasiswa dpm
        JOIN proyek p ON dpm.id_proyek = p.id_proyek
        LEFT JOIN mahasiswa m ON dpm.id_mahasiswa = m.id_mahasiswa
        LEFT JOIN users u ON m.id_users = u.id_users
        ORDER BY p.id_proyek DESC
    ");
    $proyekMahasiswa = $stmt->fetchAll();
} catch (PDOException $e) { die("Error Mahasiswa: " . $e->getMessage()); }
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
    <style>
        /* CSS untuk merapikan tabel */
        td.truncate {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        td.date-col {
            white-space: nowrap;
            font-size: 0.85rem;
        }
        .table th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php 
        $role = $_SESSION['role']; $pendingCount = 0; $waitingApproval = 0;
        include __DIR__ . '/sidebar.php'; 
        ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><span class="mr-2 text-gray-600 small">Halo, <b><?= htmlspecialchars($username) ?></b></span></li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Data Proyek</h1>
                    <p class="mb-4">Kelola data proyek dosen dan mahasiswa.</p>

                    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#createProyekDosen"><i class="fas fa-plus"></i> Tambah Proyek Dosen</button>
                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createProyekMahasiswa"><i class="fas fa-plus"></i> Tambah Proyek Mahasiswa</button>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success">Proyek Dosen</h6></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="tableDosen">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="20%">Dosen</th>
                                            <th width="25%">Judul</th>
                                            <th width="10%">Kategori</th>
                                            <th width="15%">Lokasi</th>
                                            <th width="15%">Periode</th> <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($proyekDosen as $d): ?>
                                        <tr>
                                            <td class="text-center"><?= $d['id_proyek'] ?></td>
                                            <td class="truncate" title="<?= htmlspecialchars($d['nama_dosen']) ?>">
                                                <?= htmlspecialchars($d['nama_dosen']) ?>
                                            </td>
                                            <td class="truncate" title="<?= htmlspecialchars($d['judul_proyek']) ?>">
                                                <?= htmlspecialchars($d['judul_proyek']) ?>
                                            </td>
                                            <td><?= htmlspecialchars($d['kategori_proyek_dosen']) ?></td>
                                            <td class="truncate"><?= htmlspecialchars($d['lokasi_proyek_dosen']) ?></td>
                                            <td class="text-center date-col">
                                                <?php 
                                                    $s = $d['tanggal_mulai_proyek_dosen'];
                                                    $e = $d['tanggal_selesai_proyek_dosen'];
                                                    echo ($s ? date('d M Y', strtotime($s)) : '-') . '<br>s/d<br>' . ($e ? date('d M Y', strtotime($e)) : '-');
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#editProyekDosen<?= $d['id_proyek'] ?>"><i class="fas fa-edit"></i></button>
                                                <a href="process_proyek.php?delete=<?= $d['id_proyek'] ?>" onclick="return confirm('Hapus?')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Proyek Mahasiswa</h6></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="tableMahasiswa">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="20%">Mahasiswa</th>
                                            <th width="25%">Judul</th>
                                            <th width="10%">Kategori</th>
                                            <th width="15%">Lokasi</th>
                                            <th width="15%">Periode</th> <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <?php foreach ($proyekMahasiswa as $p): ?> 
                                        <tr> 
                                            <td class="text-center"><?= $p['id_proyek'] ?></td> 
                                            <td class="truncate" title="<?= htmlspecialchars($p['nama_mahasiswa']) ?>">
                                                <?= htmlspecialchars($p['nama_mahasiswa']) ?>
                                            </td> 
                                            <td class="truncate" title="<?= htmlspecialchars($p['judul_proyek']) ?>">
                                                <?= htmlspecialchars($p['judul_proyek']) ?>
                                            </td> 
                                            <td><?= htmlspecialchars($p['kategori_proyek_mahasiswa']) ?></td> 
                                            <td class="truncate"><?= htmlspecialchars($p['lokasi_proyek_mahasiswa']) ?></td> 
                                            <td class="text-center date-col">
                                                <?php 
                                                    $s = $p['tanggal_mulai_proyek_mahasiswa'];
                                                    $e = $p['tanggal_selesai_proyek_mahasiswa'];
                                                    echo ($s ? date('d M Y', strtotime($s)) : '-') . '<br>s/d<br>' . ($e ? date('d M Y', strtotime($e)) : '-');
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#editProyekMahasiswa<?= $p['id_proyek'] ?>"><i class="fas fa-edit"></i></button>
                                                <a href="process_proyek.php?delete=<?= $p['id_proyek'] ?>" onclick="return confirm('Hapus?')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr> 
                                        <?php endforeach ?> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <footer class="sticky-footer bg-white"><div class="container my-auto text-center"><span>Copyright © LAB IVSS</span></div></footer>
        </div>
    </div>

    <div class="modal fade" id="createProyekDosen" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_proyek.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white "><h5 class="modal-title">Tambah Proyek Dosen</h5><button class="close text-white" data-dismiss="modal">×</button></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Pilih Dosen:</label>
                                <select name="id_dosen" class="form-control" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    <?php foreach($listDosen as $ld): ?>
                                        <option value="<?= $ld['id_dosen'] ?>"><?= htmlspecialchars($ld['nama_dosen']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group"><label>Judul:</label><input type="text" name="judul" class="form-control" required></div>
                        </div>
                        <div class="form-group"><label>Deskripsi:</label><textarea name="deskripsi" class="form-control" rows="2" required></textarea></div>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>Tahun:</label><input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required></div>
                            <div class="col-md-4 form-group"><label>Tipe:</label><input type="text" name="tipe" class="form-control" required></div>
                            <div class="col-md-4 form-group"><label>Kategori:</label><input type="text" name="kategori" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Mulai:</label><input type="date" name="tgl_mulai" class="form-control" required></div>
                            <div class="col-md-6 form-group"><label>Selesai:</label><input type="date" name="tgl_selesai" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Nama Penulis:</label><input type="text" name="nama_penulis" class="form-control" required></div>
                            <div class="col-md-6 form-group"><label>Lokasi:</label><input type="text" name="lokasi" class="form-control" required></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" name="create_dosen" class="btn btn-success">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <?php foreach($proyekDosen as $d): ?>
    <div class="modal fade" id="editProyekDosen<?= $d['id_proyek'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_proyek.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white"><h5 class="modal-title">Edit Proyek Dosen</h5><button class="close" data-dismiss="modal">×</button></div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id_proyek" value="<?= $d['id_proyek'] ?>">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Pilih Dosen:</label>
                                <select name="edit_id_dosen" class="form-control" required>
                                    <?php foreach($listDosen as $ld): ?>
                                        <option value="<?= $ld['id_dosen'] ?>" <?= $d['id_dosen'] == $ld['id_dosen'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ld['nama_dosen']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group"><label>Judul:</label><input type="text" name="edit_judul" value="<?= htmlspecialchars($d['judul_proyek']) ?>" class="form-control" required></div>
                        </div>
                        <div class="form-group"><label>Deskripsi:</label><textarea name="edit_deskripsi" class="form-control" rows="2"><?= htmlspecialchars($d['deskripsi_proyek']) ?></textarea></div>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>Tahun:</label><input type="number" name="edit_tahun" value="<?= $d['tahun_proyek'] ?>" class="form-control" required></div>
                            <div class="col-md-4 form-group"><label>Tipe:</label><input type="text" name="edit_tipe" value="<?= $d['tipe_proyek'] ?>" class="form-control" required></div>
                            <div class="col-md-4 form-group"><label>Kategori:</label><input type="text" name="edit_kategori" value="<?= htmlspecialchars($d['kategori_proyek_dosen']) ?>" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Mulai:</label><input type="date" name="edit_tgl_mulai" value="<?= $d['tanggal_mulai_proyek_dosen'] ?>" class="form-control"></div>
                            <div class="col-md-6 form-group"><label>Selesai:</label><input type="date" name="edit_tgl_selesai" value="<?= $d['tanggal_selesai_proyek_dosen'] ?>" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Penulis:</label><input type="text" name="edit_nama_penulis" value="<?= htmlspecialchars($d['nama_penulis_proyek_dosen']) ?>" class="form-control"></div>
                            <div class="col-md-6 form-group"><label>Lokasi:</label><input type="text" name="edit_lokasi" value="<?= htmlspecialchars($d['lokasi_proyek_dosen']) ?>" class="form-control"></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" name="update_dosen" class="btn btn-warning">Update</button></div>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="modal fade" id="createProyekMahasiswa" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_proyek.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white"><h5 class="modal-title">Tambah Proyek Mahasiswa</h5><button class="close text-white" data-dismiss="modal">×</button></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Pilih Mahasiswa:</label>
                                <select name="id_mahasiswa" class="form-control" required>
                                    <option value="">-- Pilih Mahasiswa --</option>
                                    <?php foreach($listMahasiswa as $lm): ?>
                                        <option value="<?= $lm['id_mahasiswa'] ?>">
                                            <?= htmlspecialchars($lm['nama_users']) ?> (<?= $lm['status_mahasiswa'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group"><label>Judul:</label><input type="text" name="judul" class="form-control" required></div>
                        </div>
                        <div class="form-group"><label>Deskripsi:</label><textarea name="deskripsi" class="form-control" rows="2" required></textarea></div>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>Tahun:</label><input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required></div>
                            <div class="col-md-4 form-group"><label>Tipe:</label><input type="text" name="tipe" class="form-control" required></div>
                            <div class="col-md-4 form-group"><label>Kategori:</label><input type="text" name="kategori" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Mulai:</label><input type="date" name="tgl_mulai" class="form-control" required></div>
                            <div class="col-md-6 form-group"><label>Selesai:</label><input type="date" name="tgl_selesai" class="form-control" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Penulis:</label><input type="text" name="nama_penulis" class="form-control" required></div>
                            <div class="col-md-6 form-group"><label>Lokasi:</label><input type="text" name="lokasi" class="form-control" required></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" name="create_mahasiswa" class="btn btn-primary">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    <?php foreach($proyekMahasiswa as $p): ?>
    <div class="modal fade" id="editProyekMahasiswa<?= $p['id_proyek'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_proyek.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white"><h5 class="modal-title">Edit Proyek Mahasiswa</h5><button class="close" data-dismiss="modal">×</button></div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id_proyek_mhs" value="<?= $p['id_proyek'] ?>">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Pilih Mahasiswa:</label>
                                <select name="edit_id_mahasiswa" class="form-control" required>
                                    <?php foreach($listMahasiswa as $lm): ?>
                                        <option value="<?= $lm['id_mahasiswa'] ?>" <?= $p['id_mahasiswa'] == $lm['id_mahasiswa'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($lm['nama_users']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group"><label>Judul:</label><input type="text" name="edit_judul_mhs" value="<?= htmlspecialchars($p['judul_proyek']) ?>" class="form-control" required></div>
                        </div>
                        <div class="form-group"><label>Deskripsi:</label><textarea name="edit_deskripsi_mhs" class="form-control" rows="2"><?= htmlspecialchars($p['deskripsi_proyek']) ?></textarea></div>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>Tahun:</label><input type="number" name="edit_tahun_mhs" value="<?= $p['tahun_proyek'] ?>" class="form-control"></div>
                            <div class="col-md-4 form-group"><label>Tipe:</label><input type="text" name="edit_tipe_mhs" value="<?= $p['tipe_proyek'] ?>" class="form-control"></div>
                            <div class="col-md-4 form-group"><label>Kategori:</label><input type="text" name="edit_kategori_mhs" value="<?= htmlspecialchars($p['kategori_proyek_mahasiswa']) ?>" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Mulai:</label><input type="date" name="edit_tgl_mulai" value="<?= $p['tanggal_mulai_proyek_mahasiswa'] ?>" class="form-control"></div>
                            <div class="col-md-6 form-group"><label>Selesai:</label><input type="date" name="edit_tgl_selesai" value="<?= $p['tanggal_selesai_proyek_mahasiswa'] ?>" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Penulis:</label><input type="text" name="edit_nama_penulis_mhs" value="<?= htmlspecialchars($p['nama_penulis_proyek_mahasiswa']) ?>" class="form-control"></div>
                            <div class="col-md-6 form-group"><label>Lokasi:</label><input type="text" name="edit_lokasi_mhs" value="<?= htmlspecialchars($p['lokasi_proyek_mahasiswa']) ?>" class="form-control"></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" name="update_mahasiswa" class="btn btn-warning">Update</button></div>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() { 
            $('#tableDosen').DataTable({ "order": [[ 0, "desc" ]] }); // Urutkan ID desc
            $('#tableMahasiswa').DataTable({ "order": [[ 0, "desc" ]] }); 
        });
    </script>
</body>
</html>