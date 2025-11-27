<?php
$team_members = [
    'member1.jpg', // Ganti dengan path gambar yang sebenarnya
    // ... dan seterusnya
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team & Student List (Tampilan Desktop Full Lebar)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* --- DEFINISI WARNA BARU --- */
        :root {
            /* Implementasi Warna Sesuai Permintaan */
            --color-dark-blue: #0047AB;
            --color-light-blue: #DFECFF;

            /* Mapping variable fungsional ke warna baru */
            /* Header dan Border menggunakan Dark Blue (#0047AB) */
            --color-table-header-border: var(--color-dark-blue); 
            
            /* Striping Baris Ganjil menggunakan Light Blue (#DFECFF) */
            --color-table-striped: var(--color-light-blue); 
        }

        /* CSS Tambahan untuk Layout Halaman */
        .container {
            max-width: 100%; 
            padding-left: 50px; 
            padding-right: 50px;
            padding-top: 20px;
        }
        
        @media (min-width: 768px) {
            .container {
                max-width: 1300px; 
            }
        }
        
        .team-member-circle {
            width: 100px; 
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #f8f9fa;
        }
        .team-member-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px; 
            margin-bottom: 20px;
        }
        .category-header {
            background-color: #F9D723; 
            color: #212529;
            padding: 5px 15px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .search-filter-row {
            margin-bottom: 15px;
        }
        
        /* --- STYLING TABEL --- */
        
        .table-custom-layout {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse; /* Penting untuk border 1px */
        }
        
        /* Header Tabel (Background menggunakan Dark Blue) */
        .table-custom-layout thead th {
            background-color: var(--color-table-header-border); /* #0047AB */
            color: #fff; /* Teks Putih */
            font-weight: bold;
            border: 1px solid var(--color-table-header-border); /* #0047AB */
            height: 38px; 
            padding: 0.5rem;
            text-align: center;
        }

        /* Border Sel (Border menggunakan Dark Blue) */
        .table-custom-layout th, 
        .table-custom-layout td {
            border: 1px solid var(--color-table-header-border); /* #0047AB */
            height: 38px; 
            padding: 0.5rem;
            text-align: center;
            font-weight: normal; 
        }
        
        /* Striping Baris (Baris Ganjil menggunakan Light Blue) */
        .table-custom-layout tbody tr:nth-child(odd) {
            background-color: var(--color-table-striped); /* #DFECFF */
        }

        /* Striping Baris (Baris Genap Putih) */
        .table-custom-layout tbody tr:nth-child(even) {
            background-color: #fff; 
        }
        
        /* Atur lebar kolom agar merata (20% tiap kolom) */
        .table-custom-layout th:nth-child(1), .table-custom-layout td:nth-child(1),
        .table-custom-layout th:nth-child(2), .table-custom-layout td:nth-child(2),
        .table-custom-layout th:nth-child(3), .table-custom-layout td:nth-child(3),
        .table-custom-layout th:nth-child(4), .table-custom-layout td:nth-child(4),
        .table-custom-layout th:nth-child(5), .table-custom-layout td:nth-child(5) {
            width: 20%; 
        }

        /* Styling Input Group */
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

        /* Styling Avatar */
        .rounded-avatar-wrapper {
            display: inline-block;
            border-radius: 50%;
            padding: 2px; 
            border: 1px solid #adb5bd; 
        }
    </style>
</head>
<body>

<div class="container">

    <div class="category-header">
        Anggota Tim
    </div>
    
    <div class="team-member-container">
        <div class="row w-100 justify-content-center">
            <div class="col-auto">
                <div class="rounded-avatar-wrapper">
                    <img src="https://via.placeholder.com/70/4e73df/ffffff?text=M1" class="team-member-circle" alt="Member 1">
                </div>
            </div>
            <div class="col-auto">
                <div class="rounded-avatar-wrapper">
                    <img src="https://via.placeholder.com/70/e74a3b/ffffff?text=M2" class="team-member-circle" alt="Member 2">
                </div>
            </div>
            <div class="col-auto">
                <div class="rounded-avatar-wrapper">
                    <img src="https://via.placeholder.com/70/1cc88a/ffffff?text=M3" class="team-member-circle" alt="Member 3">
                </div>
            </div>
            <div class="col-auto">
                <div class="rounded-avatar-wrapper">
                    <img src="https://via.placeholder.com/70/36b9cc/ffffff?text=M4" class="team-member-circle" alt="Member 4">
                </div>
            </div>
            <div class="col-auto">
                <div class="rounded-avatar-wrapper">
                    <img src="https://via.placeholder.com/70/f6c23e/ffffff?text=M5" class="team-member-circle" alt="Member 5">
                </div>
            </div>
            <div class="col-auto">
                <div class="rounded-avatar-wrapper">
                    <img src="https://via.placeholder.com/70/858796/ffffff?text=M6" class="team-member-circle" alt="Member 6">
                </div>
            </div>
            <div class="col-auto">
                <div class="rounded-avatar-wrapper">
                    <img src="https://via.placeholder.com/70/6f42c1/ffffff?text=M7" class="team-member-circle" alt="Member 7">
                </div>
            </div>
        </div>
    </div>
    
    <div class="category-header">
        Mahasiswa
    </div>

    <div class="row search-filter-row align-items-center">
        <div class="col-12 d-flex justify-content-between">
            
            <div class="input-group-custom me-2" style="max-width: 300px;"> 
                <i class="fas fa-search"></i>
                <input type="text" class="form-control input-search-custom" placeholder="Cari Kategori...">
            </div>
            
            <button class="btn btn-filter-custom" type="button">
                <i class="fas fa-filter"></i> Filter
            </button>
            
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-custom-layout">
            <thead>
                <tr>
                    <th>Kolom 1</th>
                    <th>Kolom 2</th>
                    <th>Kolom 3</th>
                    <th>Kolom 4</th>
                    <th>Kolom 5</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Loop untuk membuat 15 baris kosong
                for ($i = 0; $i < 15; $i++): 
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>