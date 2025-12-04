<?php
// 1. HUBUNGKAN KE DATABASE
include 'dashboard/db.php'; 

// 2. AMBIL ID DARI URL
$id_berita = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // A. UPDATE VIEWS (Opsional jika kolom views ada)
    // $stmt_views = $pdo->prepare("UPDATE berita SET views = views + 1 WHERE id_berita = :id");
    // $stmt_views->execute(['id' => $id_berita]);

    // B. AMBIL DATA BERITA UTAMA
    $stmt = $pdo->prepare("SELECT * FROM public.berita WHERE id_berita = :id");
    $stmt->execute(['id' => $id_berita]);
    $berita = $stmt->fetch();

    if (!$berita) {
        die("<div class='container py-5 text-center'><h3>Berita tidak ditemukan.</h3><a href='Berita_Pengumuman.php' class='btn btn-primary'>Kembali</a></div>");
    }

    // C. SIDEBAR BERITA TERBARU LAINNYA
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
        .news-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
            object-fit: cover;
        }
        .news-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }
        .sidebar-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .mini-news-item {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 15px;
        }
        .mini-thumb {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            flex-shrink: 0;
        }
        .badge-kategori {
            background-color: #0047AB;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
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
                    <?= htmlspecialchars($berita['kategori_berita'] ?? ''); ?>
                </span>

                <h1 class="news-title"><?= htmlspecialchars($berita['judul_berita']); ?></h1>

                <div class="news-meta">
                    <span class="me-3"><i class="far fa-calendar-alt"></i> <?= date('d F Y', strtotime($berita['created_at_berita'])); ?></span>
                    <span class="me-3"><i class="far fa-user"></i> <?= htmlspecialchars($berita['author']); ?></span>
                </div>

                <img src="uploads/<?= htmlspecialchars($berita['foto_berita']); ?>" 
                     class="news-image" 
                     alt="Gambar Berita" 
                     onerror="this.src='Asset/default_news.png';">
                
                <div class="news-body">
                    <?= nl2br(htmlspecialchars($berita['isi_berita'])); ?>
                </div>

                <?php if (!empty($berita['link_berita'])): ?>
                <div class="mt-4 p-3 bg-light border rounded">
                    <strong>Tautan Terkait:</strong><br>
                    <a href="<?= htmlspecialchars($berita['link_berita']); ?>" target="_blank" class="text-break">
                        <?= htmlspecialchars($berita['link_berita']); ?> <i class="fas fa-external-link-alt small"></i>
                    </a>
                </div>
                <?php endif; ?>

            </article>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-box">
                <h5 class="mb-3 font-weight-bold" style="color: #0047AB;">Berita Terbaru</h5>
                
                <?php if (count($sidebar_news) > 0): ?>
                    <?php foreach ($sidebar_news as $item): ?>
                    <div class="mini-news-item">
                        <img src="uploads/<?= htmlspecialchars($item['foto_berita']); ?>" 
                             class="mini-thumb" 
                             alt="thumb"
                             onerror="this.src='Asset/default_news.png';">
                        <div>
                            <h6 class="mb-1" style="font-size: 0.95rem;">
                                <a href="detail_berita.php?id=<?= $item['id_berita']; ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($item['judul_berita']); ?>
                                </a>
                            </h6>
                            <small class="text-muted">
                                <i class="far fa-clock"></i> <?= date('d M Y', strtotime($item['created_at_berita'])); ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small">Tidak ada berita lain.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>