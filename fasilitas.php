<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Fasilitas 3 Kolom - Warna Kustom</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --color-dark-blue: #0047AB;
            --color-light-blue: #DFECFF;

            --color-table-header-border: #004C99;
            --color-table-striped: #F5F9FF;
        }

        .banner1{
            background:url(../Asset/Coba.jpg) no-repeat 0px 0px;
            background-size:cover;
            min-height:250px;
        }

        /* Terapkan font-family Roboto ke seluruh body */
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
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
        }

        .table-custom tbody tr:nth-child(odd) {
            background-color: var(--color-table-striped);
        }
        .table-custom tbody tr:nth-child(even) {
            background-color: #fff;
        }
        
    </style>
</head>
<body>
<div class="banner1"> </div>
<div class="container">
    
    <button class="btn btn-mahasiswa-custom" type="button">Fasilitas dan Halaman Produk</button>

    <div class="row align-items-center search-filter-row">
        <div class="col-12 d-flex">
            <div class="input-group-custom flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control input-search-custom" placeholder="Cari Kategori...">
            </div>
            
            <button class="btn btn-filter-custom" type="button">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </div>
    
    <div class="table-custom-container">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 33.33%;">Nama Fasilitas</th>
                    <th style="width: 33.33%;">Deskripsi</th>
                    <th style="width: 33.33%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $jumlah_baris = 15;
                for ($i = 0; $i < $jumlah_baris; $i++): 
                ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php endfor; ?>
                </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>