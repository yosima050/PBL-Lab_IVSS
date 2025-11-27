<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk Riset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        /* Gaya Kustom untuk Header Kuning (Disetel agar 'press') */
        .header-riset {
            background-color: #ffe066; 
            padding: 0.5rem 1rem; 
            color: #333;
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

        /* Container Produk Riset menjadi #F5F9FF */
        .content-card {
            background-color: #F5F9FF !important; 
        }
        
        .card-shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); 
        }
        
        /* Container Informasi Detail menjadi #FFFBE4 */
        .detail-box {
            border: 1px solid #007bff; 
            padding: 1rem;
            border-radius: 0.375rem;
            background-color: #FFFCED; 
        }

        /* PERUBAHAN BARU: Container Dokumen Terkait menjadi Kuning Muda */
        .dokumen-container {
            background-color: #FFFCED; /* Warna Kuning muda sesuai gambar */
            padding: 1rem; 
            border-radius: 0.375rem; 
        }

        /* Hilangkan background list-group di dalamnya agar transparan */
        .dokumen-container .list-group-flush {
             background-color: transparent;
        }
        
        /* Style untuk meniru placeholder gambar abu-abu */
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
        // --- Akhir Data PHP Simulasi ---
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
                <h2 class="h5 m-0 text-muted"><?php echo $judul_riset; ?></h2>
            </div>
            
            <div class="image-placeholder mb-4">
                <i class="fas fa-image"></i>
            </div>
            
            <h3 class="h4"><?php echo $judul_lengkap; ?></h3>
            <p class="mb-4">
                **Status:** <span class="badge bg-success text-uppercase">
                    <?php echo $status; ?>
                </span>
            </p>
            
            <h4 class="h5">Deskripsi</h4>
            <p class="text-secondary">
                <?php echo $deskripsi; ?>
            </p>

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
                    
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>