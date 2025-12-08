<?php
// 1. HUBUNGKAN KE DATABASE
include 'dashboard/db.php'; 

// 2. CONFIG PAGINASI & PENCARIAN
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

// Tentukan halaman saat ini
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 3; // Jumlah item per halaman untuk berita utama
$offset = ($page - 1) * $limit;

try {
    // A. HITUNG TOTAL DATA BERITA (Untuk Paginasi)
    $sql_count = "SELECT COUNT(*) FROM public.berita 
                  WHERE (judul_berita ILIKE :keyword 
                  OR isi_berita ILIKE :keyword)
                  AND (kategori_berita NOT ILIKE 'pengumuman' 
                  AND kategori_berita NOT ILIKE 'agenda'
                  AND kategori_berita NOT ILIKE '%kegiatan%')";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute(['keyword' => $search_param]);
    $total_data = $stmt_count->fetchColumn();
    $total_pages = ceil($total_data / $limit);

    // B. AMBIL DATA BERITA DENGAN LIMIT & OFFSET
    $sql_berita = "SELECT * FROM public.berita 
                   WHERE (judul_berita ILIKE :keyword 
                   OR isi_berita ILIKE :keyword)
                   AND (kategori_berita NOT ILIKE 'pengumuman' 
                   AND kategori_berita NOT ILIKE 'agenda'
                   AND kategori_berita NOT ILIKE '%kegiatan%')
                   ORDER BY created_at_berita DESC
                   LIMIT :limit OFFSET :offset";
    
    $stmt_berita = $pdo->prepare($sql_berita);
    $stmt_berita->bindValue(':keyword', $search_param, PDO::PARAM_STR);
    $stmt_berita->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt_berita->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_berita->execute();
    $berita_list = $stmt_berita->fetchAll();

    // C. AMBIL PENGUMUMAN (Tanpa Paginasi - Semua Data)
    $sql_pengumuman = "SELECT * FROM public.berita 
                       WHERE kategori_berita ILIKE 'pengumuman'
                       ORDER BY created_at_berita DESC
                       LIMIT 5"; // Batasi 5 pengumuman terbaru
    $stmt_pengumuman = $pdo->query($sql_pengumuman);
    $pengumuman_list = $stmt_pengumuman->fetchAll();

    // D. AMBIL AGENDA (Tanpa Paginasi - Semua Data)
    $sql_agenda = "SELECT * FROM public.berita 
                   WHERE (kategori_berita ILIKE 'agenda' 
                   OR kategori_berita ILIKE '%kegiatan%')
                   ORDER BY created_at_berita DESC
                   LIMIT 5"; // Batasi 5 agenda terbaru
    $stmt_agenda = $pdo->query($sql_agenda);
    $agenda_list = $stmt_agenda->fetchAll();

    // Ambil 1 Berita Utama (Featured) dari list berita
    $featured_news = !empty($berita_list) ? array_shift($berita_list) : null;

    // Hitung item yang sedang ditampilkan
    $start_item = ($total_data > 0) ? $offset + 1 : 0;
    $end_item = min($offset + $limit, $total_data);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $berita_list = [];
    $pengumuman_list = [];
    $agenda_list = [];
    $featured_news = null;
    $total_data = 0;
    $total_pages = 1;
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
    
    <style>
        /* Style Paginasi (Agar tombol disabled terlihat jelas) */
        .btn-nav.disabled {
            opacity: 0.5;
            pointer-events: none;
            cursor: default;
        }
    </style>
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
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada berita yang ditemukan.</p>
                </div>
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

    <!-- Pagination Controls -->
    <?php if ($total_data > 0): ?>
    <div class="pagination-controls">
        <a href="?page=<?= max(1, $page - 1) ?>&q=<?= htmlspecialchars($keyword) ?>" 
           class="btn-nav <?= ($page <= 1) ? 'disabled' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-left-fill me-2" viewBox="0 0 16 16">
                <path d="m3.86 8.753 5.48-4.796A1 1 0 0 1 10 4.907v6.186a1 1 0 0 1-1.66 1.154l-5.48-4.796a1 1 0 0 1 0-1.509"/>
            </svg>
            Previous
        </a>
        
        <span>
            <?= $start_item; ?>-<?= $end_item; ?> of <?= $total_data; ?>
        </span>
        
        <a href="?page=<?= min($total_pages, $page + 1) ?>&q=<?= htmlspecialchars($keyword) ?>" 
           class="btn-nav <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
            Next
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill ms-2" viewBox="0 0 16 16">
                <path d="m12.14 8.753-5.48-4.796A1 1 0 0 0 6 4.907v6.186a1 1 0 0 0 1.66 1.154l5.48-4.796a1 1 0 0 0 0-1.509"/>
            </svg>
        </a>
    </div>
    <?php endif; ?>
</div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>