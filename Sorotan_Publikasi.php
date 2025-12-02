<?php
// 1. HUBUNGKAN KONEKSI
include 'dashboard/db.php'; 

// 2. INISIALISASI VARIABEL FILTER
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest'; // Default latest
$year_filter = isset($_GET['year']) ? $_GET['year'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Jumlah kartu per halaman
$offset = ($page - 1) * $limit;

try {
    // 3. QUERY UNTUK MENGAMBIL TAHUN (UNTUK DROPDOWN)
    // Mengambil tahun unik dari tabel publikasi
    $stmt_years = $pdo->query("SELECT DISTINCT EXTRACT(YEAR FROM tanggal_publikasi) as year FROM public.publikasi ORDER BY year DESC");
    $years = $stmt_years->fetchAll(PDO::FETCH_COLUMN);

    // 4. MEMBANGUN QUERY UTAMA (DENGAN FILTER)
    $sql = "SELECT * FROM public.publikasi";
    $whereClauses = [];
    $params = [];

    // Jika ada filter tahun
    if (!empty($year_filter)) {
        $whereClauses[] = "EXTRACT(YEAR FROM tanggal_publikasi) = :year";
        $params[':year'] = $year_filter;
    }

    // Gabungkan WHERE clause jika ada
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // Hitung Total Data (Untuk Pagination) sebelum dilimit
    $stmt_count = $pdo->prepare(str_replace("SELECT *", "SELECT COUNT(*)", $sql));
    $stmt_count->execute($params);
    $total_items = $stmt_count->fetchColumn();
    $total_pages = ceil($total_items / $limit);

    // Tambahkan Sorting (Order By)
    if ($sort == 'oldest') {
        $sql .= " ORDER BY tanggal_publikasi ASC";
    } else {
        $sql .= " ORDER BY tanggal_publikasi DESC"; // Default Latest
    }

    // Tambahkan Limit & Offset (Pagination)
    $sql .= " LIMIT :limit OFFSET :offset";
    
    // Eksekusi Query Data
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $publikasi_list = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $publikasi_list = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorotan Publikasi - Lab IVSS</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="dashboard/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styleSP.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    
    <style>
        /* Tambahan Style untuk Tombol Aktif */
        .filter-btn.active {
            background-color: #0047AB;
            color: white;
            border-color: #0047AB;
        }
        /* Style agar link memenuhi tombol baca */
        .baca-link {
            text-decoration: none;
            color: inherit;
            display: block;
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="banner1"></div>

    <div class="container">
        <div class="fokus-riset">Fokus Riset</div>
        
        <div class="tags">
            <span class="tag">Intelligent Vision</span>
            <span class="tag">Smart Systems</span>
        </div>

        <div class="sorotan-publikasi">Sorotan Publikasi</div>

        <div class="filter-buttons">
            <a href="?sort=latest&year=<?= $year_filter ?>" class="btn filter-btn <?= ($sort == 'latest') ? 'active' : '' ?>">Latest</a>
            
            <a href="?sort=oldest&year=<?= $year_filter ?>" class="btn filter-btn <?= ($sort == 'oldest') ? 'active' : '' ?>">Oldest</a>
            
            <div class="years-dropdown" style="display:inline-block; position:relative;">
                <button class="filter-btn">
                    <?= !empty($year_filter) ? $year_filter : 'Years' ?> ▼
                </button>
                <div class="dropdown-content">
                    <a href="?sort=<?= $sort ?>">All Years</a>
                    <?php foreach($years as $yr): ?>
                        <a href="?sort=<?= $sort ?>&year=<?= $yr ?>"><?= $yr ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="publications-grid">
            <?php if (count($publikasi_list) > 0): ?>
                <?php foreach ($publikasi_list as $row): ?>
                    <div class="publication-card">
                        <h3><?= htmlspecialchars($row['judul_publikasi']); ?></h3>
                        
                        <div class="publication-date">
                            <?= date('d M Y', strtotime($row['tanggal_publikasi'])); ?>
                        </div>
                        
                        <button class="baca-btn" onclick="window.open('<?= htmlspecialchars($row['link']); ?>', '_blank')">
                            Baca
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Belum ada data publikasi untuk kategori ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>&year=<?= $year_filter ?>">
                    <button>◄ Previous</button>
                </a>
            <?php else: ?>
                <button disabled style="opacity: 0.5; cursor: not-allowed;">◄ Previous</button>
            <?php endif; ?>

            <span>
                <?php 
                    $start = ($total_items > 0) ? $offset + 1 : 0;
                    $end = min($offset + $limit, $total_items);
                    echo "$start-$end of $total_items";
                ?>
            </span>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>&year=<?= $year_filter ?>">
                    <button>Next ►</button>
                </a>
            <?php else: ?>
                <button disabled style="opacity: 0.5; cursor: not-allowed;">Next ►</button>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>