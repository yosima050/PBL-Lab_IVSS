<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    header("Location: login.php"); exit;
}
$username = $_SESSION['nama_users'] ?? 'User';

// Variabel untuk sidebar.php
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

// 1. AMBIL DATA UNTUK DROPDOWN
$listDosen = $pdo->query("SELECT id_dosen, nama_dosen FROM dosen ORDER BY nama_dosen ASC")->fetchAll();
$listMahasiswa = $pdo->query("SELECT m.id_mahasiswa, u.nama_users, m.status_mahasiswa FROM mahasiswa m JOIN users u ON m.id_users = u.id_users ORDER BY u.nama_users ASC")->fetchAll();

// 2. QUERY PROYEK DOSEN
try {
    $stmt = $pdo->query("
        SELECT p.id_proyek, p.judul_proyek, p.tahun_proyek, p.tipe_proyek, p.deskripsi_proyek, 
            p.foto_proyek, p.file_proyek,
            STRING_AGG(DISTINCT d.nama_dosen, ', ') as list_nama_dosen,
            STRING_AGG(DISTINCT CAST(d.id_dosen AS TEXT), ',') as list_id_dosen,
            STRING_AGG(DISTINCT u.nama_users, ', ') as list_nama_asisten,
            STRING_AGG(DISTINCT CAST(m.id_mahasiswa AS TEXT), ',') as list_id_asisten,
            MAX(dpd.tanggal_mulai_proyek_dosen) as tanggal_mulai, 
            MAX(dpd.tanggal_selesai_proyek_dosen) as tanggal_selesai, 
            MAX(dpd.nama_penulis_proyek_dosen) as nama_penulis, 
            MAX(dpd.kategori_proyek_dosen) as kategori, 
            MAX(dpd.lokasi_proyek_dosen) as lokasi
        FROM proyek p
        JOIN detail_proyek_dosen dpd ON p.id_proyek = dpd.id_proyek
        LEFT JOIN dosen d ON dpd.id_dosen = d.id_dosen
        LEFT JOIN detail_proyek_mahasiswa dpm ON p.id_proyek = dpm.id_proyek
        LEFT JOIN mahasiswa m ON dpm.id_mahasiswa = m.id_mahasiswa
        LEFT JOIN users u ON m.id_users = u.id_users
        GROUP BY p.id_proyek ORDER BY p.id_proyek DESC
    ");
    $proyekDosen = $stmt->fetchAll();
} catch (PDOException $e) { die("Error Dosen: " . $e->getMessage()); }

// 3. QUERY PROYEK MAHASISWA (FIX: Tambahkan WHERE p.id_mahasiswa IS NOT NULL)
try {
    $stmt = $pdo->query("
        SELECT p.id_proyek, p.judul_proyek, p.tahun_proyek, p.tipe_proyek, p.deskripsi_proyek, p.id_dosen AS id_pembimbing,
            p.foto_proyek, p.file_proyek,
            d_pembimbing.nama_dosen AS nama_pembimbing,
            STRING_AGG(u.nama_users, ', ') as list_nama_mahasiswa,
            STRING_AGG(CAST(m.id_mahasiswa AS TEXT), ',') as list_id_mahasiswa,
            MAX(dpm.tanggal_mulai_proyek_mahasiswa) as tanggal_mulai,
            MAX(dpm.tanggal_selesai_proyek_mahasiswa) as tanggal_selesai,
            MAX(dpm.nama_penulis_proyek_mahasiswa) as nama_penulis, 
            MAX(dpm.kategori_proyek_mahasiswa) as kategori, 
            MAX(dpm.lokasi_proyek_mahasiswa) as lokasi
        FROM proyek p
        JOIN detail_proyek_mahasiswa dpm ON p.id_proyek = dpm.id_proyek
        LEFT JOIN mahasiswa m ON dpm.id_mahasiswa = m.id_mahasiswa
        LEFT JOIN users u ON m.id_users = u.id_users
        LEFT JOIN dosen d_pembimbing ON p.id_dosen = d_pembimbing.id_dosen
        WHERE p.id_mahasiswa IS NOT NULL -- FILTER PENTING: Hanya ambil proyek milik mahasiswa
        GROUP BY p.id_proyek, d_pembimbing.nama_dosen ORDER BY p.id_proyek DESC
    ");
    $proyekMahasiswa = $stmt->fetchAll();
} catch (PDOException $e) { die("Error Mahasiswa: " . $e->getMessage()); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Proyek - Lab IVSS</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">

<style>
    td.truncate { max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    td.date-col { white-space: nowrap; font-size: 0.85rem; padding: 4px 6px !important; }
    .table th { vertical-align: middle; text-align: center; }
    #tableDosen tbody td, #tableMahasiswa tbody td { vertical-align: middle !important; padding: 6px 8px !important; }

    /* SELECT2 STYLING */
    .select2-container--bootstrap4 .select2-selection--multiple {
        min-height: 42px !important; height: auto !important; border: 1px solid #d1d3e2; padding: 4px 8px; display: flex; flex-wrap: wrap; align-items: center;
    }
    .select2-container--bootstrap4 .select2-selection--single {
        height: 42px !important; padding: 6px 12px; border: 1px solid #d1d3e2; display: flex; align-items: center;
    }
    .select2-container--bootstrap4 .select2-selection__choice {
        background-color: #4e73df !important; border: none !important; border-radius: 20px !important; color: #fff !important; padding: 4px 12px !important; margin: 3px 5px 3px 0 !important; font-size: 0.85rem; font-weight: 600; display: inline-flex !important; flex-direction: row-reverse; align-items: center;
    }
    .select2-container--bootstrap4 .select2-selection__choice__remove {
        border: none !important; background: transparent !important; color: #fff !important; margin-left: 8px !important; margin-right: 0 !important; font-weight: bold; font-size: 14px; padding: 0 !important; opacity: 0.7;
    }
    .select2-container--bootstrap4 .select2-selection__choice__remove:hover { opacity: 1; color: #ffcccc !important; }
    .select2-search__field { margin-top: 5px !important; font-size: 0.9rem; }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { color: #6e707e; line-height: normal !important; padding-left: 0; }
    .select2-container--bootstrap4.select2-container--focus .select2-selection { border-color: #bac8f3; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25); }
    .modal-header { background: linear-gradient(45deg, #1cc88a, #13855c); color: white; }
    .close { color: white !important; opacity: 0.8; }
    .close:hover { opacity: 1; }
</style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($username) ?></span>
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
                     <div class="d-sm-flex align-items-center justify-content-between mb-4">
                         <h1 class="h3 mb-0 text-gray-800">Manajemen Proyek</h1>
                     </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <button class="btn btn-success shadow-sm mr-2" data-toggle="modal" data-target="#createProyekDosen">
                                <i class="fas fa-chalkboard-teacher fa-sm text-white-50"></i> Tambah Proyek Dosen
                            </button>
                            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#createProyekMahasiswa">
                                <i class="fas fa-user-graduate fa-sm text-white-50"></i> Tambah Proyek Mahasiswa
                            </button>
                        </div>
                    </div>

                    <div class="card shadow mb-4 border-left-success">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">Daftar Proyek Dosen</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tableDosen" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Tim & Asisten</th>
                                            <th width="20%">Judul Proyek</th>
                                            <th width="10%">Media</th> <th width="10%">Kategori</th>
                                            <th width="10%">Lokasi</th>
                                            <th width="15%">Periode</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($proyekDosen as $d): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <small class="text-uppercase text-secondary font-weight-bold" style="font-size: 0.7rem;">Tim Dosen:</small><br>
                                                <i class="fas fa-users text-success"></i> <?= htmlspecialchars($d['list_nama_dosen']) ?>
                                                <?php if(!empty($d['list_nama_asisten'])): ?>
                                                    <hr class="my-1">
                                                    <small class="text-uppercase text-secondary font-weight-bold" style="font-size: 0.7rem;">Asisten:</small><br>
                                                    <i class="fas fa-user-graduate text-info"></i> <?= htmlspecialchars($d['list_nama_asisten']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="col-truncate" title="<?= htmlspecialchars($d['judul_proyek']) ?>">
                                                <span class="font-weight-bold text-dark"><?= htmlspecialchars($d['judul_proyek']) ?></span>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($d['deskripsi_proyek'], 0, 50)) ?>...</small>
                                            </td>
                                            
                                            <td class="text-center">
                                                <?php if(!empty($d['foto_proyek'])): ?>
                                                    <a href="uploads/proyek/<?= htmlspecialchars($d['foto_proyek']) ?>" target="_blank">
                                                        <img src="uploads/proyek/<?= htmlspecialchars($d['foto_proyek']) ?>" alt="Foto" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if(!empty($d['file_proyek'])): ?>
                                                    <div class="mt-1">
                                                        <a href="uploads/proyek/<?= htmlspecialchars($d['file_proyek']) ?>" class="btn btn-sm btn-info btn-circle" title="Download File" download>
                                                            <i class="fas fa-file-download"></i>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(empty($d['foto_proyek']) && empty($d['file_proyek'])): ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><span class="badge badge-secondary"><?= htmlspecialchars($d['kategori']) ?></span></td>
                                            <td class="col-truncate"><?= htmlspecialchars($d['lokasi']) ?></td>
                                            <td class="col-date">
                                                <?= date('d M Y', strtotime($d['tanggal_mulai'])) ?><br>
                                                <span class="text-xs text-gray-500">s/d</span><br>
                                                <?= date('d M Y', strtotime($d['tanggal_selesai'])) ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm btn-circle" data-toggle="modal" data-target="#editProyekDosen<?= $d['id_proyek'] ?>" title="Edit" style="margin: 1.5px;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="process_proyek.php?delete=<?= $d['id_proyek'] ?>" onclick="return confirm('Hapus proyek ini?')" class="btn btn-danger btn-sm btn-circle" title="Hapus" style="margin: 1.5px;">
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

                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Proyek Mahasiswa</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tableMahasiswa" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Tim & Pembimbing</th>
                                            <th width="20%">Judul Proyek</th>
                                            <th width="10%">Media</th> <th width="10%">Kategori</th>
                                            <th width="10%">Lokasi</th>
                                            <th width="15%">Periode</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($proyekMahasiswa as $p): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <small class="text-uppercase text-secondary font-weight-bold" style="font-size: 0.7rem;">Tim Mahasiswa:</small><br>
                                                <i class="fas fa-users text-primary"></i> <?= htmlspecialchars($p['list_nama_mahasiswa']) ?>
                                                <?php if(!empty($p['nama_pembimbing'])): ?>
                                                    <hr class="my-1">
                                                    <small class="text-uppercase text-secondary font-weight-bold" style="font-size: 0.7rem;">Pembimbing:</small><br>
                                                    <span class="badge badge-success"><i class="fas fa-chalkboard-teacher"></i> <?= htmlspecialchars($p['nama_pembimbing']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="col-truncate" title="<?= htmlspecialchars($p['judul_proyek']) ?>">
                                                <span class="font-weight-bold text-dark"><?= htmlspecialchars($p['judul_proyek']) ?></span>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($p['deskripsi_proyek'], 0, 50)) ?>...</small>
                                            </td>

                                            <td class="text-center">
                                                <?php if(!empty($p['foto_proyek'])): ?>
                                                    <a href="uploads/proyek/<?= htmlspecialchars($p['foto_proyek']) ?>" target="_blank">
                                                        <img src="uploads/proyek/<?= htmlspecialchars($p['foto_proyek']) ?>" alt="Foto" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if(!empty($p['file_proyek'])): ?>
                                                    <div class="mt-1">
                                                        <a href="uploads/proyek/<?= htmlspecialchars($p['file_proyek']) ?>" class="btn btn-sm btn-info btn-circle" title="Download File" download>
                                                            <i class="fas fa-file-download"></i>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if(empty($p['foto_proyek']) && empty($p['file_proyek'])): ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><span class="badge badge-info"><?= htmlspecialchars($p['kategori']) ?></span></td>
                                            <td class="col-truncate"><?= htmlspecialchars($p['lokasi']) ?></td>
                                            <td class="col-date">
                                                <?= date('d M Y', strtotime($p['tanggal_mulai'])) ?><br>
                                                <span class="text-xs text-gray-500">s/d</span><br>
                                                <?= date('d M Y', strtotime($p['tanggal_selesai'])) ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm btn-circle" data-toggle="modal" data-target="#editProyekMahasiswa<?= $p['id_proyek'] ?>" title="Edit" style="margin: 1.5px;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="process_proyek.php?delete=<?= $p['id_proyek'] ?>" onclick="return confirm('Hapus proyek ini?')" class="btn btn-danger btn-sm btn-circle" title="Hapus" style="margin: 1.5px;">
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
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Lab IVSS 2025</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <div class="modal fade" id="createProyekDosen" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_proyek.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Proyek Dosen</h5>
                        <button class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tim Dosen</label>
                                    <select name="id_dosen[]" class="form-control select2-multiple" multiple required data-placeholder="-- Pilih Dosen --">
                                        <?php foreach($listDosen as $ld): ?>
                                            <option value="<?= $ld['id_dosen'] ?>"><?= htmlspecialchars($ld['nama_dosen']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-info">Asisten Mahasiswa</label>
                                    <select name="mahasiswa_asisten[]" class="form-control select2-multiple" multiple required data-placeholder="-- Pilih Mahasiswa --">
                                        <?php foreach($listMahasiswa as $lm): ?>
                                            <option value="<?= $lm['id_mahasiswa'] ?>"><?= htmlspecialchars($lm['nama_users']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Judul Proyek</label>
                            <input type="text" name="judul" class="form-control" placeholder="Masukkan judul lengkap..." required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Tahun</label>
                                <select name="tahun" class="form-control select2-single" required>
                                    <?php for($y = date('Y')+1; $y >= 2015; $y--): ?>
                                        <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4"><label>Tipe Proyek</label><input type="text" name="tipe" class="form-control" placeholder="Ex: Penelitian" required></div>
                            <div class="col-md-4"><label>Kategori</label><input type="text" name="kategori" class="form-control" placeholder="Ex: AI/IoT" required></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6"><label>Tanggal Mulai</label><input type="date" name="tgl_mulai" class="form-control" required></div>
                            <div class="col-md-6"><label>Tanggal Selesai</label><input type="date" name="tgl_selesai" class="form-control" required></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6"><label>Penulis/Publikasi</label><input type="text" name="nama_penulis" class="form-control" required></div>
                            <div class="col-md-6"><label>Lokasi</label><input type="text" name="lokasi" class="form-control" required></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Foto Proyek:</label>
                                <input type="file" name="foto_proyek" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>File Dokumentasi:</label>
                                <input type="file" name="file_proyek" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted">Format: PDF, DOC, DOCX, XLS, XLSX (Max 5MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="create_dosen" class="btn btn-primary">Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php foreach($proyekDosen as $d): ?>
    <div class="modal fade" id="editProyekDosen<?= $d['id_proyek'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_proyek.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-white"><i class="fas fa-edit"></i> Edit Proyek Dosen</h5>
                        <button class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id_proyek" value="<?= $d['id_proyek'] ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Tim Dosen</label>
                                <select name="edit_id_dosen[]" class="form-control select2-multiple" multiple required data-placeholder="-- Pilih Dosen --">
                                    <?php $ids = !empty($d['list_id_dosen']) ? explode(',', $d['list_id_dosen']) : []; ?>
                                    <?php foreach($listDosen as $ld): ?>
                                        <option value="<?= $ld['id_dosen'] ?>" <?= in_array($ld['id_dosen'], $ids) ? 'selected' : '' ?>><?= htmlspecialchars($ld['nama_dosen']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="text-info">Asisten Mahasiswa</label>
                                <select name="edit_mahasiswa_asisten[]" class="form-control select2-multiple" multiple required data-placeholder="-- Pilih Mahasiswa --">
                                    <?php $asisten_ids = !empty($d['list_id_asisten']) ? explode(',', $d['list_id_asisten']) : []; ?>
                                    <?php foreach($listMahasiswa as $lm): ?>
                                        <option value="<?= $lm['id_mahasiswa'] ?>" <?= in_array($lm['id_mahasiswa'], $asisten_ids) ? 'selected' : '' ?>><?= htmlspecialchars($lm['nama_users']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label>Judul</label>
                            <input type="text" name="edit_judul" value="<?= htmlspecialchars($d['judul_proyek']) ?>" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Deskripsi</label>
                            <textarea name="edit_deskripsi" class="form-control" rows="3"><?= htmlspecialchars($d['deskripsi_proyek']) ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Tahun</label>
                                <select name="edit_tahun" class="form-control select2-single" required>
                                    <?php for($y = date('Y')+1; $y >= 2015; $y--): ?>
                                        <option value="<?= $y ?>" <?= $y == $d['tahun_proyek'] ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4"><label>Tipe</label><input type="text" name="edit_tipe" value="<?= $d['tipe_proyek'] ?>" class="form-control" required></div>
                            <div class="col-md-4"><label>Kategori</label><input type="text" name="edit_kategori" value="<?= htmlspecialchars($d['kategori']) ?>" class="form-control" required></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label>Mulai</label><input type="date" name="edit_tgl_mulai" value="<?= $d['tanggal_mulai'] ?>" class="form-control"></div>
                            <div class="col-md-6"><label>Selesai</label><input type="date" name="edit_tgl_selesai" value="<?= $d['tanggal_selesai'] ?>" class="form-control"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label>Penulis</label><input type="text" name="edit_nama_penulis" value="<?= htmlspecialchars($d['nama_penulis']) ?>" class="form-control"></div>
                            <div class="col-md-6"><label>Lokasi</label><input type="text" name="edit_lokasi" value="<?= htmlspecialchars($d['lokasi']) ?>" class="form-control"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Foto Proyek (Upload jika ingin ganti):</label>
                                <input type="file" name="foto_proyek" class="form-control" accept="image/*">
                                <?php if(!empty($d['foto_proyek'])): ?>
                                    <small class="text-success"><i class="fas fa-check"></i> Foto tersimpan: <?= $d['foto_proyek'] ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>File (Upload jika ingin ganti):</label>
                                <input type="file" name="file_proyek" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <?php if(!empty($d['file_proyek'])): ?>
                                    <small class="text-success"><i class="fas fa-check"></i> File tersimpan: <?= $d['file_proyek'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="update_dosen" class="btn btn-warning">Update Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="modal fade" id="createProyekMahasiswa" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_proyek.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(45deg, #1cc88a, #13855c);">
                        <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Proyek Mahasiswa</h5>
                        <button class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tim Mahasiswa</label>
                                    <select name="id_mahasiswa[]" class="form-control select2-multiple" multiple required data-placeholder="-- Pilih Mahasiswa --">
                                        <?php foreach($listMahasiswa as $lm): ?>
                                            <option value="<?= $lm['id_mahasiswa'] ?>"><?= htmlspecialchars($lm['nama_users']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-success">Dosen Pembimbing</label>
                                    <select name="dosen_pembimbing" class="form-control select2-multiple" multiple required data-placeholder="-- Pilih Dosen --">
                                        <?php foreach($listDosen as $ld): ?>
                                            <option value="<?= $ld['id_dosen'] ?>"><?= htmlspecialchars($ld['nama_dosen']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3"><label>Judul Proyek</label><input type="text" name="judul" class="form-control" required></div>
                        <div class="form-group mb-3"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3" required></textarea></div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Tahun</label>
                                <select name="tahun" class="form-control select2-single" required>
                                    <?php for($y = date('Y')+1; $y >= 2015; $y--): ?>
                                        <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4"><label>Tipe</label><input type="text" name="tipe" class="form-control" required></div>
                            <div class="col-md-4"><label>Kategori</label><input type="text" name="kategori" class="form-control" required></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label>Mulai</label><input type="date" name="tgl_mulai" class="form-control" required></div>
                            <div class="col-md-6"><label>Selesai</label><input type="date" name="tgl_selesai" class="form-control" required></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label>Penulis</label><input type="text" name="nama_penulis" class="form-control" required></div>
                            <div class="col-md-6"><label>Lokasi</label><input type="text" name="lokasi" class="form-control" required></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Foto Proyek:</label>
                                <input type="file" name="foto_proyek" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>File Dokumentasi:</label>
                                <input type="file" name="file_proyek" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted">Format: PDF, DOC, DOCX, XLS, XLSX (Max 5MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="create_mahasiswa" class="btn btn-success">Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php foreach($proyekMahasiswa as $p): ?>
    <div class="modal fade" id="editProyekMahasiswa<?= $p['id_proyek'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_proyek.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-white"><i class="fas fa-edit"></i> Edit Proyek Mahasiswa</h5>
                        <button class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id_proyek_mhs" value="<?= $p['id_proyek'] ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Tim Mahasiswa</label>
                                <select name="edit_id_mahasiswa[]" class="form-control select2-multiple" multiple required data-placeholder="-- Pilih Mahasiswa --">
                                    <?php $ids = !empty($p['list_id_mahasiswa']) ? explode(',', $p['list_id_mahasiswa']) : []; ?>
                                    <?php foreach($listMahasiswa as $lm): ?>
                                        <option value="<?= $lm['id_mahasiswa'] ?>" <?= in_array($lm['id_mahasiswa'], $ids) ? 'selected' : '' ?>><?= htmlspecialchars($lm['nama_users']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="text-success">Dosen Pembimbing</label>
                                <select name="edit_dosen_pembimbing" class="form-control select2-single">
                                    <option value="">-- Pilih Pembimbing --</option>
                                    <?php foreach($listDosen as $ld): ?>
                                        <option value="<?= $ld['id_dosen'] ?>" <?= $p['id_pembimbing'] == $ld['id_dosen'] ? 'selected' : '' ?>><?= htmlspecialchars($ld['nama_dosen']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3"><label>Judul</label><input type="text" name="edit_judul_mhs" value="<?= htmlspecialchars($p['judul_proyek']) ?>" class="form-control" required></div>
                        <div class="form-group mb-3"><label>Deskripsi</label><textarea name="edit_deskripsi_mhs" class="form-control" rows="3"><?= htmlspecialchars($p['deskripsi_proyek']) ?></textarea></div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Tahun</label>
                                <select name="edit_tahun_mhs" class="form-control select2-single">
                                    <?php for($y = date('Y')+1; $y >= 2015; $y--): ?>
                                        <option value="<?= $y ?>" <?= $y == $p['tahun_proyek'] ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4"><label>Tipe</label><input type="text" name="edit_tipe_mhs" value="<?= $p['tipe_proyek'] ?>" class="form-control"></div>
                            <div class="col-md-4"><label>Kategori</label><input type="text" name="edit_kategori_mhs" value="<?= htmlspecialchars($p['kategori']) ?>" class="form-control"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label>Mulai</label><input type="date" name="edit_tgl_mulai" value="<?= $p['tanggal_mulai'] ?>" class="form-control"></div>
                            <div class="col-md-6"><label>Selesai</label><input type="date" name="edit_tgl_selesai" value="<?= $p['tanggal_selesai'] ?>" class="form-control"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label>Penulis</label><input type="text" name="edit_nama_penulis_mhs" value="<?= htmlspecialchars($p['nama_penulis']) ?>" class="form-control"></div>
                            <div class="col-md-6"><label>Lokasi</label><input type="text" name="edit_lokasi_mhs" value="<?= htmlspecialchars($p['lokasi']) ?>" class="form-control"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Foto Proyek:</label>
                                <input type="file" name="edit_foto_proyek" class="form-control" accept="image/*">
                                <?php if(!empty($p['foto_proyek'])): ?>
                                    <small class="text-success"><i class="fas fa-check"></i> Foto tersimpan: <?= $p['foto_proyek'] ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>File Dokumentasi:</label>
                                <input type="file" name="edit_file_proyek" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <?php if(!empty($p['file_proyek'])): ?>
                                    <small class="text-success"><i class="fas fa-check"></i> File tersimpan: <?= $p['file_proyek'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="update_mahasiswa" class="btn btn-warning">Update Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() { 
            // 1. Inisialisasi DataTable
            if (!$.fn.DataTable.isDataTable('#tableDosen')) { 
                $('#tableDosen').DataTable({ "order": [[ 0, "desc" ]]}); 
            }
            if (!$.fn.DataTable.isDataTable('#tableMahasiswa')) { 
                $('#tableMahasiswa').DataTable({ "order": [[ 0, "desc" ]] }); 
            }

            // 2. Setup Select2 Global
            $.fn.modal.Constructor.prototype._enforceFocus = function() {}; 

            // === PERUBAHAN DI SINI (LOGIKA PLACEHOLDER DINAMIS) ===
            // Kita loop setiap elemen dengan class .select2-multiple
            $('.select2-multiple').each(function() {
                $(this).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    // Ambil teks dari atribut data-placeholder di HTML
                    // Jika tidak ada, gunakan default "Klik untuk memilih..."
                    placeholder: $(this).data('placeholder') || "Klik untuk memilih...",
                    allowClear: true,
                    dropdownParent: $('body')
                });
            });

            // Select2 untuk SINGLE (Tahun, Pembimbing)
            $('.select2-single').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('body')
            });
        });
    </script>
</body>
</html>