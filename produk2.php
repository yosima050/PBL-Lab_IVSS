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
    $tgl_mulai_fmt = !empty($data['tgl_mulai']) ? date('d M Y', strtotime($data['tgl_mulai'])) : 'DD/MM/YYYY';
    $tgl_selesai_fmt = !empty($data['tgl_selesai']) ? date('d M Y', strtotime($data['tgl_selesai'])) : 'DD/MM/YYYY';
    $kategori_fmt = htmlspecialchars($data['kategori'] ?? '[Kategori]');
    $lokasi_fmt = htmlspecialchars($data['lokasi'] ?? '[Info]');
    $tim_utama_fmt = htmlspecialchars($data['tim_utama'] ?? 'N/A');
    $tim_kedua_fmt = htmlspecialchars($data['tim_kedua'] ?? '');
    $deskripsi_fmt = nl2br(htmlspecialchars($data['deskripsi_proyek']));
    
    // Status (Asumsi menggunakan tahun proyek sebagai status sederhana)
    $status_proyek = "Aktif/Published/Progress"; // Hanya teks status

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
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .banner1 {
            min-height: 0;
            background: none;
        }
        .header-riset {
            background-color: #F9D723; /* Kuning */
            padding: 0.5rem 1rem;
            color: #212529; /* Hitam/Dark */
            font-weight: bold;
            font-size: 1.25rem;
            border-radius: 0.375rem;
            margin-bottom: 2rem;
            display: inline-block;
        }
        .content-card {
            background-color: #F5F9FF;
            border: 1px solid #dee2e6;
            padding: 2rem;
        }
        .image-placeholder {
            background-color: #f1f1f1; 
            height: 200px; 
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            border-radius: 0.375rem;
            font-size: 3rem;
            color: #6c757d;
            overflow: hidden;
            border: 1px solid #ced4da;
        }
        .deskripsi-box {
            border: 1px solid #0047AB;
            padding: 1.5rem;
            border-radius: 0.375rem;
            margin-bottom: 2rem;
            background-color: white;
            line-height: 1.8;
            font-size: 0.95rem;
            color: #343a40;
        }
        .detail-info-header, .dokumen-header {
            font-weight: bold;
            font-size: 1.2rem;
            color: #212529;
            margin-bottom: 0.5rem;
        }
        .detail-box {
            border: 1px solid #0047AB;
            padding: 1.5rem;
            border-radius: 0.375rem;
            background-color: white;
            color: #343a40;
            line-height: 2;
        }
        .detail-box p {
            margin-bottom: 0;
        }
        .dokumen-item {
            background-color: #FFFCED;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .download-link {
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
        }
        .download-link:hover {
             text-decoration: underline;
        }
        .back-link {
            color: #212529;
            font-weight: bold;
            text-decoration: none;
        }
        .back-link:hover {
            color: #0047AB;
        }
        .status-badge {
            background-color: #F9D723; /* Kuning Solid */
            color: #212529; /* Teks Hitam */
            padding: 0.3rem 1rem; /* Padding yang cukup */
            border-radius: 30px; /* Rounded pill shape */
            font-weight: 500;
            margin-bottom: 1.5rem;
            width: fit-content;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="banner1"> </div>

    <div class="container my-5">
        
        <div class="header-riset">
            Halaman Proyek dan Riset
        </div>

        <div class="card border-0 p-4 content-card shadow-sm">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="produk.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h2 class="h5 m-0 text-uppercase fw-bold text-end">
                    <?= htmlspecialchars($data['judul_proyek']); ?>
                </h2>
            </div>
            
            <div class="image-placeholder mb-4">
                <?php 
                    // Path Foto (Sesuaikan jika path di dashboard Anda berbeda)
                    $fotoPath = 'dashboard/uploads/proyek/' . $data['foto_proyek'];
                    if (!empty($data['foto_proyek']) && file_exists($fotoPath)): 
                ?>
                    <img src="<?= htmlspecialchars($fotoPath); ?>" alt="Foto Proyek" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-image"></i>
                <?php endif; ?>
            </div>
            
            <h3 class="h3 fw-bold mb-2 text-dark">[Judul Lengkap Item]</h3> 
            
            <span class="status-badge">
                Status: **<?= htmlspecialchars($status_proyek); ?>**
            </span>
            
            <h4 class="detail-info-header">Deskripsi</h4>
            <div class="deskripsi-box">
                <p class="m-0">
                    <?php if (!empty($deskripsi_fmt)): ?>
                        <?= $deskripsi_fmt; ?>
                    <?php else: ?>
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                    <?php endif; ?>
                </p>
            </div>

            <h4 class="detail-info-header">Informasi Detail</h4>
            <div class="detail-box mb-4">
                <p>Tanggal Mulai: **<?= $tgl_mulai_fmt; ?>**</p>
                <p>Tanggal Selesai: **<?= $tgl_selesai_fmt; ?>**</p>
                
                <p><?= $label_tim_utama; ?>: **<?= $tim_utama_fmt; ?>**</p>
                <?php if(!empty($tim_kedua_fmt)): ?>
                    <p><?= $label_tim_kedua; ?>: **<?= $tim_kedua_fmt; ?>**</p>
                <?php endif; ?>

                <p>Kategori: **<?= $kategori_fmt; ?>**</p>
                <p>Lokasi/Jurnal: **<?= $lokasi_fmt; ?>**</p>
            </div>
            
            <h4 class="dokumen-header">Dokumen Terkait</h4>
            <div class="dokumen-container mb-4">
                <?php 
                    // Path File (Sesuaikan jika path di dashboard Anda berbeda)
                    $filePath = 'dashboard/uploads/proyek/' . $data['file_proyek'];
                    
                    if (!empty($data['file_proyek']) && file_exists($filePath)): 
                ?>
                    <div class="dokumen-item">
                        <span class="fw-semibold"><?= htmlspecialchars(basename($data['file_proyek'])); ?></span>
                        <a href="<?= htmlspecialchars($filePath); ?>" class="download-link" download>
                            [Download]
                        </a>
                    </div>
                <?php else: ?>
                    <div class="dokumen-item justify-content-center">
                        <span class="text-muted"><i class="fas fa-exclamation-circle me-2"></i>Tidak ada dokumen yang dilampirkan.</span>
                    </div>
                    <div class="dokumen-item">
                        <span class="fw-semibold">Dokumen_1.pdf</span>
                        <a href="#" class="download-link">
                            [Download]
                        </a>
                    </div>
                     <div class="dokumen-item">
                        <span class="fw-semibold">Dokumen_2.pdf</span>
                        <a href="#" class="download-link">
                            [Download]
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>