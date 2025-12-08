<?php
include 'dashboard/db.php'; 

// 1. AMBIL ID DARI URL
$id_aktivitas = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // A. Query Data Utama Aktivitas
    $stmt = $pdo->prepare("SELECT * FROM public.aktivitas WHERE id_aktivitas = :id");
    $stmt->execute(['id' => $id_aktivitas]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("<script>alert('Aktivitas tidak ditemukan!'); window.location='Aktivitas_Dokumen.php';</script>");
    }

    // B. Query Galeri
    $stmt_galeri = $pdo->prepare("SELECT * FROM public.galeri WHERE id_aktivitas = :id ORDER BY created_at_galeri DESC");
    $stmt_galeri->execute(['id' => $id_aktivitas]);
    $galeri_list = $stmt_galeri->fetchAll(PDO::FETCH_ASSOC);
    
    // C. Hitung Statistik Media (Untuk Badge Filter)
    $total_media = count($galeri_list);
    $count_foto = 0;
    $count_video = 0;

    foreach ($galeri_list as $chk) {
        if (!empty($chk['video_galeri'])) {
            $count_video++;
        } elseif (!empty($chk['foto_galeri'])) {
            $count_foto++;
        }
    }

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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">

    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #fff; }
        
        .banner1 {
            background: url(Asset/Coba.jpg) no-repeat center center;
            background-size: cover;
            min-height: 250px; 
        }
        
        /* 1. JUDUL KUNING */
        .title-box {
            background-color: #F9D723;
            padding: 15px 30px;
            border-radius: 10px;
            text-align: center;
            margin: -30px auto 40px auto;
            position: relative;
            width: fit-content;
            min-width: 50%;
            max-width: 90%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .title-box h1 {
            font-size: 1.5rem; font-weight: 700; margin: 0; color: #000;
        }

        /* 2. BOX INFORMASI */
        .info-box {
            background-color: #F5F7FA;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 50px;
        }
        .info-section { margin-bottom: 25px; }
        .info-section:last-child { margin-bottom: 0; }
        .info-label {
            font-size: 1.2rem; font-weight: 500; color: #333; margin-bottom: 10px; display: block;
        }
        .info-content {
            font-size: 1rem; color: #555; line-height: 1.6; text-align: justify;
        }

        /* 3. GALERI MEDIA */
        .gallery-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;
        }
        .gallery-title { font-size: 1.25rem; font-weight: 700; color: #000; }

        /* Filter Badges */
        .media-filters .badge {
            font-weight: 500; 
            padding: 8px 15px; 
            font-size: 0.9rem; 
            margin-left: 5px; 
            cursor: pointer; 
            color: #333;
            border: 1px solid transparent;
            transition: all 0.3s;
        }
        
        /* State Warna Filter */
        .badge-filter.active {
            background-color: #FFF176; /* Kuning Gelap */
            border-color: #F9D723;
            font-weight: bold;
        }
        .badge-filter.inactive {
            background-color: #FFF9C4; /* Kuning Terang */
        }
        .badge-filter:hover {
            background-color: #FFF59D;
        }

        /* Grid Kartu Media */
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .media-card {
            border: 2px solid #0047AB;
            border-radius: 10px;
            overflow: hidden;
            background: white;
            padding: 10px;
            text-align: center;
            transition: transform 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 250px; 
        }
        
        .media-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,71,171,0.2);
        }

        /* Container Media */
        .media-wrapper {
            width: 100%;
            height: 180px;
            border-radius: 5px;
            margin-bottom: 10px;
            overflow: hidden;
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .media-content {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .media-caption {
            font-size: 0.9rem;
            color: #333;
            margin-top: auto;
            width: 100%;
            text-align: center;
            font-weight: 500;
        }

        .media-icon { font-size: 4rem; color: #757575; margin-bottom: 10px; }
        
        /* Helper untuk JS Filter */
        .hidden-item { display: none; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="banner1"></div>

<div class="container pb-5">
    
    <div class="title-box">
        <h1><?= htmlspecialchars($data['judul_aktivitas']); ?></h1>
    </div>

    <h3 class="mb-3 fw-bold">Informasi Aktifitas</h3>
    
    <div class="info-box">
        
        <div class="info-section">
            <span class="info-label">Deskripsi</span>
            <div class="info-content">
                <?= nl2br(htmlspecialchars($data['isi_aktivitas'])); ?>
            </div>
        </div>

        <div class="info-section mt-4">
            <span class="info-label">Kategori / Tag</span>
            <div class="info-content">
                <span class="badge bg-primary fs-6" style="background-color: #0047AB !important;">
                    <?= !empty($data['tag_aktivitas']) ? htmlspecialchars($data['tag_aktivitas']) : 'Umum'; ?>
                </span>
            </div>
        </div>

        <div class="info-section mt-4">
            <span class="info-label">Waktu Pelaksanaan</span>
            <div class="info-content">
                <i class="far fa-calendar-alt text-warning me-2"></i>
                <?= date('d F Y', strtotime($data['tanggal_mulai_aktivitas'])); ?>
                <?php if($data['tanggal_selesai_aktivitas'] && $data['tanggal_selesai_aktivitas'] != $data['tanggal_mulai_aktivitas']): ?>
                     - <?= date('d F Y', strtotime($data['tanggal_selesai_aktivitas'])); ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="gallery-header">
        <div class="gallery-title">Galeri Media (<?= $total_media; ?>)</div>
        
        <div class="media-filters">
            <span class="badge badge-filter active rounded-pill text-dark" onclick="filterMedia('all', this)">
                Semua (<?= $total_media; ?>)
            </span>
            
            <span class="badge badge-filter inactive rounded-pill text-dark" onclick="filterMedia('image', this)">
                <i class="fas fa-image me-1"></i> Foto (<?= $count_foto; ?>)
            </span>
            
            <span class="badge badge-filter inactive rounded-pill text-dark" onclick="filterMedia('video', this)">
                <i class="fas fa-video me-1"></i> Video (<?= $count_video; ?>)
            </span>
        </div>
    </div>

    <div class="media-grid" id="galleryContainer">
        <?php if ($total_media > 0): ?>
            <?php foreach ($galeri_list as $item): ?>
                <?php 
                    $src = '';
                    $type = 'unknown'; 
                    
                    // 1. CEK VIDEO
                    if (!empty($item['video_galeri'])) {
                        $f = $item['video_galeri'];
                        if (file_exists('uploads/' . $f)) { $src = 'uploads/' . $f; } 
                        elseif (file_exists('dashboard/uploads/' . $f)) { $src = 'dashboard/uploads/' . $f; }
                        
                        if ($src) $type = 'video';
                    }

                    // 2. CEK FOTO (Jika bukan video)
                    if ($type == 'unknown' && !empty($item['foto_galeri'])) {
                        $f = $item['foto_galeri'];
                        if (file_exists('uploads/' . $f)) { $src = 'uploads/' . $f; } 
                        elseif (file_exists('dashboard/uploads/' . $f)) { $src = 'dashboard/uploads/' . $f; }
                        
                        if ($src) $type = 'image';
                    }
                ?>
                
                <div class="media-card filter-item" data-type="<?= $type ?>" 
                     <?php if ($type == 'image'): ?> onclick="window.open('<?= htmlspecialchars($src); ?>', '_blank')" <?php endif; ?> >
                    
                    <div class="media-wrapper">
                        <?php if ($type == 'video'): ?>
                            <video controls class="media-content">
                                <source src="<?= htmlspecialchars($src); ?>" type="video/mp4">
                                Browser Anda tidak mendukung tag video.
                            </video>
                        <?php elseif ($type == 'image'): ?>
                            <img src="<?= htmlspecialchars($src); ?>" class="media-content" alt="Foto Galeri">
                        <?php else: ?>
                            <div class="text-warning text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>File Hilang
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="media-caption">
                        <?= htmlspecialchars($item['judul_foto'] ?? 'Dokumentasi'); ?>
                        <?php if($type == 'video'): ?> <i class="fas fa-video ms-1 text-primary"></i> <?php endif; ?>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="media-card">
                <i class="fas fa-images media-icon"></i>
                <div class="media-caption">Belum ada dokumentasi</div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="mt-5">
         <a href="Aktivitas_Dokumen.php" class="text-decoration-none text-muted fw-bold">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
         </a>
    </div>

</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function filterMedia(category, btnElement) {
    // 1. Reset tombol badge
    var badges = document.getElementsByClassName('badge-filter');
    for (var i = 0; i < badges.length; i++) {
        badges[i].classList.remove('active');
        badges[i].classList.add('inactive');
    }
    // 2. Aktifkan tombol yang diklik
    btnElement.classList.remove('inactive');
    btnElement.classList.add('active');

    // 3. Filter Item Grid
    var items = document.getElementsByClassName('filter-item');
    for (var j = 0; j < items.length; j++) {
        var itemType = items[j].getAttribute('data-type');
        
        if (category === 'all') {
            items[j].classList.remove('hidden-item');
        } else if (category === itemType) {
            items[j].classList.remove('hidden-item');
        } else {
            items[j].classList.add('hidden-item');
        }
    }
}
</script>

</body>
</html>