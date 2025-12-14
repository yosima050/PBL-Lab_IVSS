<?php
// Pastikan path ini benar sesuai struktur folder Anda
include 'dashboard/db.php'; 

// 1. CONFIG PAGINASI & PENCARIAN
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

// Tentukan halaman saat ini (Default 1)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 4; // Jumlah item per halaman
$offset = ($page - 1) * $limit;

try {
    // A. HITUNG TOTAL DATA (Untuk Paginasi)
    $sql_count = "SELECT COUNT(*) FROM public.proyek 
                  WHERE judul_proyek ILIKE :keyword 
                  OR deskripsi_proyek ILIKE :keyword";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute(['keyword' => $search_param]);
    $total_data = $stmt_count->fetchColumn();

    // Hitung Total Halaman
    $total_pages = ceil($total_data / $limit);

    // B. AMBIL DATA (DENGAN GROUP BY AGAR TIDAK DUPLIKAT)
    // Kita gunakan GROUP BY id_proyek untuk menggabungkan baris ganda
    // Kita gunakan MAX() untuk mengambil satu nilai kategori (Prioritas Dosen > Mahasiswa)
    $sql = "SELECT 
                p.id_proyek, 
                p.judul_proyek, 
                p.deskripsi_proyek, 
                p.tahun_proyek, 
                p.tipe_proyek,
                -- Logika: Ambil Kategori Dosen dulu. Jika kosong, baru ambil Kategori Mahasiswa.
                COALESCE(
                    MAX(dd.kategori_proyek_dosen), 
                    MAX(dm.kategori_proyek_mahasiswa), 
                    '-'
                ) as kategori_proyek
            FROM public.proyek p
            LEFT JOIN public.detail_proyek_dosen dd ON p.id_proyek = dd.id_proyek
            LEFT JOIN public.detail_proyek_mahasiswa dm ON p.id_proyek = dm.id_proyek
            WHERE p.judul_proyek ILIKE :keyword 
            OR p.deskripsi_proyek ILIKE :keyword 
            GROUP BY p.id_proyek -- PENTING: Mencegah duplikasi data
            ORDER BY p.id_proyek DESC 
            LIMIT :limit OFFSET :offset"; 
            
    $stmt = $pdo->prepare($sql);
    
    // Binding Parameter
    $stmt->bindValue(':keyword', $search_param, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $proyek_list = $stmt->fetchAll();

    // Hitung item yang sedang ditampilkan (Info: 1-4 of 10)
    $start_item = ($total_data > 0) ? $offset + 1 : 0;
    $end_item = min($offset + $limit, $total_data);

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    $proyek_list = [];
    $total_data = 0;
    $total_pages = 1;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Produk dan Riset</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="navbar.css">
    
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .banner1{
            background:url(Asset/Coba.jpg) no-repeat 0px 0px;
            background-size:cover;
            min-height:250px;
        }
        .custom-yellow-header {
            background-color: #F9D723;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
            color: #0047AB;
        }
        .custom-card-title {
            background-color: #F9D723;
            color: #212529;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 10px;
        }
        .badge-status {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: normal;
            color: white;
        }
        .bg-blue { background-color: #0047AB; }
        .bg-green { background-color: #198754; }
        .bg-orange { background-color: #fd7e14; }

        .custom-detail-button {
            background-color: #FFFCED;
            color: #212529;
            font-weight: bold;
            width: 100%;
            border: none;
            display: block;
            text-align: center;
            padding: 8px 0;
            text-decoration: none;
        }
        .custom-detail-button:hover {
            background-color: #F9D723;
            color: #0047AB;
        }
        .custom-card {
            border: 2px solid #0047AB !important;
            border-radius: 0.375rem;
            margin-bottom: 15px;
            padding: 15px;
            background-color: white;
        }
        .bg-light-gray {
            background-color: #F5F9FF;
            border-radius: 0.375rem;
            padding: 20px;
        }
        /* Style Paginasi */
        .pagination-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .pagination-controls .btn-nav {
            background-color: white;
            color: #0047AB;
            border: 2px solid #0047AB;
            border-radius: 0.3rem;
            padding: 8px 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
        }
        .pagination-controls .btn-nav:hover {
            background-color: #F9D723;
            color: #0047AB;
        }
        .pagination-controls .btn-nav.disabled {
            opacity: 0.5;
            pointer-events: none;
            border-color: #ccc;
            color: #ccc;
        }
        .pagination-controls span {
            margin: 0 20px;
            font-size: 1.1rem;
            color: #212529;
            font-weight: 500;
        }
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>

    <div class="banner1"> </div>
    
    <div class="container py-4">

        <div class="custom-yellow-header">
            Halaman Proyek dan Riset
        </div>
        
        <div class="bg-light-gray shadow-sm">
            
            <h4 class="mb-3 d-flex align-items-center" style="color: #0047AB; font-weight:700;">
                Daftar Proyek & Riset
            </h4>

            <form action="" method="GET">
                <div class="input-group mb-4">
                    <input type="text" name="q" class="form-control" 
                           placeholder="Cari Produk, Proyek..." 
                           value="<?= htmlspecialchars($keyword) ?>">
                    
                    <button class="btn btn-light border" type="submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>

            <?php if (count($proyek_list) > 0): ?>
                <?php foreach ($proyek_list as $row): ?>
                    
                    <div class="custom-card shadow-sm">
                        <div class="custom-card-title">
                            <?= htmlspecialchars($row['tipe_proyek'] ?? ''); ?>
                        </div>
                        
                        <h5 class="card-title fw-bold">
                            <?= htmlspecialchars($row['judul_proyek'] ?? ''); ?>
                            <small class="text-muted ms-2" style="font-size: 0.8rem;">(<?= htmlspecialchars($row['tahun_proyek'] ?? ''); ?>)</small>
                        </h5>
                        
                        <p class="card-text text-muted">
                            <?php 
                                $deskripsi = $row['deskripsi_proyek'] ?? '';
                                echo htmlspecialchars(substr($deskripsi, 0, 150)) . (strlen($deskripsi) > 150 ? '...' : ''); 
                            ?>
                        </p>
                        
                        <div class="mb-3">
                            <?php
                                // Badge Warna
                                $badgeColor = 'bg-blue';
                                $tipe = strtolower($row['tipe_proyek'] ?? '');
                                
                                if (strpos($tipe, 'aktif') !== false) {
                                    $badgeColor = 'bg-orange';
                                } elseif (strpos($tipe, 'publikasi') !== false || strpos($tipe, 'selesai') !== false) {
                                    $badgeColor = 'bg-green';
                                }

                                // Kategori dari Query
                                $kategori = $row['kategori_proyek'] ?? '-';
                            ?>
                            
                            <span class="badge-status <?= $badgeColor ?>">
                                <?= htmlspecialchars($kategori); ?>
                            </span>
                        </div>
                        
                        <a href="produk2.php?id=<?= $row['id_proyek']; ?>" class="btn custom-detail-button">Lihat Detail</a>
                    </div>

                <?php endforeach; ?>

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

            <?php else: ?>
                <div class="alert alert-warning text-center">
                    Tidak ada data produk atau riset yang ditemukan.
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>