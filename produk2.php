<?php
include 'dashboard/db.php'; 

// 1. AMBIL ID DARI URL
$id_proyek = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // A. CEK KEPEMILIKAN PROYEK (Dosen atau Mahasiswa)
    $stmtCheck = $pdo->prepare("SELECT id_dosen, id_mahasiswa FROM proyek WHERE id_proyek = :id");
    $stmtCheck->execute(['id' => $id_proyek]);
    $check = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$check) {
        die("<div class='container py-5 text-center'><h3>Proyek tidak ditemukan.</h3><a href='produk.php' class='btn btn-primary'>Kembali</a></div>");
    }

    // Variabel Default
    $label_tim_utama = "Tim Penulis";
    $label_tim_kedua = ""; // Bisa jadi Asisten atau Pembimbing
    $data_tim_kedua  = ""; 

    // B. LOGIKA QUERY BERDASARKAN PEMILIK
    
    // --- KASUS 1: PROYEK DOSEN (Ada Asisten Mahasiswa) ---
    // Cek apakah id_dosen di tabel PROYEK terisi (sebagai ketua)
    if (!empty($check['id_dosen'])) {
        $label_tim_utama = "Tim Dosen";
        $label_tim_kedua = "Asisten Mahasiswa";

        $sql = "SELECT 
                    p.*,
                    -- Tim Utama (Dosen)
                    STRING_AGG(DISTINCT d.nama_dosen, ', ') as tim_utama,
                    -- Tim Kedua (Mahasiswa Asisten)
                    STRING_AGG(DISTINCT u.nama_users, ', ') as tim_kedua,
                    
                    MAX(dd.tanggal_mulai_proyek_dosen) as tgl_mulai,
                    MAX(dd.tanggal_selesai_proyek_dosen) as tgl_selesai,
                    MAX(dd.kategori_proyek_dosen) as kategori,
                    MAX(dd.lokasi_proyek_dosen) as lokasi
                FROM proyek p
                JOIN detail_proyek_dosen dd ON p.id_proyek = dd.id_proyek
                LEFT JOIN dosen d ON dd.id_dosen = d.id_dosen
                -- Join ke Asisten (Detail Mahasiswa)
                LEFT JOIN detail_proyek_mahasiswa dm ON p.id_proyek = dm.id_proyek
                LEFT JOIN mahasiswa m ON dm.id_mahasiswa = m.id_mahasiswa
                LEFT JOIN users u ON m.id_users = u.id_users
                WHERE p.id_proyek = :id
                GROUP BY p.id_proyek";

    } else {
        // --- KASUS 2: PROYEK MAHASISWA (Ada Dosen Pembimbing) ---
        $label_tim_utama = "Tim Mahasiswa";
        $label_tim_kedua = "Dosen Pembimbing";

        $sql = "SELECT 
                    p.*,
                    -- Tim Utama (Mahasiswa)
                    STRING_AGG(DISTINCT u.nama_users, ', ') as tim_utama,
                    -- Tim Kedua (Dosen Pembimbing dari tabel Proyek)
                    dbimbing.nama_dosen as tim_kedua,
                    
                    MAX(dm.tanggal_mulai_proyek_mahasiswa) as tgl_mulai,
                    MAX(dm.tanggal_selesai_proyek_mahasiswa) as tgl_selesai,
                    MAX(dm.kategori_proyek_mahasiswa) as kategori,
                    MAX(dm.lokasi_proyek_mahasiswa) as lokasi
                FROM proyek p
                JOIN detail_proyek_mahasiswa dm ON p.id_proyek = dm.id_proyek
                LEFT JOIN mahasiswa m ON dm.id_mahasiswa = m.id_mahasiswa
                LEFT JOIN users u ON m.id_users = u.id_users
                -- Join Pembimbing
                LEFT JOIN dosen dbimbing ON p.id_dosen = dbimbing.id_dosen
                WHERE p.id_proyek = :id
                GROUP BY p.id_proyek, dbimbing.nama_dosen";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_proyek]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("<div class='container py-5 text-center'><h3>Detail data tidak lengkap/rusak.</h3><a href='produk.php' class='btn btn-primary'>Kembali</a></div>");
    }

    // Format Tanggal
    $tgl_mulai_fmt = !empty($data['tgl_mulai']) ? date('d M Y', strtotime($data['tgl_mulai'])) : '-';
    $tgl_selesai_fmt = !empty($data['tgl_selesai']) ? date('d M Y', strtotime($data['tgl_selesai'])) : '-';

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk Riset - <?= htmlspecialchars($data['judul_proyek']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .banner1{
            background:url(Asset/Coba.jpg) no-repeat 0px 0px;
            background-size:cover;
            min-height:250px;
        }
        .header-riset {
            background-color: #F9D723;
            padding: 0.3rem 1rem;
            color: #0047AB;
            font-weight: bold;
            font-size: 1.10rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            display: inline-block;
        }
        .content-card {
            background-color: #F5F9FF !important;
        }
        .detail-box {
            border: 2px solid #0047AB;
            padding: 1rem;
            border-radius: 0.375rem;
            /* MODIFIKASI: Mengganti background-color: transparent menjadi white */
            background-color: white; 
        }
        /* Style tabel detail agar lebih rapi */
        .detail-box table tr td {
            padding: 8px 0;
            vertical-align: top;
        }
        .deskripsi-box {
            border: 2px solid #0047AB;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            background-color: white;
        }
        .image-placeholder {
            background-color: #e9ecef;
            height: 300px; /* Sedikit dipertinggi */
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            border-radius: 0.375rem;
            font-size: 3rem;
            color: #6c757d;
            overflow: hidden;
        }
        .bg-custom-blue { background-color: #0047AB !important; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="banner1"> </div>

    <div class="container my-5">
        
        <div class="header-riset">
            Halaman Produk dan Riset
        </div>

        <div class="card border-0 p-4 content-card shadow-sm">
            
            <div class="d-flex align-items-center mb-4">
                <a href="produk.php" class="text-decoration-none me-3 text-dark">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h2 class="h5 m-0 text-muted ms-auto text-uppercase fw-bold">
                    <?= htmlspecialchars($data['tipe_proyek']); ?>
                </h2>
            </div>
            
            <div class="image-placeholder mb-4 shadow-sm">
                <?php 
                    // Path Foto (Sesuaikan jika path di dashboard Anda berbeda)
                    $fotoPath = 'dashboard/uploads/proyek/' . $data['foto_proyek'];
                    if (!empty($data['foto_proyek']) && file_exists($fotoPath)): 
                ?>
                    <img src="<?= htmlspecialchars($fotoPath); ?>" alt="Foto Proyek" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div class="text-center">
                        <i class="fas fa-image fa-2x mb-2"></i><br>
                        <span class="fs-6">Tidak ada foto</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <h3 class="h3 fw-bold mb-2 text-dark"><?= htmlspecialchars($data['judul_proyek']); ?></h3>
            <p class="text-muted mb-4"><i class="far fa-calendar-alt me-1"></i> Tahun Proyek: <strong><?= htmlspecialchars($data['tahun_proyek']); ?></strong></p>
            
            <h4 class="h5 fw-bold text-primary"><i class="fas fa-align-left me-2"></i>Deskripsi</h4>
            <div class="deskripsi-box shadow-sm">
                <p class="text-secondary m-0" style="text-align: justify; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($data['deskripsi_proyek'])); ?>
                </p>
            </div>

            <hr class="my-4">
            
            <h4 class="h5 fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Informasi Detail</h4>
            <div class="detail-box mb-4"> <table class="table table-borderless m-0 bg-transparent">
                    <tr>
                        <td width="180" class="fw-bold text-secondary">Tipe Proyek</td>
                        <td>: <span class="badge bg-custom-blue"><?= htmlspecialchars($data['tipe_proyek']); ?></span></td>
                    </tr>
                    
                    <tr>
                        <td class="fw-bold text-secondary">Tahun Pelaksanaan</td>
                        <td>: <?= htmlspecialchars($data['tahun_proyek']); ?></td>
                    </tr>

                    <tr>
                        <td class="fw-bold text-secondary"><?= $label_tim_utama; ?></td>
                        <td>: 
                            <span class="fw-semibold text-dark">
                                <?= htmlspecialchars($data['tim_utama'] ?? '-'); ?>
                            </span>
                        </td>
                    </tr>

                    <?php if(!empty($data['tim_kedua'])): ?>
                    <tr>
                        <td class="fw-bold text-secondary"><?= $label_tim_kedua; ?></td>
                        <td>: 
                            <span class="text-primary">
                                <?= htmlspecialchars($data['tim_kedua']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <td class="fw-bold text-secondary">Kategori</td>
                        <td>: <?= htmlspecialchars($data['kategori'] ?? '-'); ?></td>
                    </tr>

                    <?php if (!empty($data['lokasi']) && $data['lokasi'] != '-'): ?>
                    <tr>
                        <td class="fw-bold text-secondary">Lokasi / Jurnal</td>
                        <td>: <?= htmlspecialchars($data['lokasi']); ?></td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <td class="fw-bold text-secondary">Durasi Proyek</td>
                        <td>: <?= $tgl_mulai_fmt; ?> <span class="mx-1 text-muted">s/d</span> <?= $tgl_selesai_fmt; ?></td>
                    </tr>
                </table>
            </div>
            
            <h4 class="h5 fw-bold text-primary"><i class="fas fa-paperclip me-2"></i>Dokumen Terkait</h4>
            <div class="p-3 mb-4 rounded shadow-sm" style="background-color: #FFFCED;"> <div class="list-group list-group-flush bg-transparent">
                <?php 
                    // Path File (Sesuaikan jika path di dashboard Anda berbeda)
                    $filePath = 'dashboard/uploads/proyek/' . $data['file_proyek'];
                    
                    if (!empty($data['file_proyek']) && file_exists($filePath)): 
                ?>
                    <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf fa-2x me-3 text-danger"></i>
                            <div>
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars(basename($data['file_proyek'])); ?></h6>
                                <small class="text-muted">Klik tombol di kanan untuk mengunduh.</small>
                            </div>
                        </div>
                        <a href="<?= htmlspecialchars($filePath); ?>" class="btn btn-primary btn-sm px-3" download>
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted"><i class="fas fa-exclamation-circle me-2"></i>Tidak ada dokumen yang dilampirkan.</span>
                    </div>
                <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>