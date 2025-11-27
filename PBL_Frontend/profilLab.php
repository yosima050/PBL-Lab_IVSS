<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
        /* Base styles from original */
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFFFFF; /* Light Gray */
            color: #333; /* Dark Text */
            line-height: 1.6;
        }

        /* Header styling - background: #f9d723; padding: 10px 20px; border-radius: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
        .custom-header {
            background-color: #f9d723; /* Yellow-ish */
            padding: 10px 20px; 
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            display: inline-block;
        }
        
        .custom-header h1 {
            color: #333;
            font-size: 20px; 
            font-weight: bold;
        }
        .custom-header h2 { 
            color: #333;
            font-size: 20px; 
            font-weight: bold;
        }

        /* Main Content - background: #e6f0ff; margin: 20px 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); */
        .custom-main-content {
            background-color: #F5F9FF; /* Light Blue */
            margin: 20px 30px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Lab Image - box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 10px; */
        .lab-image-style {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Description text */
        .description-section p, .section-content p, .section-content ul li {
            text-align: justify;
            font-size: 14px;
            color: #333;
        }

        /* Vision and Mission Title (the "Visi" and "Misi" blocks) */
        .custom-vision-mission-title {
            background-color: #f9d723; /* Yellow-ish */
            padding: 8px 20px; 
            text-align: left;
            margin-top: 20px;
            margin-left: 30px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: inline-block;
            border-radius: 25px;
            font-weight: bold;
            color: #333;
            font-size: 18px; 
            /* Perubahan utama: Menambahkan jarak di bawah tombol */
            margin-bottom: 10px; 
        }

        /* Section Container - background: white; margin: 20px 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
        .custom-section-container {
            background-color: white;
            /* Mengurangi margin-top karena jarak sudah diberikan oleh tombol kuning di atasnya */
            margin: 10px 30px 30px 30px; 
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Section Content - padding: 25px; background: #e6f0ff; border-radius: 0 0 10px 10px; */
        .custom-section-content {
            padding: 25px;
            background-color: #F5F9FF; /* Light Blue */
            border-radius: 0 0 10px 10px;
        }
        
        .custom-section-content ul {
            list-style-position: outside;
            margin-left: 20px;
        }

        /* Responsive Design Emulation */
        @media (max-width: 768px) {
            .custom-main-content {
                margin: 15px;
            }
            .custom-header, .custom-vision-mission-title {
                margin-left: auto !important; 
                margin-right: auto !important;
            }
            .custom-header h1, .custom-header h2 { 
                font-size: 18px; 
            }
            .custom-vision-mission-title {
                margin-left: 15px; 
                font-size: 16px; 
                /* Mengurangi margin di layar kecil agar tidak terlalu jauh */
                margin-bottom: 8px; 
            }
            .custom-section-container {
                margin: 8px 15px 30px 15px; 
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="text-center">
            <div class="custom-header d-inline-block">
                <h1>Profil Laboratorium</h1>
            </div>
        </div>
        
        <div class="custom-main-content">
            <div class="row g-4 g-lg-5">
                <div class="col-12 col-lg-4">
                    <div class="image-section">
                        <img src="lab-image.jpg" alt="Laboratorium" class="img-fluid lab-image-style mx-auto d-block" style="max-width: 300px;">
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

        <div class="text-center">
             <div class="custom-header d-inline-block">
                <h2>Visi dan Misi</h2>
            </div>
        </div>
        <br>
        
        <div class="custom-vision-mission-title">Visi</div>
        <div class="custom-section-container">
            <div class="custom-section-content">
                <p>
                    Menjadi laboratorium unggulan dalam pengembangan teknologi penglihatan cerdas (**Intelligent Vision**) dan sistem cerdas terintegrasi (**Smart Systems**) yang inovatif, aplikatif, serta berdaya saing nasional dan internasional untuk mendukung kemajuan bidang teknologi informasi dan industri berbasis kecerdasan buatan.
                </p>
            </div>
        </div>

        <div class="custom-vision-mission-title">Misi</div>
        <div class="custom-section-container" style="margin-bottom: 20px;"> 
            <div class="custom-section-content">
                <ul>
                    <li>Melaksanakan penelitian dan inovasi di bidang **computer vision, artificial intelligence,** dan **smart systems** yang berorientasi pada kebutuhan industri dan masyarakat.</li>
                    <li>Menyediakan fasilitas riset dan pelatihan bagi dosen dan mahasiswa Polinema dalam pengembangan sistem berbasis penglihatan komputer, pembelajaran mesin, dan **Internet of Things (IoT)**.</li>
                    <li>Mendorong kolaborasi akademik dan industri dalam penerapan teknologi **intelligent vision** dan **smart systems** untuk menghasilkan solusi nyata dan berkelanjutan.</li>
                    <li>Menghasilkan publikasi ilmiah, prototype, dan produk inovatif yang mendukung reputasi Polinema sebagai institusi vokasi berkelas internasional.</li>
                    <li>Mengembangkan ekosistem pembelajaran adaptif berbasis riset untuk mencetak sumber daya manusia unggul di bidang kecerdasan buatan dan sistem cerdas.</li>
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>