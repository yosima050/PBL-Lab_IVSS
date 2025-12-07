<?php
include 'dashboard/db.php'; 

// 1. CONFIG PAGINASI & PENCARIAN
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

// Tentukan halaman saat ini
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 4; // Jumlah item per halaman
$offset = ($page - 1) * $limit;

try {
    // A. HITUNG TOTAL DATA (Untuk Paginasi)
    $sql_count = "SELECT COUNT(*) FROM public.aktivitas 
                  WHERE judul_aktivitas ILIKE :keyword 
                  OR isi_aktivitas ILIKE :keyword";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute(['keyword' => $search_param]);
    $total_data = $stmt_count->fetchColumn();
    $total_pages = ceil($total_data / $limit);

    // B. AMBIL DATA AKTIVITAS (DENGAN LIMIT & OFFSET)
    // Subquery untuk menghitung jumlah media (foto + video)
    $sql = "SELECT a.*,
            (SELECT COUNT(*) FROM public.galeri g WHERE g.id_aktivitas = a.id_aktivitas) as total_galeri,
            -- Ambil satu foto acak untuk thumbnail (prioritas foto > video)
            (SELECT foto_galeri FROM public.galeri g WHERE g.id_aktivitas = a.id_aktivitas AND foto_galeri IS NOT NULL LIMIT 1) as thumb_foto,
            (SELECT video_galeri FROM public.galeri g WHERE g.id_aktivitas = a.id_aktivitas AND video_galeri IS NOT NULL LIMIT 1) as thumb_video
            FROM public.aktivitas a
            WHERE a.judul_aktivitas ILIKE :keyword 
            OR a.isi_aktivitas ILIKE :keyword 
            ORDER BY a.tanggal_mulai_aktivitas DESC
            LIMIT :limit OFFSET :offset";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':keyword', $search_param, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $aktivitas_list = $stmt->fetchAll();

    // Hitung item yang sedang ditampilkan
    $start_item = ($total_data > 0) ? $offset + 1 : 0;
    $end_item = min($offset + $limit, $total_data);

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    $aktivitas_list = [];
    $total_data = 0;
    $total_pages = 1;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas & Dokumentasi - Lab IVSS</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="dashboard/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styleAD.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    
    <style>
        .activity-image {
            position: relative;
            background-color: #eee;
            overflow: hidden;
            border-radius: 10px;
        }
        .activity-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        /* Icon Video Overlay */
        .video-overlay {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 3rem;
            opacity: 0.8;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
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
    <div class="tag1">
        <span>Aktivitas dan Dokumentasi</span>
    </div>
</div>

<div class="profile-container">
    <div class="profile-container">
        <div class="activities-header">
            <h2>Aktivitas Laboratorium</h2>
        </div>

        <form action="" method="GET">
            <div class="search-filter-row">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="Cari Aktivitas..." 
                           value="<?= htmlspecialchars($keyword); ?>">
                </div>
                <button class="filter-btn" type="submit">
                    <i class="fas fa-filter"></i> Cari
                </button>
            </div>
        </form>

        <div id="activitiesContainer">
            
            <?php if (count($aktivitas_list) > 0): ?>
                <?php foreach ($aktivitas_list as $row): ?>
                    
                    <div class="activity-card">
                        <div class="activity-date">
                            <?= date('d M Y', strtotime($row['tanggal_mulai_aktivitas'])); ?>
                        </div>
                        
                        <div class="activity-image">
                            <?php 
                                // Prioritaskan Foto, jika tidak ada baru Video
                                $fotoPath = 'uploads/' . $row['thumb_foto'];
                                $videoPath = 'uploads/' . $row['thumb_video'];
                                
                                if (!empty($row['thumb_foto']) && file_exists($fotoPath)): 
                            ?>
                                <img src="<?= htmlspecialchars($fotoPath); ?>" alt="Thumbnail">
                            
                            <?php elseif (!empty($row['thumb_video']) && file_exists($videoPath)): ?>
                                <div style="width:100%; height:100%; background:#000; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-play-circle video-overlay"></i>
                                </div>

                            <?php else: ?>
                                <div style="height:100%; display:flex; align-items:center; justify-content:center; background:#eee; border-radius:10px;">
                                    <i class="fas fa-image fa-3x text-secondary"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="activity-title">
                            <?= htmlspecialchars($row['judul_aktivitas']); ?>
                        </div>

                        <div class="activity-description">
                            <?= htmlspecialchars(substr($row['isi_aktivitas'], 0, 150)) . '...'; ?>
                        </div>

                        <div class="activity-meta">
                            <div class="activity-stats">
                                <i class="fas fa-tags"></i> <?= htmlspecialchars($row['tag_aktivitas']); ?> 
                                <?php if($row['total_galeri'] > 0): ?>
                                    | <i class="fas fa-photo-video"></i> +<?= $row['total_galeri']; ?> media
                                <?php endif; ?>
                            </div>
                            
                            <a href="Detail_Aktivitas.php?id=<?= $row['id_aktivitas']; ?>" class="detail-link">
                                Lihat Detail <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada aktivitas yang ditemukan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

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

<?php include 'footer.php'; ?>

</body>
</html>