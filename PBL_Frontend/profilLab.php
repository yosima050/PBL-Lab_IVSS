<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="navbar.css"> 
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFFFFF;
            color: #333;
            line-height: 1.6;
        }
        .custom-header {
            background-color: #f9d723;
            padding: 10px 20px;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
            display: inline-block;
        }
        .custom-header h1, .custom-header h2 {
            color: #0047AB;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }
        .custom-main-content {
            background-color: #F5F9FF;
            margin: 20px 30px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .lab-image-style {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .description-section p, .section-content p, .section-content ul li {
            text-align: justify;
            font-size: 14px;
            color: #333;
        }
        .custom-vision-mission-title {
            background-color: #f9d723;
            padding: 8px 20px;
            text-align: left;
            margin-top: 20px;
            margin-left: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: inline-block;
            border-radius: 25px;
            font-weight: bold;
            color: #0047AB;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .custom-section-container {
            background-color: white;
            margin: 10px 30px 30px 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .custom-section-content {
            padding: 25px;
            background-color: #F5F9FF;
            border-radius: 0 0 10px 10px;
        }
        .custom-section-content ul { list-style-position: outside; margin-left: 20px; }
        
        /* Tambahan agar judul tidak tertutup navbar saat di-klik */
        #visi-misi {
            scroll-margin-top: 100px; 
        }

        @media (max-width: 768px) {
            .custom-main-content { margin: 15px; }
            .custom-header, .custom-vision-mission-title {
                margin-left: auto !important;
                margin-right: auto !important;
            }
            .custom-header h1, .custom-header h2 { font-size: 18px; }
            .custom-vision-mission-title { margin-left: 15px; font-size: 16px; margin-bottom: 8px; }
            .custom-section-container { margin: 8px 15px 30px 15px; }
        }
    </style>
</head>
<body>
    
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container-fluid p-0">
        <div class="text-center">
            <div class="custom-header d-inline-block">
                <h1>Tentang Kami</h1>
            </div>
        </div>

        <div class="custom-main-content">
            <div class="row g-4 g-lg-5">
                <div class="col-12 col-lg-4">
                    <div class="image-section">
                        <img src="../images/labivss.jpg" alt="Laboratorium" class="img-fluid lab-image-style mx-auto d-block" style="max-width:300px;">
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="description-section d-flex flex-column gap-3 h-100">
                        <p>
                            Laboratorium Visi Cerdas dan Sistem Cerdas merupakan pusat riset dan pengembangan di bawah Jurusan Teknologi Informasi Politeknik Negeri Malang yang berfokus pada bidang intelligent vision, dan smart system. Laboratorium ini menjadi wadah bagi dosen dan mahasiswa untuk melakukan penelitian, pembelajaran, serta pelatihan dalam pengembangan sistem cerdas berbasis pengolahan citra dan kecerdasan buatan.
                        </p>
                        <p>
                            Penelitian di laboratorium ini mengintegrasikan computer vision, AI, dan IoT untuk menciptakan solusi inovatif yang mampu mengenali, menganalisis, serta merespon lingkungan secara mandiri.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center" id="visi-misi">
            <div class="custom-header d-inline-block"><h2>Visi dan Misi</h2></div>
        </div>

        <div class="custom-vision-mission-title">Visi</div>
        <div class="custom-section-container">
            <div class="custom-section-content">
                <p>Menjadi laboratorium unggulan dalam pengembangan teknologi penglihatan cerdas (Intelligent Vision) dan sistem cerdas terintegrasi (Smart Systems) yang inovatif, aplikatif, serta berdaya saing nasional dan internasional untuk mendukung kemajuan bidang teknologi informasi dan industri berbasis kecerdasan buatan.</p>
            </div>
        </div>

        <div class="custom-vision-mission-title">Misi</div>
        <div class="custom-section-container" style="margin-bottom:20px;">
            <div class="custom-section-content">
                <ul>
                    <li>Melaksanakan penelitian dan inovasi di bidang computer vision, artificial intelligence, dan smart systems yang berorientasi pada kebutuhan industri dan masyarakat.</li>
                    <li>Menyediakan fasilitas riset dan pelatihan bagi dosen dan mahasiswa Polinema dalam pengembangan sistem berbasis penglihatan komputer, pembelajaran mesin, dan Internet of Things (IoT).</li>
                    <li>Mendorong kolaborasi akademik dan industri dalam penerapan teknologi intelligent vision dan smart systems untuk menghasilkan solusi nyata dan berkelanjutan.</li>
                    <li>Menghasilkan publikasi ilmiah, prototype, dan produk inovatif yang mendukung reputasi Polinema sebagai institusi vokasi berkelas internasional.</li>
                    <li>Mengembangkan ekosistem pembelajaran adaptif berbasis riset untuk mencetak sumber daya manusia unggul di bidang kecerdasan buatan dan sistem cerdas.</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>