<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorotan Publikasi - Lab IVSS</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="dashboard/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styleSP.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="banner1">
    </div>
    <body>
    <div class="container">
        <div class="fokus-riset">Fokus Riset</div>
        
        <div class="tags">
            <span class="tag">Intelligent Vision</span>
            <span class="tag">Smart Systems</span>
        </div>

        <div class="sorotan-publikasi">Sorotan Publikasi</div>

        <div class="filter-buttons">
            <button class="filter-btn">Latest</button>
            <button class="filter-btn">Oldest</button>
            <div class="years-dropdown">
                <button class="filter-btn">Years ▼</button>
                <div class="dropdown-content">
                    <a href="#">2025</a>
                    <a href="#">2024</a>
                    <a href="#">2023</a>
                </div>
            </div>
        </div>

        <div class="publications-grid">
            <div class="publication-card">
                <h3>Segmentasi berbasis k-means pada deteksi citra penyakit daun tanaman jagung</h3>
                <div class="publication-date">15 Nov 2025</div>
                <button class="baca-btn">Baca</button>
            </div>

            <div class="publication-card">
                <h3>Klasifikasi Jenis Kelamin Pada Citra Wajah Menggunakan Metode Naive Bayes</h3>
                <div class="publication-date">25 Nov 2025</div>
                <button class="baca-btn">Baca</button>
            </div>

            <div class="publication-card">
                <h3>Sistem Pengambil Keputusan Rekomendasi Lokasi Wisata Malang Raya Dengan Metode MOORA</h3>
                <div class="publication-date">5 Dec 2025</div>
                <button class="baca-btn">Baca</button>
            </div>

            <div class="publication-card">
                <h3>Penerapan Facial Landmark Point Untuk Klasifikasi Jenis Kelamin Berdasarkan Citra Wajah</h3>
                <div class="publication-date">15 Dec 2025</div>
                <button class="baca-btn">Baca</button>
            </div>

            <div class="publication-card">
                <h3>Comparison of recognition accuracy on dynamic hand gesture using feature selection</h3>
                <div class="publication-date">19 Dec 2025</div>
                <button class="baca-btn">Baca</button>
            </div>

            <div class="publication-card">
                <h3>Subpixel subtle motion estimation of micro-expressions multiclass classification</h3>
                <div class="publication-date">28 Dec 2025</div>
                <button class="baca-btn">Baca</button>
            </div>
        </div>

        <div class="pagination">
            <button>◄ Previous</button>
            <span>1-6 of 79</span>
            <button>Next ►</button>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>