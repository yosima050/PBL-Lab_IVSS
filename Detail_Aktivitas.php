<?php
include 'dashboard/db.php'; 


// 2. AMBIL ID DARI URL
$id_aktivitas = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // A. Query Data Utama Aktivitas
    $stmt = $pdo->prepare("SELECT * FROM public.aktivitas WHERE id_aktivitas = :id");
    $stmt->execute(['id' => $id_aktivitas]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data tidak ditemukan, redirect atau tampilkan pesan error
    if (!$data) {
        echo "<script>alert('Aktivitas tidak ditemukan!'); window.location='Aktivitas_Dokumen.php';</script>";
        exit;
    }

    // B. Query Galeri Tambahan (Foto-foto lain yang terkait)
    // Asumsi: Tabel 'galeri' punya kolom 'id_aktivitas' sebagai foreign key
    $stmt_galeri = $pdo->prepare("SELECT * FROM public.galeri WHERE id_aktivitas = :id ORDER BY created_at_galeri DESC");
    $stmt_galeri->execute(['id' => $id_aktivitas]);
    $galeri_list = $stmt_galeri->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['judul_aktivitas']); ?> - Detail Aktivitas</title>
    
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="dashboard/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">

    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .banner1 {
            background: url(Asset/Coba.jpg) no-repeat center center;
            background-size: cover;
            min-height: 200px;
        }
        
        /* Container Utama */
        .detail-container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-top: -50px; /* Efek overlap ke banner */
            position: relative;
            z-index: 10;
        }

        .activity-header h1 {
            font-weight: 700;
            color: #0047AB; /* Biru Polinema */
            margin-bottom: 15px;
        }

        .meta-info {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .meta-info i { margin-right: 8px; color: #F9D723; /* Kuning Icon */ }
        .meta-info span { margin-right: 20px; }

        /* Foto Utama */
        .main-image {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 30px;
            object-fit: cover;
            max-height: 500px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* Isi Konten */
        .activity-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
            margin-bottom: 40px;
        }

        /* Galeri Grid */
        .gallery-section h3 {
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            border-left: 5px solid #F9D723;
            padding-left: 15px;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            cursor: pointer;
            height: 150px;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        
        /* Tombol Kembali */
        .btn-back {
            background-color: #0047AB;
            color: white;
            border-radius: 50px;
            padding: 10px 25px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background-color: #003380;
            color: white;
            transform: translateX(-5px);
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="banner1"></div>

<div class="container pb-5">
    
    <div class="detail-container">
        
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                <li class="breadcrumb-item"><a href="Aktivitas_Dokumen.php">Aktivitas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>

        <div class="activity-header">
            <h1><?= htmlspecialchars($data['judul_aktivitas']); ?></h1>
            
            <div class="meta-info">
                <span>
                    <i class="far fa-calendar-alt"></i> 
                    <?= date('d F Y', strtotime($data['tanggal_mulai_aktivitas'])); ?>
                    <?php if($data['tanggal_selesai_aktivitas'] && $data['tanggal_selesai_aktivitas'] != $data['tanggal_mulai_aktivitas']): ?>
                        - <?= date('d F Y', strtotime($data['tanggal_selesai_aktivitas'])); ?>
                    <?php endif; ?>
                </span>
                <span>
                    <i class="fas fa-tag"></i> 
                    <?= !empty($data['tag_aktivitas']) ? htmlspecialchars($data['tag_aktivitas']) : 'Umum'; ?>
                </span>
            </div>
        </div>

        <?php 
            $mainFoto = 'uploads/' . $data['foto_galeri'];
            if (!empty($data['foto_galeri']) && file_exists($mainFoto)): 
        ?>
            <img src="<?= htmlspecialchars($mainFoto); ?>" class="main-image" alt="Foto Utama">
        <?php endif; ?>

        <div class="activity-content">
            <?= nl2br(htmlspecialchars($data['isi_aktivitas'])); ?>
        </div>

        <?php if (count($galeri_list) > 0): ?>
            <div class="gallery-section">
                <h3>Dokumentasi Lainnya</h3>
                <div class="gallery-grid">
                    <?php foreach ($galeri_list as $img): ?>
                        <?php 
                            $galeriPath = 'uploads/' . $img['foto_galeri'];
                            if (file_exists($galeriPath)):
                        ?>
                        <div class="gallery-item">
                            <img src="<?= htmlspecialchars($galeriPath); ?>" 
                                 alt="<?= htmlspecialchars($img['judul_foto']); ?>" 
                                 onclick="window.open(this.src, '_blank')">
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-5 border-top pt-4">
            <a href="Aktivitas_Dokumen.php" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Aktivitas
            </a>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.js"></script>

</body>
</html>