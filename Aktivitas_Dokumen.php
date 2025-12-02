<?php
// 1. HUBUNGKAN KE DATABASE
include 'dashboard/db.php'; 

// 2. LOGIKA PENCARIAN
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

try {
    // Query Utama: Mengambil data aktivitas
    // + Subquery untuk menghitung jumlah foto/video di tabel 'galeri' yang terkait
    $sql = "SELECT 
                a.*,
                (SELECT COUNT(*) FROM public.galeri g WHERE g.id_aktivitas = a.id_aktivitas) as total_galeri
            FROM public.aktivitas a
            WHERE a.judul_aktivitas ILIKE :keyword 
            OR a.isi_aktivitas ILIKE :keyword 
            ORDER BY a.tanggal_mulai_aktivitas DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['keyword' => $search_param]);
    $aktivitas_list = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    $aktivitas_list = [];
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
</head>

<body>
    
<?php include 'navbar.php'; ?>

<div class="banner1"> </div>

<div class="container">
    <div class="tag1">
        <span>Aktivitas dan Dokumentasi</span>
        <img src="Asset/logo.png" class="emoji">
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
                    <i class="fas fa-filter"></i>
                    Cari
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
                            <?php if (!empty($row['foto_galeri']) && file_exists($row['foto_galeri'])): ?>
                                <img src="<?= htmlspecialchars($row['foto_galeri']); ?>" alt="Thumbnail" style="width:100%; height:100%; object-fit:cover; border-radius:10px;">
                            <?php else: ?>
                                <i class="fas fa-image"></i> <?php endif; ?>
                        </div>

                        <div class="activity-title">
                            <?= htmlspecialchars($row['judul_aktivitas']); ?>
                        </div>

                        <div class="activity-description">
                            <?= htmlspecialchars(substr($row['isi_aktivitas'], 0, 150)) . '...'; ?>
                        </div>

                        <div class="activity-meta">
                            <div class="activity-stats">
                                <?= $row['total_galeri']; ?> item dokumentasi
                            </div>
                            
                            <a href="detail_aktivitas.php?id=<?= $row['id_aktivitas']; ?>" class="detail-link">
                                Lihat Detail
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p>Tidak ada aktivitas yang ditemukan.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>