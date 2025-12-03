<?php
include 'dashboard/db.php'; 


// 2. AMBIL DATA DARI MATERIALIZED VIEW
// Dashboard Anda mengupdate tabel 'profil_lab' lalu me-refresh 'mv_profil_lab'.
// Jadi front-end cukup ambil dari 'mv_profil_lab'.
try {
    $stmt = $pdo->query("SELECT * FROM mv_profil_lab LIMIT 1");
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data kosong, gunakan default agar tidak error
    if (!$profil) {
        $profil = [
            'visi' => 'Belum ada data visi.',
            'misi' => 'Belum ada data misi.',
            'narasi' => 'Belum ada data narasi.',
            'foto_profil_lab' => '' 
        ];
    }
} catch (PDOException $e) {
    die("Gagal mengambil data profil: " . $e->getMessage());
}

// 3. PARSING DATA MISI (Memecah teks per baris menjadi list item)
$misi_list = array_filter(explode("\n", $profil['misi'])); // Pecah berdasarkan Enter
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Laboratorium - Lab IVSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    
    <style>
        .banner1{
            background:url(Asset/Coba.jpg) no-repeat 0px 0px;
            background-size:cover;
            min-height:250px;
        }
        body {
            font-family: "Roboto", sans-serif;
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
            margin: 40px 50px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .lab-image-style {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .description-section p {
            text-align: justify;
            font-size: 14px;
            color: #333;
            white-space: pre-line; /* Agar spasi/enter di DB terbaca */
        }
        .custom-vision-mission-title {
            background-color: #f9d723;
            padding: 8px 20px;
            text-align: left;
            margin-top: 20px;
            margin-left: 50px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: inline-block;
            border-radius: 25px;
            font-weight: bold;
            color: #0047AB;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .custom-section-container {
            margin: 0 50px 30px 50px;
        }
        .custom-section-content {
            padding: 25px;
            margin : 1px;
            background-color: #F5F9FF;
            border-radius: 15px 15px 15px 15px;
        }
        .custom-section-content ul { list-style-position: outside; margin-left: 20px; }
        
        #visi-misi { scroll-margin-top: 100px; }

        @media (max-width: 768px) {
            .custom-main-content { margin: 15px; }
            .custom-header, .custom-vision-mission-title {
                margin-left: auto !important;
                margin-right: auto !important;
            }
            .custom-vision-mission-title { margin-left: 15px; font-size: 16px; margin-bottom: 8px; }
            .custom-section-container { margin: 8px 15px 30px 15px; }
        }
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>
    
    <div class="banner1"> </div>
    
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
                        <?php 
                            $fotoPath = !empty($profil['foto_profil_lab']) ? 'uploads/'.$profil['foto_profil_lab'] : 'images/labivss.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($fotoPath); ?>" 
                             alt="Laboratorium IVSS" 
                             class="img-fluid lab-image-style mx-auto d-block" 
                             style="max-width:300px;"
                             onerror="this.src='Asset/noimage.png'">
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="description-section d-flex flex-column gap-3 h-100">
                        <p>
                            <?= nl2br(htmlspecialchars($profil['narasi'])); ?>
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
                <p><?= nl2br(htmlspecialchars($profil['visi'])); ?></p>
            </div>
        </div>

        <div class="custom-vision-mission-title">Misi</div>
        <div class="custom-section-container" style="margin-bottom:20px;">
            <div class="custom-section-content">
                <ul style="list-style-type: none; padding-left: 0;">
                    <?php if (!empty($misi_list)): ?>
                        <?php foreach($misi_list as $misi_item): ?>
                            <?php if(trim($misi_item) != ''): ?>
                                <li><?= htmlspecialchars(trim($misi_item)); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Belum ada data misi.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>