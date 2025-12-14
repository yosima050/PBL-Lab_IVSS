<?php
// 1. HUBUNGKAN KE DATABASE
include 'dashboard/db.php'; 

// 2. LOGIKA PENGAMBILAN DATA DOSEN (TEAM MEMBER)
try {
    $stmt_dosen = $pdo->prepare("SELECT id_dosen, nama_dosen, foto_dosen FROM public.dosen ORDER BY id_dosen ASC LIMIT 7");
    $stmt_dosen->execute();
    $team_members = $stmt_dosen->fetchAll();
} catch (PDOException $e) {
    $team_members = [];
    echo "Error fetching dosen: " . $e->getMessage();
}

// 3. LOGIKA PENGAMBILAN DATA MAHASISWA & PAGINASI
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

// --- Konfigurasi Paginasi ---
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // A. Hitung Total Data (Join Pendaftaran + Mahasiswa)
    // Hanya ambil yang sudah DITERIMA
    $sql_count = "SELECT COUNT(*) 
                  FROM public.pendaftaran p
                  JOIN public.mahasiswa m ON p.id_users = m.id_users
                  WHERE p.status_mahasiswa = 'Diterima' 
                  AND (p.nama_mahasiswa ILIKE :keyword OR p.nim ILIKE :keyword)";
                  
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute(['keyword' => $search_param]);
    $total_data = $stmt_count->fetchColumn();
    $total_pages = ceil($total_data / $limit);

    // B. Ambil Data Mahasiswa + Status Keaktifan
    $sql_mhs = "SELECT p.*, m.keaktifan_mahasiswa 
                FROM public.pendaftaran p
                JOIN public.mahasiswa m ON p.id_users = m.id_users
                WHERE p.status_mahasiswa = 'Diterima' 
                AND (p.nama_mahasiswa ILIKE :keyword OR p.nim ILIKE :keyword)
                ORDER BY 
                    CASE WHEN m.keaktifan_mahasiswa = 'Aktif' THEN 1 ELSE 2 END ASC, -- Aktif duluan
                    p.nama_mahasiswa ASC 
                LIMIT :limit OFFSET :offset";
    
    $stmt_mhs = $pdo->prepare($sql_mhs);
    $stmt_mhs->bindValue(':keyword', $search_param, PDO::PARAM_STR);
    $stmt_mhs->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt_mhs->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_mhs->execute();
    $mahasiswa_list = $stmt_mhs->fetchAll();
    
    // Info item yang sedang tampil
    $start_item = ($total_data > 0) ? $offset + 1 : 0;
    $end_item = min($offset + $limit, $total_data);

} catch (PDOException $e) {
    $mahasiswa_list = [];
    $total_data = 0;
    $total_pages = 1;
    echo "Error fetching mahasiswa: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team & Student List - Lab IVSS</title>

    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
<style>
    :root {
        --color-dark-blue: #0047AB;
        --color-light-blue: #F5F9FF;
        --color-table-header-border: var(--color-dark-blue);
        --color-table-striped: var(--color-light-blue);
    }

    .banner1{
        background:url(Asset/Coba.jpg) no-repeat 0px 0px;
        background-size:cover;
        min-height:250px;
    }
    body { font-family: 'Roboto', sans-serif; }

    .container {
        max-width: 1200px;
        padding: 20px 24px 50px 24px;
    }

    /* === ANIMASI FLIP CARD === */
    .flip-container {
        background-color: transparent;
        width: 200px;
        height: 200px;
        perspective: 1000px;
        margin: 10px;
    }
    .flip-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.8s;
        transform-style: preserve-3d;
        cursor: pointer;
    }
    .flip-container:hover .flip-inner {
        transform: rotateY(180deg);
    }
    .flip-front, .flip-back {
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        border-radius: 50%;
        border: 4px solid #f8f9fa;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .flip-front { background-color: #fff; }
    .flip-front img { width: 100%; height: 100%; object-fit: cover; }
    .flip-back {
        background-color: #0047AB;
        color: white;
        transform: rotateY(180deg);
        padding: 15px;
        flex-direction: column;
    }
    .flip-back h5 { font-size: 1rem; margin: 0; font-weight: bold; }
    .flip-back small { margin-top: 5px; font-size: 0.8rem; opacity: 0.8; }

    .team-member-container {
        display: flex;
        flex-wrap: wrap; 
        justify-content: center;
        gap: 20px;
        margin-bottom: 40px; 
    }

    .category-header {
        background-color: #F9D723;
        color: #0047AB;
        padding: 5px 15px;
        border-radius: 5px;
        display: inline-block;
        font-weight: bold;
        margin-bottom: 15px;
    }
    
    .search-filter-row { margin-bottom: 15px; }
    .table-responsive { margin-bottom: 20px; }

    .table-custom-layout {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .table-custom-layout thead th {
        background-color: var(--color-table-header-border);
        color: #fff;
        font-weight: bold;
        border: 1px solid var(--color-table-header-border);
        height: 38px; 
        padding: 0.5rem;
        text-align: center;
    }
    .table-custom-layout th, .table-custom-layout td {
        border: 1px solid var(--color-table-header-border);
        height: 38px;
        padding: 0.5rem;
        text-align: center;
        font-weight: normal;
        vertical-align: middle;
    }
    .table-custom-layout tbody tr:nth-child(odd) td { background-color: #F5F9FF; }
    .table-custom-layout tbody tr:nth-child(even) td { background-color: #fff; }

    .input-search-custom {
        border-right: 1px solid #ced4da !important; 
        border-color: #ced4da;
        border-radius: .25rem !important; 
        padding-left: 3rem !important; 
        height: 38px; 
    }
    .input-group-custom { position: relative; }
    .input-group-custom .fa-search {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        color: #6c757d;
    }
    .btn-filter-custom {
        background-color: #fff;
        color: #000;
        border: 1px solid #ced4da;
        padding: .375rem 1rem;
        height: 38px;
        border-radius: .25rem;
    }

    /* Badge Status */
    .badge-status {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .badge-aktif {
        background-color: #1cc88a; /* Hijau */
        color: white;
    }
    .badge-alumni {
        background-color: #858796; /* Abu-abu */
        color: white;
    }

    /* Paginasi */
    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 20px;
    }
    .btn-page {
        background-color: #fff;
        color: #0047AB;
        border: 1px solid #0047AB;
        padding: 6px 16px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: bold;
    }
    .btn-page:hover { background-color: #F9D723; color: #0047AB; }
    .btn-page.disabled { border-color: #ccc; color: #ccc; pointer-events: none; }
    .page-info { font-weight: 500; color: #555; }
</style>

</head>
<body>

<?php include 'navbar.php'; ?>

<div class="banner1"> </div>

<div class="container">

    <div class="category-header">
        Dosen Laboratorium
    </div>
    
    <div class="team-member-container">
        <?php if (count($team_members) > 0): ?>
            <?php foreach ($team_members as $member): ?>
                <?php
                    $fileName = $member['foto_dosen'] ?? '';
                    $exists   = $fileName && file_exists(__DIR__ . '/uploads/' . $fileName);
                    $src      = $exists ? 'uploads/' . htmlspecialchars($fileName)
                                      : 'Asset/default_profile.jpg';
                ?>
                <div class="flip-container">
                    <a href="Profil_dosen.php?id=<?= $member['id_dosen']; ?>" class="text-decoration-none">
                        <div class="flip-inner">
                            <div class="flip-front">
                                <img src="<?= $src ?>" alt="<?= htmlspecialchars($member['nama_dosen']); ?>">
                            </div>
                            <div class="flip-back">
                                <h5><?= htmlspecialchars($member['nama_dosen']); ?></h5>
                                <small>Klik untuk Detail</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">Data anggota tim belum tersedia.</div>
        <?php endif; ?>
    </div>
    
    <div class="category-header">
        Mahasiswa
    </div>

    <form action="" method="GET">
        <div class="row search-filter-row align-items-center">
            <div class="col-12 d-flex justify-content-between">
                <div class="input-group-custom me-2" style="max-width: 300px;"> 
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" class="form-control input-search-custom" 
                           placeholder="Cari Nama / NIM..." 
                           value="<?= htmlspecialchars($keyword); ?>">
                </div>
                
                <button class="btn btn-filter-custom" type="submit">
                    <i class="fas fa-filter"></i> Cari
                </button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-custom-layout">
            <thead>
                <tr>
                    <th style="width: 15%;">NIM</th>
                    <th style="width: 25%;">Nama Mahasiswa</th>
                    <th style="width: 20%;">Jurusan</th>
                    <th style="width: 15%;">Prodi</th>
                    <th style="width: 15%;">Email</th>
                    <th style="width: 10%;">Status</th> </tr>
            </thead>
            <tbody>
                <?php if (count($mahasiswa_list) > 0): ?>
                    <?php foreach ($mahasiswa_list as $mhs): ?>
                    <tr>
                        <td><?= htmlspecialchars($mhs['nim']); ?></td>
                        <td><?= htmlspecialchars($mhs['nama_mahasiswa']); ?></td>
                        <td>Teknologi Informasi</td> 
                        <td><?= htmlspecialchars($mhs['prodi']); ?></td>
                        <td><?= htmlspecialchars($mhs['email_mahasiswa']); ?></td>
                        <td>
                            <?php 
                                $status = $mhs['keaktifan_mahasiswa'] ?? 'Aktif'; 
                                $badgeClass = ($status == 'Alumni') ? 'badge-alumni' : 'badge-aktif';
                            ?>
                            <span class="badge-status <?= $badgeClass ?>">
                                <?= htmlspecialchars($status) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data mahasiswa ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_data > 0): ?>
    <div class="pagination-container">
        <a href="?q=<?= htmlspecialchars($keyword) ?>&page=<?= $page - 1 ?>" 
           class="btn-page <?= ($page <= 1) ? 'disabled' : '' ?>">
           <i class="fas fa-chevron-left me-1"></i> Prev
        </a>

        <span class="page-info">
            Menampilkan <?= $start_item ?> - <?= $end_item ?> dari <?= $total_data ?> Mahasiswa
        </span>

        <a href="?q=<?= htmlspecialchars($keyword) ?>&page=<?= $page + 1 ?>" 
           class="btn-page <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
           Next <i class="fas fa-chevron-right ms-1"></i>
        </a>
    </div>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>