<?php
// 1. HUBUNGKAN KE DATABASE
// Pastikan file koneksi Anda menggunakan PDO ($pdo)
include 'dashboard/db.php'; 

try {
    $sql_count = "SELECT COUNT(*) FROM public.proyek 
                  WHERE judul_proyek ILIKE :keyword 
                  OR deskripsi_proyek ILIKE :keyword";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute(['keyword' => $search_param]);
    $total_data = $stmt_count->fetchColumn();
    // 2. QUERY UTAMA: Ambil Semua Berita (Urut Terbaru)
    $sql = "SELECT * FROM public.berita ORDER BY created_at_berita DESC";
    $stmt = $pdo->query($sql);
    $all_data = $stmt->fetchAll();
    $items_per_page = 4;
    $current_page = 1;
    $start_item = (($current_page - 1) * $items_per_page) + 1;
    $end_item = min($current_page * $items_per_page, $total_data);

    // 3. FILTER DATA BERDASARKAN KATEGORI (Dari kolom 'kategori_berita')
    $berita_list = [];
    $pengumuman_list = [];
    $agenda_list = [];

    foreach ($all_data as $row) {
        $kategori = strtolower($row['kategori_berita'] ?? ''); // Ubah ke huruf kecil biar aman

        if ($kategori === 'pengumuman') {
            $pengumuman_list[] = $row;
        } elseif ($kategori === 'agenda' || strpos($kategori, 'kegiatan') !== false) {
            $agenda_list[] = $row;
        } else {
            // Default: Masuk ke Berita (Termasuk jika kategori 'Berita' atau 'Tautan')
            $berita_list[] = $row;
        }
    }

    // Ambil 1 Berita Utama (Featured) dari list berita
    $featured_news = !empty($berita_list) ? array_shift($berita_list) : null;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $aktivitas_list = [];
    $total_data = 0; // Inisialisasi jika ada error
    $start_item = 0;
    $end_item = 0;
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
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="banner1"> </div>
    
<div class="container"> 
    <div class="content-wrapper">
    
    <div class="header-berita">Berita</div>

    <div class="main-grid">
        
        <div class="berita-section">
            
            <?php if ($featured_news): ?>
            <div class="berita-card featured">
                <div class="berita-image featured">
                    <a href="detail_berita.php?id=<?= $featured_news['id_berita']; ?>" class="berita-image-link">
                        <img src="uploads/<?= htmlspecialchars($featured_news['foto_berita']); ?>" 
                             alt="Featured News" 
                             onerror="this.src='Asset/default_news.png';">
                    </a>
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
                        <a href="detail_berita.php?id=<?= $news['id_berita']; ?>" class="berita-image-link">
                            <img src="uploads/<?= htmlspecialchars($news['foto_berita']); ?>" 
                                 alt="Thumbnail" 
                                 onerror="this.src='Asset/default_news.png';">
                        </a>
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
                    <div class="sidebar-item"><p class="text-muted small">Tidak ada pengumuman terbaru.</p></div>
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
                    <div class="sidebar-item"><p class="text-muted small">Tidak ada agenda terbaru.</p></div>
                <?php endif; ?>
            </div>            
        </div>
    </div>
</div>
<div class="pagination-controls">
    <a href="#" class="btn-nav">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-left-fill me-2" viewBox="0 0 16 16">
    <path d="m3.86 8.753 5.48-4.796A1 1 0 0 1 10 4.907v6.186a1 1 0 0 1-1.66 1.154l-5.48-4.796a1 1 0 0 1 0-1.509"/>
        </svg>
            Previous
            </a>
        <span>
            <?= $start_item; ?>-<?= $end_item; ?> of <?= $total_data; ?>
        </span>
    <a href="#" class="btn-nav">
        Next
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill ms-2" viewBox="0 0 16 16">
                <path d="m12.14 8.753-5.48-4.796A1 1 0 0 0 6 4.907v6.186a1 1 0 0 0 1.66 1.154l5.48-4.796a1 1 0 0 0 0-1.509"/>
            </svg>
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>