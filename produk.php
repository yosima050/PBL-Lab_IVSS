<?php
// 1. HUBUNGKAN KE DATABASE
include 'dashboard/db.php'; 

// 2. LOGIKA PENCARIAN
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

try {
    // Query mengambil data dari tabel 'proyek'
    // Kita filter berdasarkan Judul atau Deskripsi
    $sql = "SELECT * FROM public.proyek 
            WHERE judul_proyek ILIKE :keyword 
            OR deskripsi_proyek ILIKE :keyword 
            ORDER BY tahun_proyek DESC"; // Urutkan dari yang terbaru
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['keyword' => $search_param]);
    $proyek_list = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    $proyek_list = [];
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
            background:url(../Asset/Coba.jpg) no-repeat 0px 0px;
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
        /* Style Badge Dinamis */
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
            display: block; /* Agar tombol memenuhi lebar */
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
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>

    <div class="banner1"> </div>
    
    <div class="container py-4">

        <div class="custom-yellow-header">
            Halaman Produk dan Riset
        </div>
        
        <div class="bg-light-gray shadow-sm">
            
            <h4 class="mb-3 d-flex align-items-center" style="color: #0047AB; font-weight:700;">
                Daftar Riset & Proyek
            </h4>

            <form action="" method="GET">
                <div class="input-group mb-4">
                    <input type="text" name="q" class="form-control" 
                           placeholder="Cari Produk, Proyek..." 
                           value="<?= htmlspecialchars($keyword) ?>"
                           aria-label="Cari Produk">
                    
                    <button class="btn btn-light border" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Cari
                    </button>
                </div>
            </form>

            <?php if (count($proyek_list) > 0): ?>
                <?php foreach ($proyek_list as $row): ?>
                    
                    <div class="custom-card shadow-sm">
                        <div class="custom-card-title">
                            <?= htmlspecialchars($row['tipe_proyek']); ?>
                        </div>
                        
                        <h5 class="card-title fw-bold">
                            <?= htmlspecialchars($row['judul_proyek']); ?>
                            <small class="text-muted ms-2" style="font-size: 0.8rem;">(<?= htmlspecialchars($row['tahun_proyek']); ?>)</small>
                        </h5>
                        
                        <p class="card-text text-muted">
                            <?= htmlspecialchars(substr($row['deskripsi_proyek'], 0, 150)) . '...'; ?>
                        </p>
                        
                        <div class="mb-3">
                            <?php
                                // Memberikan warna badge bedasarkan tipe proyek
                                $badgeColor = 'bg-blue'; // Default
                                if (stripos($row['tipe_proyek'], 'Proyek Aktif') !== false) {
                                    $badgeColor = 'bg-orange';
                                } elseif (stripos($row['tipe_proyek'], 'Publikasi') !== false) {
                                    $badgeColor = 'bg-green';
                                }
                            ?>
                            <span class="badge-status <?= $badgeColor ?>">
                                <?= htmlspecialchars($row['tipe_proyek']); ?>
                            </span>
                        </div>
                        
                        <a href="#" class="btn custom-detail-button">Lihat Detail</a>
                    </div>

                <?php endforeach; ?>
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