<?php
include 'dashboard/db.php'; 


// 2. INISIALISASI VARIABEL (Agar tidak error Undefined Variable)
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$year_filter = isset($_GET['year']) ? $_GET['year'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Variabel default untuk mencegah warning jika query gagal
$publikasi_list = [];
$years = [];
$total_items = 0;
$total_pages = 0;

try {
    // 3. QUERY UNTUK MENGAMBIL TAHUN (UNTUK DROPDOWN)
    // PERBAIKAN: Gunakan 'tahun_publikasi' langsung
    $stmt_years = $pdo->query("SELECT DISTINCT tahun_publikasi as year FROM public.publikasi ORDER BY year DESC");
    $years = $stmt_years->fetchAll(PDO::FETCH_COLUMN);

    // 4. MEMBANGUN QUERY UTAMA
    $sql = "SELECT * FROM public.publikasi";
    $whereClauses = [];
    $params = [];

    // Filter Tahun
    if (!empty($year_filter)) {
        // PERBAIKAN: Langsung bandingkan dengan kolom tahun_publikasi
        $whereClauses[] = "tahun_publikasi = :year";
        $params[':year'] = $year_filter;
    }

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // Hitung Total Data (Untuk Pagination)
    $stmt_count = $pdo->prepare(str_replace("SELECT *", "SELECT COUNT(*)", $sql));
    $stmt_count->execute($params);
    $total_items = $stmt_count->fetchColumn();
    
    // Hitung total halaman (hindari pembagian dengan nol jika data kosong)
    $total_pages = ($total_items > 0) ? ceil($total_items / $limit) : 1;

    // Sorting
    if ($sort == 'oldest') {
        $sql .= " ORDER BY tahun_publikasi ASC";
    } else {
        $sql .= " ORDER BY tahun_publikasi DESC";
    }

    // Limit & Offset
    $sql .= " LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $publikasi_list = $stmt->fetchAll();

} catch (PDOException $e) {
    // Tampilkan error hanya untuk debugging, bisa dikomentari saat production
    echo "<div class='alert alert-danger'>Error Database: " . $e->getMessage() . "</div>";
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
        .filter-btn.active {
            background-color: #0047AB;
            color: white;
            border-color: #0047AB;
        }
        /* Dropdown CSS fix */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 100px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .years-dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {background-color: #f1f1f1}
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
                            Tahun: <?= htmlspecialchars($row['tahun_publikasi']); ?>
                        </div>
                        
                        <button class="baca-btn" onclick="window.open('<?= htmlspecialchars($row['link_publikasi']); ?>', '_blank')">
                            Baca
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center my-5">
                    <p>Belum ada data publikasi.</p>
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