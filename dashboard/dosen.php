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

$role = $_SESSION['role'] ?? null;
$pendingCount = 0;
$waitingApproval = 0;

try {
    // Hitung Pendaftar Baru (Status: Pending)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Pending'");
    $stmt->execute();
    $pendingCount = (int)$stmt->fetchColumn();
} catch (Exception $e) {
    $pendingCount = 0;
}

try {
    // Hitung Validasi Data (Status: Menunggu)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE status_mahasiswa = 'Menunggu'");
    $stmt->execute();
    $waitingApproval = (int)$stmt->fetchColumn();
} catch (Exception $e) {
    $waitingApproval = 0;
}

// ============================
// LOGIKA HAPUS (PERBAIKAN: Hapus Dosen + User)
// ============================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];

    try {
        $pdo->beginTransaction();

        // 1. Ambil Foto & ID User sebelum dihapus
        $stmt = $pdo->prepare("SELECT foto_dosen, id_users FROM dosen WHERE id_dosen = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        // Hapus Foto Fisik
        if ($data && !empty($data['foto_dosen']) && file_exists($uploadDir . $data['foto_dosen'])) {
            @unlink($uploadDir . $data['foto_dosen']);
        }

        // 2. PUTUS HUBUNGAN DULU (Agar tidak kena Foreign Key Constraint)
        // Set id_dosen di tabel users menjadi NULL
        $pdo->prepare("UPDATE users SET id_dosen = NULL WHERE id_dosen = :id")->execute(['id' => $id]);
        
        // 3. Hapus Data Dosen
        $pdo->prepare("DELETE FROM dosen WHERE id_dosen = :id")->execute(['id' => $id]);

        // 4. Hapus Akun User Terkait (Opsional, tapi disarankan agar bersih)
        if (!empty($data['id_users'])) {
            $pdo->prepare("DELETE FROM users WHERE id_users = :uid")->execute(['uid' => $data['id_users']]);
        }

        // 5. Refresh View
        $pdo->query("REFRESH MATERIALIZED VIEW mv_dosen");

        $pdo->commit();
        $_SESSION['flash'] = "Dosen dan Akun User berhasil dihapus!";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash'] = "Gagal menghapus: " . $e->getMessage();
    }

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
    
    <style>
        /* === CSS UNTUK STICKY ACTION COLUMN === */
        
        /* Pastikan tabel tidak wrap teksnya agar scroll muncul */
        #dataTable th, #dataTable td {
            white-space: nowrap;
            vertical-align: middle !important;
            font-size: 0.9rem;
        }

        /* Atur lebar kolom minimal agar rapi */
        .col-foto { width: 60px; min-width: 60px; }
        .col-nama { min-width: 200px; }
        .col-identitas { min-width: 150px; }
        .col-jabatan { min-width: 180px; }
        .col-pendidikan { min-width: 250px; white-space: normal !important; } /* Allow wrap for long lists */
        .col-matkul { min-width: 200px; white-space: normal !important; }
        .col-kontak { min-width: 200px; }
        .col-links { min-width: 100px; text-align: center; }

        /* LOGIKA STICKY (KOLOM AKSI) */
        th.sticky-col, td.sticky-col {
            position: -webkit-sticky; /* Safari */
            position: sticky;
            right: 0;
            z-index: 10;
            background-color: #fff;
            border-left: 2px solid #e3e6f0;
            box-shadow: -5px 0 5px -5px rgba(0,0,0,0.2);
        }

        /* Perbaikan Warna Background saat Striped/Hover */
        .table-striped tbody tr:nth-of-type(odd) td.sticky-col { background-color: #f8f9fc !important; }
        .table-hover tbody tr:hover td.sticky-col { background-color: #eaecf4 !important; }
        thead th.sticky-col { background-color: #f8f9fc; z-index: 20; }
        
        /* Utility */
        .list-unstyled { margin-bottom: 0; }
        .badge-wrap { white-space: normal; display: inline-block; text-align: left;}
    </style>
</head>
<body id="page-top">

<div id="wrapper">

<?php
$role = $_SESSION['role'] ?? null;
// Counter code assumed present in sidebar.php
include __DIR__ . '/sidebar.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                        <span class="mr-2 text-gray-600 small">Halo, <b><?= htmlspecialchars($username) ?></b></span>
                        <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
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
            <p class="mb-4">Kelola data lengkap dosen peneliti (Profil, Pendidikan, Publikasi, dll).</p>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Dosen</h6>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahDosen">
                        <i class="fas fa-plus"></i> Tambah Dosen
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="col-foto">Foto</th>
                                    <th class="col-nama">Nama Lengkap</th>
                                    <th class="col-identitas">Identitas (NIP/NIDN)</th>
                                    <th class="col-jabatan">Jabatan & Prodi</th>
                                    <th class="col-pendidikan">Pendidikan & Sertifikasi</th>
                                    <th class="col-matkul">Mata Kuliah</th>
                                    <th class="col-kontak">Kontak</th>
                                    <th class="col-links">Links</th>
                                    <th class="text-center sticky-col" width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dosen as $d): ?>
                                <tr>
                                    <td class="text-center col-foto">
                                        <?php $foto = !empty($d['foto_dosen']) ? '../uploads/' . $d['foto_dosen'] : '../Asset/default_profile.jpg'; ?>
                                        <img src="<?= htmlspecialchars($foto) ?>" width="50" class="rounded" onerror="this.src='../Asset/default_profile.jpg'">
                                    </td>
                                    
                                    <td class="col-nama">
                                        <strong><?= htmlspecialchars($d['nama_dosen']) ?></strong>
                                        <?php if(!empty($d['bidang_riset'])): ?>
                                            <br><small class="text-info"><i class="fas fa-microscope"></i> <?= substr(htmlspecialchars($d['bidang_riset']), 0, 50) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="col-identitas">
                                        <small class="d-block"><b>NIP:</b> <?= htmlspecialchars($d['nip'] ?? '-') ?></small>
                                        <small class="d-block"><b>NIDN:</b> <?= htmlspecialchars($d['nidn_dosen'] ?? '-') ?></small>
                                    </td>
                                    
                                    <td class="col-jabatan">
                                        <div class="text-primary font-weight-bold"><?= htmlspecialchars($d['jabatan_dosen']) ?></div>
                                        <div class="text-muted"><?= htmlspecialchars($d['prodi_dosen']) ?></div>
                                    </td>
                                    
                                    <td class="col-pendidikan">
                                        <?php if(!empty($d['pendidikan_dosen'])): ?>
                                            <small class="d-block mb-1"><b>Edu:</b> <?= substr(htmlspecialchars($d['pendidikan_dosen']), 0, 80) ?>...</small>
                                        <?php endif; ?>
                                        <?php if(!empty($d['sertifikasi_dosen'])): ?>
                                            <small class="d-block text-success"><b>Cert:</b> <?= substr(htmlspecialchars($d['sertifikasi_dosen']), 0, 80) ?>...</small>
                                        <?php endif; ?>
                                    </td>

                                    <td class="col-matkul">
                                        <?php if(!empty($d['mata_kuliah_dosen'])): ?>
                                            <small><?= substr(htmlspecialchars($d['mata_kuliah_dosen']), 0, 100) ?>...</small>
                                        <?php else: ?> - <?php endif; ?>
                                    </td>

                                    <td class="col-kontak">
                                        <?php if(!empty($d['email_dosen'])): ?>
                                            <small class="d-block"><i class="fas fa-envelope"></i> <?= htmlspecialchars($d['email_dosen']) ?></small>
                                        <?php endif; ?>
                                        <?php if(!empty($d['alamat_kantor'])): ?>
                                            <small class="d-block"><i class="fas fa-map-marker-alt"></i> <?= substr(htmlspecialchars($d['alamat_kantor']), 0, 30) ?>...</small>
                                        <?php endif; ?>
                                    </td>

                                    <td class="col-links text-center">
                                        <?php if(!empty($d['link_linkedin'])): ?>
                                            <a href="<?= htmlspecialchars($d['link_linkedin']) ?>" target="_blank" class="text-primary mr-1" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                                        <?php endif; ?>
                                        <?php if(!empty($d['link_google_scholar'])): ?>
                                            <a href="<?= htmlspecialchars($d['link_google_scholar']) ?>" target="_blank" class="text-danger mr-1" title="Scholar"><i class="fas fa-graduation-cap"></i></a>
                                        <?php endif; ?>
                                        <?php if(!empty($d['link_sinta'])): ?>
                                            <a href="<?= htmlspecialchars($d['link_sinta']) ?>" target="_blank" class="text-warning" title="Sinta"><i class="fas fa-book"></i></a>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center sticky-col">
                                        <button class="btn btn-warning btn-sm btnEditDosen" 
                                                data-id="<?= $d['id_dosen'] ?>"
                                                data-nama="<?= htmlspecialchars($d['nama_dosen']) ?>"
                                                data-nip="<?= htmlspecialchars($d['nip']) ?>"
                                                data-nidn="<?= htmlspecialchars($d['nidn_dosen']) ?>"
                                                data-bidang="<?= htmlspecialchars($d['bidang_riset']) ?>"
                                                data-jabatan="<?= htmlspecialchars($d['jabatan_dosen']) ?>"
                                                data-prodi="<?= htmlspecialchars($d['prodi_dosen']) ?>"
                                                data-email="<?= htmlspecialchars($d['email_dosen']) ?>"
                                                data-alamat="<?= htmlspecialchars($d['alamat_kantor']) ?>"
                                                data-pendidikan="<?= htmlspecialchars($d['pendidikan_dosen']) ?>"
                                                data-sertifikasi="<?= htmlspecialchars($d['sertifikasi_dosen']) ?>"
                                                data-matkul="<?= htmlspecialchars($d['mata_kuliah_dosen']) ?>"
                                                data-linkedin="<?= htmlspecialchars($d['link_linkedin']) ?>"
                                                data-scholar="<?= htmlspecialchars($d['link_google_scholar']) ?>"
                                                data-sinta="<?= htmlspecialchars($d['link_sinta']) ?>"
                                                data-foto="<?= htmlspecialchars($d['foto_dosen']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="dosen.php?aksi=hapus&id=<?= $d['id_dosen'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">
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
    <footer class="sticky-footer bg-white"><div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; LAB IVSS</span></div></div></footer>
</div>
</div>

<div class="modal fade" id="modalTambahDosen" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Dosen Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="dosen_process.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                
                <ul class="nav nav-tabs mb-3" id="tabTambah" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab">Data Utama</a></li>
                    <li class="nav-item"><a class="nav-link" id="profile-tab" data-toggle="tab" href="#detail" role="tab">Detail Akademik</a></li>
                    <li class="nav-item"><a class="nav-link" id="contact-tab" data-toggle="tab" href="#sosmed" role="tab">Kontak & Sosmed</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Nama Lengkap</label><input type="text" name="nama_dosen" class="form-control" required></div>
                            <div class="col-md-6 form-group"><label>Jabatan</label><input type="text" name="jabatan_dosen" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>NIP</label><input type="text" name="nip" class="form-control"></div>
                            <div class="col-md-4 form-group"><label>NIDN</label><input type="text" name="nidn_dosen" class="form-control"></div>
                            <div class="col-md-4 form-group"><label>Prodi</label><input type="text" name="prodi_dosen" class="form-control"></div>
                        </div>
                        <div class="form-group"><label>Foto Dosen</label><input type="file" name="foto_dosen" class="form-control-file"></div>
                    </div>

                    <div class="tab-pane fade" id="detail" role="tabpanel">
                        <div class="form-group"><label>Bidang Riset</label><input type="text" name="bidang_riset" class="form-control" placeholder="Pisahkan dengan koma"></div>
                        <div class="form-group"><label>Pendidikan (Pisahkan koma)</label><textarea name="pendidikan_dosen" class="form-control" rows="2"></textarea></div>
                        <div class="form-group"><label>Sertifikasi (Pisahkan koma)</label><textarea name="sertifikasi_dosen" class="form-control" rows="2"></textarea></div>
                        <div class="form-group"><label>Mata Kuliah</label><textarea name="mata_kuliah_dosen" class="form-control" rows="2" placeholder="Format: Matkul A, Matkul B; Semester Ganjil"></textarea></div>
                    </div>

                    <div class="tab-pane fade" id="sosmed" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Email</label><input type="email" name="email_dosen" class="form-control"></div>
                            <div class="col-md-6 form-group"><label>Alamat Kantor</label><input type="text" name="alamat_kantor" class="form-control"></div>
                        </div>
                        <div class="form-group"><label>Link LinkedIn</label><input type="text" name="link_linkedin" class="form-control"></div>
                        <div class="form-group"><label>Link Google Scholar</label><input type="text" name="link_google_scholar" class="form-control"></div>
                        <div class="form-group"><label>Link Sinta</label><input type="text" name="link_sinta" class="form-control"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditDosen" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Data Dosen</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="dosen_process.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id_dosen" id="edit_id">

                <ul class="nav nav-tabs mb-3" id="tabEdit" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="home-edit-tab" data-toggle="tab" href="#home-edit" role="tab">Data Utama</a></li>
                    <li class="nav-item"><a class="nav-link" id="detail-edit-tab" data-toggle="tab" href="#detail-edit" role="tab">Detail Akademik</a></li>
                    <li class="nav-item"><a class="nav-link" id="contact-edit-tab" data-toggle="tab" href="#sosmed-edit" role="tab">Kontak & Sosmed</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="home-edit">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Nama Lengkap</label><input type="text" name="nama_dosen" id="edit_nama" class="form-control" required></div>
                            <div class="col-md-6 form-group"><label>Jabatan</label><input type="text" name="jabatan_dosen" id="edit_jabatan" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>NIP</label><input type="text" name="nip" id="edit_nip" class="form-control"></div>
                            <div class="col-md-4 form-group"><label>NIDN</label><input type="text" name="nidn_dosen" id="edit_nidn" class="form-control"></div>
                            <div class="col-md-4 form-group"><label>Prodi</label><input type="text" name="prodi_dosen" id="edit_prodi" class="form-control"></div>
                        </div>
                        <div class="form-group">
                            <label>Foto:</label><br>
                            <img id="edit_foto_preview" src="" width="80" class="rounded mb-2">
                            <input type="file" name="foto_dosen" class="form-control-file">
                        </div>
                    </div>

                    <div class="tab-pane fade" id="detail-edit">
                        <div class="form-group"><label>Bidang Riset</label><input type="text" name="bidang_riset" id="edit_bidang" class="form-control"></div>
                        <div class="form-group"><label>Pendidikan</label><textarea name="pendidikan_dosen" id="edit_pendidikan" class="form-control" rows="2"></textarea></div>
                        <div class="form-group"><label>Sertifikasi</label><textarea name="sertifikasi_dosen" id="edit_sertifikasi" class="form-control" rows="2"></textarea></div>
                        <div class="form-group"><label>Mata Kuliah</label><textarea name="mata_kuliah_dosen" id="edit_matkul" class="form-control" rows="2"></textarea></div>
                    </div>

                    <div class="tab-pane fade" id="sosmed-edit">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Email</label><input type="email" name="email_dosen" id="edit_email" class="form-control"></div>
                            <div class="col-md-6 form-group"><label>Alamat Kantor</label><input type="text" name="alamat_kantor" id="edit_alamat" class="form-control"></div>
                        </div>
                        <div class="form-group"><label>Link LinkedIn</label><input type="text" name="link_linkedin" id="edit_linkedin" class="form-control"></div>
                        <div class="form-group"><label>Link Google Scholar</label><input type="text" name="link_google_scholar" id="edit_scholar" class="form-control"></div>
                        <div class="form-group"><label>Link Sinta</label><input type="text" name="link_sinta" id="edit_sinta" class="form-control"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui Data</button>
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
        // Inisialisasi DataTable dengan opsi scrollX
        $('#dataTable').DataTable({
            "scrollX": true
        }); 
    });
    
    $(document).on('click', '.btnEditDosen', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_nama').val($(this).data('nama'));
        $('#edit_nip').val($(this).data('nip'));
        $('#edit_nidn').val($(this).data('nidn'));
        $('#edit_jabatan').val($(this).data('jabatan'));
        $('#edit_prodi').val($(this).data('prodi'));
        $('#edit_bidang').val($(this).data('bidang'));
        $('#edit_pendidikan').val($(this).data('pendidikan'));
        $('#edit_sertifikasi').val($(this).data('sertifikasi'));
        $('#edit_matkul').val($(this).data('matkul'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_alamat').val($(this).data('alamat'));
        $('#edit_linkedin').val($(this).data('linkedin'));
        $('#edit_scholar').val($(this).data('scholar'));
        $('#edit_sinta').val($(this).data('sinta'));

        let foto = $(this).data('foto');
        if (foto) { $('#edit_foto_preview').attr('src', '../uploads/' + foto); } 
        else { $('#edit_foto_preview').attr('src', '../Asset/default_profile.jpg'); }

        $('#modalEditDosen').modal('show');
    });
</script>

</body>
</html>