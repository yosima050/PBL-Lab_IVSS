<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Produk dan Riset</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Terapkan font-family Roboto ke seluruh body */
        body {
            font-family: 'Roboto', sans-serif;
        }

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
        .custom-badge-active {
            background-color: #0047AB;
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: normal;
        }
        .custom-badge-published {
            background-color: #0047AB;
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: normal;
        }
        .custom-badge-progress {
            background-color: #0047AB;
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: normal;
        }
        .custom-detail-button {
            background-color: #FFFCED;
            color: #212529;
            font-weight: bold;
            width: 100%;
            border: none;
        }
        .custom-card {
            border: 2px solid #0047AB !important;
            border-radius: 0.375rem;
            margin-bottom: 15px;
            padding: 15px;
        }
        .bg-light-gray {
            background-color: #F5F9FF;
            border-radius: 0.375rem;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="banner1"> </div>
<div class="container py-4">

    <div class="custom-yellow-header">
        Halaman Produk dan Riset
    </div>
    
    <div class="bg-light-gray shadow-sm">
        
        <h4 class="mb-3 d-flex align-items-center">
            Produk Riset
        </h4>

        <div class="input-group mb-4">
            <input type="text" class="form-control" placeholder="Cari Produk, Publikasi" aria-label="Cari Produk, Publikasi">
            <button class="btn btn-light border" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter" viewBox="0 0 16 16">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Filter
            </button>
        </div>

        <div class="custom-card shadow-sm">
            <div class="custom-card-title">
                Produk Riset
            </div>
            <h5 class="card-title">[Judul Produk Riset]</h5>
            <p class="card-text">Deskripsi singkat produk riset yang dikembangkan oleh dosen</p>
            <div class="mb-3">
                <span class="custom-badge-active">Aktif</span>
            </div>
            <a href="#" class="btn custom-detail-button">Lihat Detail</a>
        </div>

        <div class="custom-card border-0 shadow-sm">
            <div class="custom-card-title">
                Publikasi
            </div>
            <h5 class="card-title">[Judul Publikasi Jurnal]</h5>
            <p class="card-text">Paper yang telah dipublikasikan di jurnal</p>
            <div class="mb-3">
                <span class="custom-badge-published">Published</span>
            </div>
            <a href="#" class="btn custom-detail-button">Lihat Detail</a>
        </div>

        <div class="custom-card border-0 shadow-sm">
            <div class="custom-card-title">
                Proyek Aktif
            </div>
            <h5 class="card-title">[Nama Proyek Penelitian]</h5>
            <p class="card-text">Proyek kolaborasi dengan industri</p>
            <div class="mb-3">
                <span class="custom-badge-progress">In Progress</span>
            </div>
            <a href="#" class="btn custom-detail-button">Lihat Detail</a>
        </div>

    </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>