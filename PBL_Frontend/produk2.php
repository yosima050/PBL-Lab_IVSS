<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk Riset</title>
<<<<<<< HEAD
    <link rel="stylesheet" href="produk2.css">
</head>
<body>
    <div class="container">
        <!-- Judul Produk Riset -->
        <h1 class="main-title">[Judul Produk Riset]</h1>
        
        <!-- Judul Lengkap Item dan Status -->
        <div class="header-section">
            <h2 class="item-title">[Judul Lengkap Item]</h2>
            <div class="status-badge">Status: [Aktif/Published/Progress]</div>
        </div>

        <!-- Deskripsi Section -->
        <div class="section">
            <h3 class="section-title">Deskripsi</h3>
            <div class="description-text">
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1950s with the release of Letraset sheets containing Loren Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Loren Ipsum.</p>
            </div>
        </div>

        <!-- Garis Pemisah -->
        <hr class="divider">

        <!-- Informasi Detail Section -->
        <div class="section">
            <h3 class="section-title">Informasi Detail</h3>
            <div class="detail-info">
                <div class="info-item">
                    <span class="info-label">Tanggal Mulai:</span>
                    <span class="info-value">DD/MM/YYYY</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Seleasi:</span>
                    <span class="info-value">DD/MM/YYYY</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Peruilisi/Tim:</span>
                    <span class="info-value">Nama Dosen</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kategori:</span>
                    <span class="info-value">[Kategori]</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Lokasi/Jurnal:</span>
                    <span class="info-value">[Info]</span>
=======
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .header-riset {
            background-color: #ffe066;
            padding: 0.5rem 1rem;
            color: #0047AB;
            font-weight: bold;
            font-size: 1.35rem;
            border-radius: 0.375rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            width: fit-content;
        }
        
        .header-riset span {
            margin-left: 0.5rem;
            font-size: 1.35rem;
        }
        .content-card {
            background-color: #F5F9FF !important;
        }
        
        .card-shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .detail-box {
            border: 2px solid #0047AB;
            padding: 1rem;
            border-radius: 0.375rem;
            background-color: transparent;
        }

        .deskripsi-box {
            border: 2px solid #0047AB;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }

        .dokumen-container {
            background-color: #FFFCED;
            padding: 1rem;
            border-radius: 0.375rem;
        }

        .dokumen-container .list-group-flush {
        background-color: transparent;
        }

        .image-placeholder {
            background-color: #FFFCED;
            height: 200px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            border-radius: 0.375rem;
            font-size: 3rem;
            color: #ccc;
        }

        .bg-custom-blue {
            background-color: #0047AB !important;
        }
    </style>
</head>
<body>

    <?php
        // --- Data PHP Simulasi ---
        $judul_riset = "[Judul Produk Riset]";
        $judul_lengkap = "Judul Lengkap Item";
        $status = "AKTIF/PUBLISHED/PROGRESS"; 
        $deskripsi = "Lorem ipsum is simply dummy text of the printing and typesetting industry. Lorem ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
        $tgl_mulai = "DD/MM/YYYY";
        $tgl_selesai = "DD/MM/YYYY";
        $penulis_tim = "Nama Dosen";
        $kategori = "[Kategori]";
        $lokasi_jurnal = "[Info]";
        $dokumen_1 = "Dokumen_1.pdf";
        $dokumen_2 = "Dokumen_2.pdf";
    ?>

    <div class="container my-5">
        
        <div class="header-riset">
            Halaman Produk dan Riset
        </div>

        <div class="card card-shadow border-0 p-4 content-card">
            
            <div class="d-flex align-items-center mb-4">
                <a href="#" class="text-decoration-none me-3 text-dark">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h2 class="h5 m-0 text-muted ms-auto"><?php echo $judul_riset; ?></h2>
            </div>
            
            <div class="image-placeholder mb-4">
                <i class="fas fa-image"></i>
            </div>
            
            <h3 class="h4"><?php echo $judul_lengkap; ?></h3>
            
            <p class="mb-4">
                    <span class="badge bg-custom-blue text-uppercase">
                    <?php echo $status; ?>
                </span>
            </p>
            
            <h4 class="h5">Deskripsi</h4>
            
            <div class="deskripsi-box">
                <p class="text-secondary m-0">
                    <?php echo $deskripsi; ?>
                </p>
            </div>

            <hr class="my-4">
            
            <h4 class="h5">Informasi Detail</h4>
            <div class="detail-box mb-4">
                <p class="mb-1">Tanggal Mulai: **<?php echo $tgl_mulai; ?>**</p>
                <p class="mb-1">Tanggal Selesai: **<?php echo $tgl_selesai; ?>**</p>
                <p class="mb-1">Penulis/Tim: **<?php echo $penulis_tim; ?>**</p>
                <p class="mb-1">Kategori: **<?php echo $kategori; ?>**</p>
                <p class="m-0">Lokasi/Jurnal: **<?php echo $lokasi_jurnal; ?>**</p>
            </div>
            
            <h4 class="h5">Dokumen Terkait</h4>
            <div class="dokumen-container mb-4">
                <div class="list-group list-group-flush bg-transparent">
                    
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span><?php echo $dokumen_1; ?></span>
                        <a href="#" class="btn btn-sm btn-outline-secondary">
                            [Download]
                        </a>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span><?php echo $dokumen_2; ?></span>
                        <a href="#" class="btn btn-sm btn-outline-secondary">
                            [Download]
                        </a>
                    </div>
                    
>>>>>>> 90301ecd3d451330be25094abe264ab394e9b779
                </div>
            </div>
        </div>

<<<<<<< HEAD
        <!-- Garis Pemisah -->
        <hr class="divider">

        <!-- Dokumen Terkait Section -->
        <div class="section">
            <h3 class="section-title">Dokumen Terkait</h3>
            <div class="document-list">
                <div class="document-item">
                    <span class="document-name">Dokumen_1.pdf</span>
                    <button class="download-btn">[Download]</button>
                </div>
                <div class="document-item">
                    <span class="document-name">Dokumen_2.pdf</span>
                    <button class="download-btn">[Download]</button>
                </div>
            </div>
        </div>
    </div>
=======
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> 90301ecd3d451330be25094abe264ab394e9b779
</body>
</html>