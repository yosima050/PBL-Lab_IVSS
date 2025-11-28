<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas & Dokumentasi - Lab IVSS</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="dashboard/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styleAD.css">
    <link rel="stylesheet" href="PBL_Frontend/navbar.css">
    <link rel="stylesheet" href="PBL_Frontend/footer.css">
</head>

<body>
<?php include 'PBL_Frontend/navbar.php'; ?>
<div class="banner1"> </div>

<div class="container">
    <div class="tag1">
        <span>Aktivitas dan Dokumentasi</span>
        <img src="Asset/logo.png" class="emoji">
    </div>
</div>

<div class="profile-container">
    <div class="profile-container">
        <div class="activities-header">
            <h2>Aktivitas Laboratorium</h2>
        </div>

        <div class="search-filter-row">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari Aktivitas..." id="searchInput">
            </div>
            <button class="filter-btn">
                <i class="fas fa-filter"></i>
                Filter
            </button>
        </div>

        <div id="activitiesContainer">
            <div class="activity-card">
                <div class="activity-date">15 Nov 2025</div>
                <div class="activity-image">
                    <i class="fas fa-image"></i>
                </div>
                <div class="activity-title">Penelitian IoT untuk Smart Agriculture</div>
                <div class="activity-description">
                    Pengembangan sistem monitoring tanaman berbasis IoT dengan sensor kelembaban tanah dan sirkulasi udara.
                </div>
                <div class="activity-meta">
                    <div class="activity-stats">5 foto, 3 video</div>
                    <a href="#" class="detail-link">
                        Lihat Detail
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="activity-card">
                <div class="activity-date">10 Nov 2025</div>
                <div class="activity-image">
                    <i class="fas fa-image"></i>
                </div>
                <div class="activity-title">Workshop Machine Learning Dasar</div>
                <div class="activity-description">
                    Pelatihan pengenalan machine learning menggunakan Python dan library scikit-learn untuk mahasiswa semester 4.
                </div>
                <div class="activity-meta">
                    <div class="activity-stats">17 foto, 5 video</div>
                    <a href="#" class="detail-link">
                        Lihat Detail
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="activity-card">
                <div class="activity-date">8 Nov 2025</div>
                <div class="activity-image">
                    <i class="fas fa-image"></i>
                </div>
                <div class="activity-title">Maintenance Server dan Jaringan</div>
                <div class="activity-description">
                    Pemeliharaan rutin server laboratorium dan upgrade kapasitas jaringan untuk meningkatkan performa.
                </div>
                <div class="activity-meta">
                    <div class="activity-stats">7 foto, 4 video</div>
                    <a href="#" class="detail-link">
                        Lihat Detail
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.activity-card');
        
        cards.forEach(card => {
            const title = card.querySelector('.activity-title').textContent.toLowerCase();
            const description = card.querySelector('.activity-description').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
    <?php include 'PBL_Frontend/footer.php'; ?>

</body>