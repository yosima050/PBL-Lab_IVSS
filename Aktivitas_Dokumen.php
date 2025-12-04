<?php
include 'dashboard/db.php'; 


// 2. LOGIKA PENCARIAN
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

try {
    $sql_count = "SELECT COUNT(*) FROM public.proyek 
                  WHERE judul_proyek ILIKE :keyword 
                  OR deskripsi_proyek ILIKE :keyword";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute(['keyword' => $search_param]);
    $total_data = $stmt_count->fetchColumn();
    // Query Utama: Mengambil data aktivitas
    // Subquery total_galeri: Menghitung jumlah foto tambahan di tabel 'galeri'
    $sql = "SELECT a.*,
            (SELECT COUNT(*) FROM public.galeri g WHERE g.id_aktivitas = a.id_aktivitas) as total_galeri
            FROM public.aktivitas a
            WHERE a.judul_aktivitas ILIKE :keyword 
            OR a.isi_aktivitas ILIKE :keyword 
            ORDER BY a.tanggal_mulai_aktivitas DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['keyword' => $search_param]);
    $aktivitas_list = $stmt->fetchAll();
    $items_per_page = 4;
    $current_page = 1;
    $start_item = (($current_page - 1) * $items_per_page) + 1;
    $end_item = min($current_page * $items_per_page, $total_data);

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
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
    <title>Aktivitas & Dokumentasi - Lab IVSS</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="dashboard/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styleAD.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    
    <style>
        .activity-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
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
                                $fotoPath = 'uploads/' . $row['foto_galeri'];
                                if (!empty($row['foto_galeri']) && file_exists($fotoPath)): 
                            ?>
                                <img src="<?= htmlspecialchars($fotoPath); ?>" alt="Thumbnail">
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
                                    | <i class="fas fa-images"></i> +<?= $row['total_galeri']; ?> foto lainnya
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