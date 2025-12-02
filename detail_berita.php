<?php
// 1. HUBUNGKAN KE DATABASE
include 'dashboard/db.php'; 

// 2. AMBIL ID DARI URL
$id_berita = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // ---------------------------------------------------------
    // FITUR: UPDATE VIEW COUNTER (Setiap kali dibuka, views +1)
    // ---------------------------------------------------------
    if ($id_berita > 0) {
        $stmt_views = $pdo->prepare("UPDATE public.berita SET views = views + 1 WHERE id_berita = :id");
        $stmt_views->execute(['id' => $id_berita]);
    }

    // ---------------------------------------------------------
    // QUERY UTAMA: AMBIL DATA BERITA SESUAI ID
    // ---------------------------------------------------------
    $stmt = $pdo->prepare("SELECT * FROM public.berita WHERE id_berita = :id");
    $stmt->execute(['id' => $id_berita]);
    $berita = $stmt->fetch();

    // Jika berita tidak ditemukan, redirect atau tampilkan pesan
    if (!$berita) {
        die("<div class='container py-5 text-center'><h3>Berita tidak ditemukan.</h3><a href='Berita_Pengumuman.php' class='btn btn-primary'>Kembali</a></div>");
    }

    // ---------------------------------------------------------
    // QUERY SIDEBAR: BERITA TERBARU LAINNYA (Kecuali berita ini)
    // ---------------------------------------------------------
    $stmt_sidebar = $pdo->prepare("SELECT id_berita, judul_berita, foto_berita, created_at_berita FROM public.berita WHERE id_berita != :id ORDER BY created_at_berita DESC LIMIT 5");
    $stmt_sidebar->execute(['id' => $id_berita]);
    $sidebar_news = $stmt_sidebar->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($berita['judul_berita']); ?> - Lab IVSS</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">

    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .banner1 {
            background: url(Asset/Coba.jpg) no-repeat center center;
            background-size: cover;
            min-height: 200px;
        }
        
        /* Konten Utama */
        .detail-content {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .news-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .news-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .news-meta i { margin-right: 5px; color: #0047AB; }
        .news-meta span { margin-right: 15px; }

        .news-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
            object-fit: cover;
        }
        .image-caption {
            font-size: 0.85rem;
            color: #666;
            text-align: center;
            margin-bottom: 25px;
            font-style: italic;
        }
        .news-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }

        /* Sidebar Styling */
        .sidebar-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .sidebar-header {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0047AB;
            margin-bottom: 15px;
            border-left: 4px solid #F9D723;
            padding-left: 10px;
        }
        .mini-news-item {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 15px;
        }
        .mini-news-item:last-child { border-bottom: none; }
        .mini-thumb {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            flex-shrink: 0;
        }
        .mini-title {
            font-size: 0.9rem;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 5px;
        }
        .mini-title a { text-decoration: none; color: #333; }
        .mini-title a:hover { color: #0047AB; }
        .mini-date { font-size: 0.75rem; color: #999; }
        
        /* Kategori Badge */
        .badge-kategori {
            background-color: #0047AB;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            text-decoration: none;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="banner1"></div>

<div class="container py-5">
    
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
            <li class="breadcrumb-item"><a href="Berita_Pengumuman.php">Berita & Pengumuman</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <article class="detail-content">
                
                <span class="badge-kategori">
                    <?= !empty($berita['kategori']) ? htmlspecialchars($berita['kategori']) : 'Berita'; ?>
                </span>

                <h1 class="news-title"><?= htmlspecialchars($berita['judul_berita']); ?></h1>

                <div class="news-meta">
                    <span><i class="far fa-calendar-alt"></i> <?= date('d F Y', strtotime($berita['created_at_berita'])); ?></span>
                    <span><i class="far fa-user"></i> <?= htmlspecialchars($berita['author']); ?></span>
                    <span><i class="far fa-eye"></i> <?= $berita['views']; ?>x dilihat</span>
                </div>

                <img src="<?= htmlspecialchars($berita['foto_berita']); ?>" 
                     class="news-image" 
                     alt="Gambar Berita" 
                     onerror="this.src='Asset/default_news.png';">
                
                <?php if(!empty($berita['caption_foto'])): ?>
                    <div class="image-caption"><?= htmlspecialchars($berita['caption_foto']); ?></div>
                <?php endif; ?>

                <div class="news-body">
                    <?= nl2br(htmlspecialchars($berita['isi_berita'])); ?>
                </div>

                <div class="mt-5 pt-3 border-top">
                    <p class="fw-bold">Bagikan:</p>
                    <a href="#" class="btn btn-sm btn-outline-primary me-2"><i class="fab fa-facebook-f"></i> Facebook</a>
                    <a href="#" class="btn btn-sm btn-outline-info me-2"><i class="fab fa-twitter"></i> Twitter</a>
                    <a href="#" class="btn btn-sm btn-success me-2"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                </div>

            </article>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-box">
                <div class="sidebar-header">Berita Lainnya</div>
                
                <?php if (count($sidebar_news) > 0): ?>
                    <?php foreach ($sidebar_news as $item): ?>
                    <div class="mini-news-item">
                        <img src="<?= htmlspecialchars($item['foto_berita']); ?>" 
                             class="mini-thumb" 
                             alt="thumb"
                             onerror="this.src='Asset/default_news.png';">
                        <div>
                            <div class="mini-title">
                                <a href="detail_Berita_Pengumuman.php?id=<?= $item['id_berita']; ?>">
                                    <?= htmlspecialchars($item['judul_berita']); ?>
                                </a>
                            </div>
                            <div class="mini-date">
                                <i class="far fa-clock"></i> <?= date('d M Y', strtotime($item['created_at_berita'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small">Tidak ada berita lain.</p>
                <?php endif; ?>

            </div>

            <div class="sidebar-box mt-4">
                <div class="sidebar-header">Arsip</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="Berita_Pengumuman.php" class="text-decoration-none">Semua Berita</a></li>
                    </ul>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>