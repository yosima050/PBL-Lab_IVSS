<?php
include 'dashboard/db.php'; 

// 2. LOGIKA PENCARIAN (Opsional)
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

try {
    // Ambil data dari tabel 'fasilitas'
    $sql = "SELECT * FROM public.fasilitas 
            WHERE nama_fasilitas ILIKE :keyword 
            OR deskripsi_fasilitas ILIKE :keyword 
            ORDER BY nama_fasilitas ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['keyword' => $search_param]);
    $fasilitas_list = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    $fasilitas_list = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fasilitas - Lab IVSS</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --color-dark-blue: #0047AB;
            --color-light-blue: #DFECFF;
            --color-table-header-border: #004C99;
            --color-table-striped: #F5F9FF;
        }

        .banner1{
            background:url(Asset/Coba.jpg) no-repeat 0px 0px;
            background-size:cover;
            min-height:250px;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
            /* Padding top dihapus karena navbar biasanya sudah handle layout */
        }

        .btn-mahasiswa-custom {
            background-color: #F9D723;
            color: #0047AB;
            font-weight: bold;
            border: none;
            padding: 8px 15px;
            border-radius: .25rem;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .search-filter-row {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .input-search-custom {
            border-right: none !important;
            border-color: #ced4da;
            border-radius: .25rem 0 0 .25rem !important;
            padding-left: 3rem !important;
        }

        .btn-filter-custom {
            background-color: #F5F9FF;
            color: #000;
            border: none;
            padding: .375rem 1rem;
            border-radius: 0 .25rem .25rem 0;
        }

        .input-group-custom {
            position: relative;
        }
        .input-group-custom .fa-search {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #6c757d;
        }
        
        .table-custom-container {
            border: none;
            border-radius: 0;
            overflow-x: auto;
        }

        .table-custom {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-custom thead th {
            background-color: var(--color-table-header-border);
            color: #fff;
            font-weight: bold;
            border: 1px solid var(--color-table-header-border);
            height: 38px;
            padding: 0.5rem;
            text-align: center;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid var(--color-table-header-border);
            height: 38px;
            padding: 0.5rem;
            width: 33.33%;
            font-weight: normal;
            text-align: center;
            vertical-align: middle;
        }

        .table-custom tbody tr:nth-child(odd) {
            background-color: var(--color-table-striped);
        }
        .table-custom tbody tr:nth-child(even) {
            background-color: #fff;
        }
        .fasilitas-img {
            width: 80px; 
            height: 60px; 
            object-fit: cover; 
            border-radius: 5px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="banner1"> </div>

<div class="container mb-5">
    
    <button class="btn btn-mahasiswa-custom" type="button">Fasilitas dan Halaman Produk</button>

    <form action="" method="GET">
        <div class="row align-items-center search-filter-row">
            <div class="col-12 d-flex">
                <div class="input-group-custom flex-grow-1 position-relative">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" class="form-control input-search-custom" 
                           placeholder="Cari Fasilitas..." 
                           value="<?= htmlspecialchars($keyword); ?>">
                </div>
                
                <button class="btn btn-filter-custom ms-2" type="submit">
                    <i class="fas fa-filter"></i> Cari
                </button>
            </div>
        </div>
    </form>
    
    <div class="table-custom-container">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 30%;">Nama Fasilitas</th>
                    <th style="width: 50%;">Deskripsi</th>
                    <th style="width: 20%;">Foto</th> 
                </tr>
            </thead>
            <tbody>
                <?php if (count($fasilitas_list) > 0): ?>
                    <?php foreach ($fasilitas_list as $row): ?>
                    <tr>
                        <td class="text-start ps-4 fw-bold">
                            <?= htmlspecialchars($row['nama_fasilitas']); ?>
                        </td>
                        <td class="text-start ps-3">
                            <?= htmlspecialchars($row['deskripsi_fasilitas']); ?>
                        </td>
                        <td>
                            <?php if(!empty($row['foto_fasilitas'])): ?>
                                <img src="<?= htmlspecialchars($row['foto_fasilitas']); ?>" 
                                     class="fasilitas-img" 
                                     alt="Foto"
                                     onerror="this.src='Asset/noimage.png';">
                            <?php else: ?>
                                <span>Tidak ada Foto</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-3">Tidak ada data fasilitas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>