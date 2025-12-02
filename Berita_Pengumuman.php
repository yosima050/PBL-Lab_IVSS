<?php
// 1. HUBUNGKAN KE DATABASE
include 'dashboard/db.php'; 

try {
    // 2. QUERY MENGAMBIL SEMUA BERITA (Diurutkan dari yang terbaru)
    // Asumsi: Kita memfilter berdasarkan kata kunci judul untuk memisahkan Berita vs Pengumuman vs Agenda
    // Jika Anda punya kolom 'kategori' di tabel berita, silakan sesuaikan WHERE-nya.
    
    $sql = "SELECT * FROM public.berita ORDER BY created_at_berita DESC";
    $stmt = $pdo->query($sql);
    $all_data = $stmt->fetchAll();

    // 3. PISAHKAN DATA BERDASARKAN KATEGORI (LOGIKA PHP)
    $berita_list = [];
    $pengumuman_list = [];
    $agenda_list = [];

    foreach ($all_data as $row) {
        $judul_lower = strtolower($row['judul_berita']);

        // Cek Kata Kunci di Judul
        if (strpos($judul_lower, 'pengumuman') !== false) {
            $pengumuman_list[] = $row;
        } elseif (strpos($judul_lower, 'agenda') !== false || strpos($judul_lower, 'kegiatan') !== false) {
            $agenda_list[] = $row;
        } else {
            // Jika tidak ada kata pengumuman/agenda, anggap sebagai Berita Umum
            $berita_list[] = $row;
        }
    }

    // Ambil 1 Berita Utama (Featured)
    $featured_news = !empty($berita_list) ? array_shift($berita_list) : null;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Pengumuman - Lab IVSS</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    
    <link rel="stylesheet" href="css/styleBP.css">
    <style>
        .berita-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Style tambahan agar link judul berita bisa diklik */
        .berita-link {
            text-decoration: none;
            color: inherit;
        }
        .berita-link:hover {
            color: #0047AB; /* Biru Polinema */
        }
    </style>
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="banner1"> </div>
    
<div class="container"> 
    <div class="content-wrapper">
    
    <div class="header-berita">
        Berita
    </div>

    <div class="main-grid">
        
        <div class="berita-section">
            
            <?php if ($featured_news): ?>
            <div class="berita-card featured">
                <div class="berita-image featured">
                    <img src="<?= htmlspecialchars($featured_news['foto_berita']); ?>" 
                         alt="Featured News" 
                         onerror="this.src='Asset/default_news.png';">
                </div>
                <div class="berita-content">
                    <a href="detail_berita.php?id=<?= $featured_news['id_berita']; ?>" class="berita-link">
                        <p class="berita-title featured">
                            <?= htmlspecialchars($featured_news['judul_berita']); ?>
                        </p>
                    </a>
                    <p class="berita-date">
                        <?= date('d F Y', strtotime($featured_news['created_at_berita'])); ?> | 
                        Oleh: <?= htmlspecialchars($featured_news['author']); ?>
                    </p>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-info">Belum ada berita utama.</div>
            <?php endif; ?>

            <?php if (count($berita_list) > 0): ?>
                <?php foreach ($berita_list as $news): ?>
                <div class="berita-card regular">
                    <div class="berita-image regular">
                        <img src="<?= htmlspecialchars($news['foto_berita']); ?>" 
                             alt="Thumbnail" 
                             onerror="this.src='Asset/default_news.png';">
                    </div>
                    <div class="berita-content">
                        <a href="detail_berita.php?id=<?= $news['id_berita']; ?>" class="berita-link">
                            <p class="berita-title regular">
                                <?= htmlspecialchars($news['judul_berita']); ?>
                            </p>
                        </a>
                        <p class="berita-date">
                            <?= date('d F Y', strtotime($news['created_at_berita'])); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <div class="sidebar-section">
            
            <div class="sidebar-header">Pengumuman</div>
            <div class="sidebar-box">
                <?php if (count($pengumuman_list) > 0): ?>
                    <?php foreach ($pengumuman_list as $info): ?>
                    <div class="sidebar-item">
                        <a href="detail_berita.php?id=<?= $info['id_berita']; ?>" class="berita-link">
                            <p class="sidebar-title">
                                <?= htmlspecialchars($info['judul_berita']); ?>
                            </p>
                        </a>
                        <p class="sidebar-date">
                            <?= date('d M Y', strtotime($info['created_at_berita'])); ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="sidebar-item"><p>Tidak ada pengumuman terbaru.</p></div>
                <?php endif; ?>
            </div>

            <div class="sidebar-header mt-4">Agenda</div>
            <div class="sidebar-box">
                <?php if (count($agenda_list) > 0): ?>
                    <?php foreach ($agenda_list as $agenda): ?>
                    <div class="sidebar-item">
                        <a href="detail_berita.php?id=<?= $agenda['id_berita']; ?>" class="berita-link">
                            <p class="sidebar-title">
                                <?= htmlspecialchars($agenda['judul_berita']); ?>
                            </p>
                        </a>
                        <p class="sidebar-date">
                            <?= date('d M Y', strtotime($agenda['created_at_berita'])); ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="sidebar-item"><p>Tidak ada agenda terbaru.</p></div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>