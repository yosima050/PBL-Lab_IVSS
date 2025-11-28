<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Dosen - Lab IVSS</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="dashboard/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styleube.css">
    <link rel="stylesheet" href="PBL_Frontend/navbar.css">

    <style>
        .banner1{
            background:url(../Asset/Coba.jpg) no-repeat 0px 0px;
            background-size:cover;
            min-height:300px;
        }
        body.bg-profile {
            background-color: #dbeafe;
        }
        .profile-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 30px;
            margin-top: 30px;
            margin-bottom: 30px;
            border: 1px solid #e3e6f0;
        }

        /* Styling Foto Profil */
        .profile-img-box {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .profile-img-box img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Styling Teks Sidebar (Kiri) */
        .sidebar-label {
            font-weight: 700;
            color: #0047AB; /* Warna biru Polinema */
            margin-bottom: 2px;
            font-size: 0.95rem;
        }
        .sidebar-value {
            color: #333;
            margin-bottom: 15px;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .contact-list {
            list-style: none;
            padding: 0;
        }
        .contact-list li {
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        .contact-list strong {
            color: #0047AB;
        }

        /* Styling Konten Utama (Kanan) */
        .profile-name {
            font-weight: 800;
            color: #000;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .tag-badge {
            background-color: #dbeafe;
            color: #555;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid #ddd;
            display: inline-block;
            margin-bottom: 15px;
        }

        .social-btn-group .btn-outline-primary {
            border-color: #0047AB;
            color: #0047AB;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
            padding: 5px 15px;
        }
        .social-btn-group .btn-outline-primary:hover {
            background-color: #0047AB;
            color: #fff;
        }

        .section-title {
            color: #0047AB;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .info-card {
            border: 1.5px solid #0047AB;
            border-radius: 10px;
            padding: 20px;
            background: #fff;
            margin-bottom: 20px;
            min-height: 100px;
        }

        .info-card h5 {
            color: #0047AB;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .info-card ul {
            padding-left: 20px;
            margin-bottom: 0;
        }
        .info-card ul li {
            margin-bottom: 8px;
            color: #0047AB; /* Bullet points biru */
        }
        .info-card ul li span {
            color: #333; /* Teks isi hitam/abu */
        }

        hr.divider {
            margin: 20px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>

    <?php include 'PBL_Frontend/navbar.php'; ?>
    
        <div class="banner1"> </div>

    <div class="container">
        <div class="profile-container">
            <div class="row">
                
                <div class="col-md-4 border-right-custom">
                    <div class="profile-img-box">
                        <img src="Asset/unnamed.jpg" alt="Foto Profil Dosen">
                    </div>

                    <div class="mb-3">
                        <div class="sidebar-label">NIP</div>
                        <div class="sidebar-value">19780327200312200</div>
                        
                        <div class="sidebar-label">NIDN</div>
                        <div class="sidebar-value">4314058001</div>

                        <div class="sidebar-label">Program Studi</div>
                        <div class="sidebar-value">Rekayasa Teknologi Informasi</div>

                        <div class="sidebar-label">Jabatan</div>
                        <div class="sidebar-value">Ketua Laboratorium IVSS</div>
                    </div>

                    <hr class="divider">

                    <div class="mb-3">
                        <div class="sidebar-label mb-2">Kontak</div>
                        <ul class="contact-list">
                            <li>
                                <strong>EMAIL:</strong><br>
                                rosiani@polinema.ac.id
                            </li>
                            <li>
                                <strong>Alamat Kantor:</strong><br>
                                Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141
                            </li>
                            <li>
                                <strong>Website:</strong><br>
                                -
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-8 pl-md-4">
                    
                    <h1 class="profile-name">Dr. Ulla Delfana Rosiani, ST., MT.</h1>
                    
                    <div class="tag-badge">Information System</div>

                    <div class="social-btn-group mb-4">
                        <a href="https://www.linkedin.com/" target="_blank" class="btn btn-outline-primary btn-sm">LinkedIn</a>
                        <a href="https://scholar.google.com/" target="_blank" class="btn btn-outline-primary btn-sm">Google Scholar</a>
                        <a href="https://sinta.kemdiktisaintek.go.id/home/index/" target="_blank" class="btn btn-outline-primary btn-sm">Sinta</a>
                        <a href="https://rosiani@polinema.ac.id" target="_blank" class="btn btn-outline-primary btn-sm">Email</a>
                        <a href="#" target="_blank" class="btn btn-outline-primary btn-sm">CV</a>
                </div>

                    <div class="section-title">Pendidikan, Sertifikasi & Mata Kuliah</div>

                    <div class="info-card">
                        <h5>Pendidikan</h5>
                        <ul>
                            <li>
                                <span><strong>S3 - Doktor</strong><br>
                                Institut Teknologi Sepuluh Nopember (2021)</span>
                            </li>
                            <li>
                                <span><strong>S2 - Magister Teknik</strong><br>
                                Universitas Brawijaya (2010)</span>
                            </li>
                            <li>
                                <span><strong>S1 - Sarjana Teknik</strong><br>
                                Universitas Brawijaya (2001)</span>
                            </li>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h5>Sertifikasi</h5>
                        <ul>
                            <li><span>-</span></li>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h5>Mata Kuliah</h5>
                        
                        <div class="mb-2" style="color:#0047AB; font-weight:600;">Semester Genap</div>
                        <ul>
                            <li><span>Pengembangan Karir</span></li>
                            <li><span>Analisis Proses Bisnis</span></li>
                        </ul>

                        <div class="mt-3 mb-2" style="color:#0047AB; font-weight:600;">Semester Ganjil</div>
                        <ul>
                            <li><span>Pengantar Akuntasi, Manajemen, dan Bisnis</span></li>
                            <li><span>Manajemen Produk</span></li>
                            <li><span>Kewirausahaan Berbasis Teknologi</span></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    
</body>
</html>