<?php
// 1. HUBUNGKAN KE DATABASE
include 'dashboard/db.php'; // Pastikan file koneksi.php sudah ada

// 2. LOGIKA PENGAMBILAN DATA DOSEN (TEAM MEMBER)
try {
    // Mengambil maksimal 7 dosen untuk ditampilkan di lingkaran atas
    // Diurutkan berdasarkan ID atau urutan tertentu
    $stmt_dosen = $pdo->prepare("SELECT id_dosen, nama_dosen, foto_dosen FROM public.dosen ORDER BY id_dosen ASC LIMIT 7");
    $stmt_dosen->execute();
    $team_members = $stmt_dosen->fetchAll();
} catch (PDOException $e) {
    $team_members = []; // Jika error, set array kosong agar tidak crash
    echo "Error fetching dosen: " . $e->getMessage();
}

// 3. LOGIKA PENGAMBILAN DATA MAHASISWA & PENCARIAN
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_param = "%" . $keyword . "%";

try {
    // Query mengambil data mahasiswa dari tabel pendaftaran
    // Filter berdasarkan Nama atau NIM jika ada pencarian
    $sql_mhs = "SELECT * FROM public.pendaftaran 
                WHERE nama_mahasiswa ILIKE :keyword 
                OR nim ILIKE :keyword 
                ORDER BY nama_mahasiswa ASC";
    
    $stmt_mhs = $pdo->prepare($sql_mhs);
    $stmt_mhs->execute(['keyword' => $search_param]);
    $mahasiswa_list = $stmt_mhs->fetchAll();
} catch (PDOException $e) {
    $mahasiswa_list = [];
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
    body {
        font-family: 'Roboto', sans-serif;
    }

    .container {
        max-width: 100%;
        padding-left: 50px;
        padding-right: 50px;
        padding-top: 20px;
        /* Tambahkan padding bawah di container agar konten tidak mepet footer */
        padding-bottom: 50px; 
    }
    
    @media (min-width: 768px) {
        .container {
            /* Dibuat lebih lebar agar 6 foto muat di baris atas pada layar besar */
            max-width: 1500px; 
        }
    }
    
    /* Styling untuk Lingkaran Foto - UKURAN FINAL LEBIH BESAR */
    .team-member-circle {
        /* Ukuran baru: 160px x 160px */
        width: 160px; 
        height: 160px; 
        
        object-fit: cover; 
        
        border-radius: 50%;
        border: 4px solid #f8f9fa;
        transition: transform 0.2s; 
    }
    
    /* Efek saat foto disorot mouse */
    .rounded-avatar-wrapper:hover .team-member-circle {
        transform: scale(1.05); 
        border-color: #0047AB;
    }

    .rounded-avatar-wrapper {
        display: inline-block;
        border-radius: 50%;
        /* Padding dibuat sedikit lebih besar untuk mengimbangi ukuran foto */
        padding: 7px; 
        border: 1px solid #adb5bd;
        cursor: pointer; 
        margin: 8px; /* Margin diperbesar sedikit */
    }

    .team-member-container {
        display: flex;
        flex-wrap: wrap; 
        justify-content: center;
        gap: 30px; /* Gap antar lingkaran dibuat lebih besar */
        margin-bottom: 40px; 
        padding: 10px 0; 
    }

    /* Pengaturan Flexbox untuk memastikan wrapping yang benar */
    .team-member-container .row {
        flex-grow: 1; 
        display: contents; 
    }

    .team-member-container .col-auto {
        flex: 0 0 auto; 
        width: auto;
    }

    /* Margin yang lebih besar untuk memisahkan baris bawah */
    .team-member-container .row:nth-child(2) {
        margin-top: 20px;
    }
    /* Akhir Perubahan Layout Foto */


    .category-header {
        background-color: #F9D723;
        color: #0047AB;
        padding: 5px 15px;
        border-radius: 5px;
        display: inline-block;
        font-weight: bold;
        margin-bottom: 15px;
    }
    .search-filter-row {
        margin-bottom: 15px;
    }
    
    /* Margin bawah pada tabel agar tidak mepet footer */
    .table-responsive {
        margin-bottom: 50px; 
    }

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

    .table-custom-layout th,
    .table-custom-layout td {
        border: 1px solid var(--color-table-header-border);
        height: 38px;
        padding: 0.5rem;
        text-align: center;
        font-weight: normal;
    }
    .table-custom-layout tbody tr:nth-child(odd) td {
        background-color: #F5F9FF;
    }
    .table-custom-layout tbody tr:nth-child(even) td {
        background-color: #fff;
    }

    /* Mengatur lebar kolom agar seimbang */
    .table-custom-layout th:nth-child(1), .table-custom-layout td:nth-child(1),
    .table-custom-layout th:nth-child(2), .table-custom-layout td:nth-child(2),
    .table-custom-layout th:nth-child(3), .table-custom-layout td:nth-child(3),
    .table-custom-layout th:nth-child(4), .table-custom-layout td:nth-child(4),
    .table-custom-layout th:nth-child(5), .table-custom-layout td:nth-child(5) {
        width: 20%; 
    }
    
    .input-search-custom {
        border-right: 1px solid #ced4da !important; 
        border-color: #ced4da;
        border-radius: .25rem !important; 
        padding-left: 3rem !important; 
        height: 38px; 
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

    .btn-filter-custom {
        background-color: #fff;
        color: #000;
        border: 1px solid #ced4da;
        padding: .375rem 1rem;
        height: 38px;
        border-radius: .25rem;
    }

    /* Link styling agar tidak merusak layout */
    a.member-link {
        text-decoration: none;
        display: inline-block;
    }
</style>

</head>
<body>

<?php include 'navbar.php'; ?>

<div class="banner1"> </div>

<div class="container">

    <div class="category-header">
        Anggota Tim
    </div>
    
    <div class="team-member-container">
        <div class="row w-100 justify-content-center">
            
            <?php if (count($team_members) > 0): ?>
                <?php foreach ($team_members as $member): ?>
                    <div class="col-auto">
                        <a href="Profil_dosen.php?id=<?= $member['id_dosen']; ?>" class="member-link" title="<?= htmlspecialchars($member['nama_dosen']); ?>">
                            <div class="rounded-avatar-wrapper">
                                <img src="<?= htmlspecialchars($member['foto_dosen']); ?>" 
                                     class="team-member-circle" 
                                     alt="<?= htmlspecialchars($member['nama_dosen']); ?>"
                                     onerror="this.src='Asset/default_profile.jpg';"> </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">Data anggota tim belum tersedia.</div>
            <?php endif; ?>

        </div>
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
                    <th>Nim</th>
                    <th>Nama Mahasiswa</th>
                    <th>Jurusan</th>
                    <th>Prodi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($mahasiswa_list) > 0): ?>
                    <?php foreach ($mahasiswa_list as $mhs): ?>
                    <tr>
                        <td><?= htmlspecialchars($mhs['nim']); ?></td>
                        <td><?= htmlspecialchars($mhs['nama_mahasiswa']); ?></td>
                        <td>Teknologi Informasi</td> 
                        <td><?= htmlspecialchars($mhs['prodi']); ?></td>
                        <td><?= htmlspecialchars($mhs['status_mahasiswa']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data mahasiswa ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>