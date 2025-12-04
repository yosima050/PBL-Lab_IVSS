<?php
include 'dashboard/db.php'; 


// 2. AMBIL ID DARI URL
$id_proyek = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Query menggabungkan tabel proyek dengan detail_dosen dan detail_mahasiswa
    $sql = "SELECT 
                p.*,
                dd.tanggal_mulai_proyek_dosen, dd.tanggal_selesai_proyek_dosen, 
                dd.nama_penulis_proyek_dosen, dd.kategori_proyek_dosen, dd.lokasi_proyek_dosen,
                dm.tanggal_mulai_proyek_mahasiswa, dm.tanggal_selesai_proyek_mahasiswa, 
                dm.nama_penulis_proyek_mahasiswa, dm.kategori_proyek_mahasiswa, dm.lokasi_proyek_mahasiswa
            FROM public.proyek p
            LEFT JOIN public.detail_proyek_dosen dd ON p.id_proyek = dd.id_proyek
            LEFT JOIN public.detail_proyek_mahasiswa dm ON p.id_proyek = dm.id_proyek
            WHERE p.id_proyek = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_proyek]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("<div class='container py-5 text-center'><h3>Proyek tidak ditemukan.</h3><a href='produk.php' class='btn btn-primary'>Kembali</a></div>");
    }

    // 3. MAPPING DATA (Logika untuk menentukan data mana yang dipakai)
    // Cek apakah data ada di tabel detail_dosen atau detail_mahasiswa
    if (!empty($data['nama_penulis_proyek_dosen'])) {
        // Data dari Dosen
        $tgl_mulai   = $data['tanggal_mulai_proyek_dosen'];
        $tgl_selesai = $data['tanggal_selesai_proyek_dosen'];
        $penulis     = $data['nama_penulis_proyek_dosen'];
        $kategori    = $data['kategori_proyek_dosen'];
        $lokasi      = $data['lokasi_proyek_dosen'];
    } else {
        // Data dari Mahasiswa (Default)
        $tgl_mulai   = $data['tanggal_mulai_proyek_mahasiswa'];
        $tgl_selesai = $data['tanggal_selesai_proyek_mahasiswa'];
        $penulis     = $data['nama_penulis_proyek_mahasiswa'];
        $kategori    = $data['kategori_proyek_mahasiswa'];
        $lokasi      = $data['lokasi_proyek_mahasiswa'];
    }

    // Format Tanggal agar cantik
    $tgl_mulai_fmt = $tgl_mulai ? date('d M Y', strtotime($tgl_mulai)) : '-';
    $tgl_selesai_fmt = $tgl_selesai ? date('d M Y', strtotime($tgl_selesai)) : '-';

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk Riset</title>
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
            background-color: transparent;
        }
        .deskripsi-box {
            border: 2px solid #0047AB;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            background-color: white;
        }
        .dokumen-container {
            background-color: #FFFCED;
            padding: 1rem;
            border-radius: 0.375rem;
        }
        .image-placeholder {
            background-color: #e9ecef;
            height: 200px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            border-radius: 0.375rem;
            font-size: 3rem;
            color: #6c757d;
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
                <h2 class="h5 m-0 text-muted ms-auto">
                    <?= htmlspecialchars($data['tipe_proyek']); ?>
                </h2>
            </div>
            
            <div class="image-placeholder mb-4">
                <i class="fas fa-file-alt"></i>
            </div>
            
            <h3 class="h4 fw-bold mb-3"><?= htmlspecialchars($data['judul_proyek']); ?></h3>
            
            <p class="mb-4">
                <span class="badge bg-custom-blue text-uppercase">
                    <?= htmlspecialchars($data['tipe_proyek']); ?>
                </span>
                <span class="badge bg-secondary ms-2">
                    Tahun: <?= htmlspecialchars($data['tahun_proyek']); ?>
                </span>
            </p>
            
            <h4 class="h5">Deskripsi</h4>
            <div class="deskripsi-box">
                <p class="text-secondary m-0" style="text-align: justify; line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($data['deskripsi_proyek'])); ?>
                </p>
            </div>

            <hr class="my-4">
            
            <h4 class="h5">Informasi Detail</h4>
            <div class="detail-box mb-4">
                <table class="table table-borderless m-0 bg-transparent">
                    <tr>
                        <td width="150"><strong>Penulis/Tim</strong></td>
                        <td>: <?= htmlspecialchars($penulis ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Kategori</strong></td>
                        <td>: <?= htmlspecialchars($kategori ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Mulai</strong></td>
                        <td>: <?= $tgl_mulai_fmt; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Selesai</strong></td>
                        <td>: <?= $tgl_selesai_fmt; ?></td>
                    </tr>
                    <?php if (!empty($lokasi) && $lokasi != '-'): ?>
                    <tr>
                        <td><strong>Lokasi/Jurnal</strong></td>
                        <td>: <?= htmlspecialchars($lokasi); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <h4 class="h5">Dokumen Terkait</h4>
            <div class="dokumen-container mb-4">
                <div class="list-group list-group-flush bg-transparent">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span><i class="fas fa-file-pdf me-2 text-danger"></i>Laporan_Proyek.pdf</span>
                        <button class="btn btn-sm btn-outline-secondary" disabled>
                            [File Tidak Tersedia]
                        </button>
                    </div>
                </div>
                <small class="text-muted fst-italic">*Dokumen belum diupload ke database.</small>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>