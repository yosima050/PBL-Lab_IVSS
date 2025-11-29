<?php
$team_members = [
    'member1.jpg', // Ganti dengan path gambar
    // ... dan seterusnya
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team & Student List (Tampilan Desktop Full Lebar)</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --color-dark-blue: #0047AB;
            --color-light-blue: #F5F9FF
;

            --color-table-header-border: var(--color-dark-blue);

            --color-table-striped: var(--color-light-blue);
        }

        .banner1{
            background:url(../Asset/Coba.jpg) no-repeat 0px 0px;
            background-size:cover;
            min-height:250px;
        }

        /* Terapkan font-family Roboto ke seluruh body */
        body {
            font-family: 'Roboto', sans-serif;
        }

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
            background-color: #F5F9FF
        }
        .table-custom-layout tbody tr:nth-child(even) td {
            background-color: #fff;
        }

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

        .rounded-avatar-wrapper {
            display: inline-block;
            border-radius: 50%;
            padding: 2px;
            border: 1px solid #adb5bd;
        }
    </style>
</head>
<body>
<div class="banner1"> </div>
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
                    <th>Nim</th>
                    <th>Nama Mahasiswa</th>
                    <th>Jurusan</th>
                    <th>Prodi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
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