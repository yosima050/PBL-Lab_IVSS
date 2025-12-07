<?php
include 'dashboard/db.php'; 

$id_dosen = isset($_GET['id']) ? intval($_GET['id']) : 9;

try {
    $stmt = $pdo->prepare("SELECT * FROM public.dosen WHERE id_dosen = :id");
    $stmt->execute(['id' => $id_dosen]);
    $row = $stmt->fetch();

    if (!$row) {
        die("Data dosen dengan ID $id_dosen tidak ditemukan di database.");
    }

    $pendidikan_list = [];
    if (!empty($row['pendidikan_dosen']) && $row['pendidikan_dosen'] != '-') {
        $pendidikan_list = explode(',', $row['pendidikan_dosen']);
    }

    $sertifikasi_list = [];
    if (!empty($row['sertifikasi_dosen']) && $row['sertifikasi_dosen'] != '-') {
        $sertifikasi_list = explode(',', $row['sertifikasi_dosen']);
    }

    $mk_raw = $row['mata_kuliah_dosen'];
    $mk_genap = [];
    $mk_ganjil = [];

    if (!empty($mk_raw) && $mk_raw != '-') {
        $semesters = explode(';', $mk_raw);
        
        foreach ($semesters as $sem) {
            $sem = trim($sem);
            if (stripos($sem, 'Semester Genap') !== false) {
                $clean = str_ireplace('Semester Genap', '', $sem);
                $clean = trim($clean, " ,;"); 
                $mk_genap = explode(',', $clean);
            } elseif (stripos($sem, 'Semester Ganjil') !== false) {
                $clean = str_ireplace('Semester Ganjil', '', $sem);
                $clean = trim($clean, " ,;");
                $mk_ganjil = explode(',', $clean);
            }
        }
    }

} catch (PDOException $e) {
    die("Error Query: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Dosen - <?= htmlspecialchars($row['nama_dosen']); ?></title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="dashboard/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styleube.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">

    <style>
        .banner1{
            background:url(../Asset/Coba.jpg) no-repeat 0px 0px;
            background-size:cover;
            min-height:250px;
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
            font-family: "Roboto", sans-serif;
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
            word-wrap: break-word;
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

    <?php include 'navbar.php'; ?>
    
        <div class="banner1"> </div>

    <div class="container">
        <div class="profile-container">
            <div class="row">
                
                <div class="col-md-4 border-right-custom">
                    <div class="profile-img-box">
                        <?php 
                            $foto = !empty($row['foto_dosen']) ? 'uploads/' . htmlspecialchars($row['foto_dosen']) : 'Asset/default_profile.jpg';
                        ?>
                        <img src="<?= $foto ?>" class="img-fluid rounded"
                             alt="Foto <?= htmlspecialchars($row['nama_dosen']) ?>"
                             onerror="this.src='Asset/default_profile.jpg'">
                    </div>

                    <div class="mb-3">
                        <div class="sidebar-label">NIP</div>
                        <div class="sidebar-value"><?= htmlspecialchars($row['nip']); ?></div>
                        
                        <div class="sidebar-label">NIDN</div>
                        <div class="sidebar-value"><?= htmlspecialchars($row['nidn_dosen']); ?></div>

                        <div class="sidebar-label">Program Studi</div>
                        <div class="sidebar-value"><?= htmlspecialchars($row['prodi_dosen']); ?></div>

                        <div class="sidebar-label">Jabatan</div>
                        <div class="sidebar-value"><?= htmlspecialchars($row['jabatan_dosen']); ?></div>
                    </div>

                    <hr class="divider">

                    <div class="mb-3">
                        <div class="sidebar-label mb-2">Kontak</div>
                        <ul class="contact-list">
                            <li>
                                <strong>EMAIL:</strong><br>
                                <?= htmlspecialchars($row['email_dosen']); ?>
                            </li>
                            <li>
                                <strong>Alamat Kantor:</strong><br>
                                <?= htmlspecialchars($row['alamat_kantor']); ?>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-8 pl-md-4">
                    
                    <h1 class="profile-name"><?= htmlspecialchars($row['nama_dosen']); ?></h1>
                    
                    <?php 
                        $riset_items = explode(',', $row['bidang_riset']);
                        foreach($riset_items as $riset): 
                            if(trim($riset) != '-'):
                    ?>
                        <div class="tag-badge"><?= htmlspecialchars(trim($riset)); ?></div>
                    <?php endif; endforeach; ?>

                    <div class="social-btn-group mb-4">
                        <?php if(!empty($row['link_linkedin']) && $row['link_linkedin'] != '-'): ?>
                            <a href="<?= htmlspecialchars($row['link_linkedin']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">LinkedIn</a>
                        <?php endif; ?>
                        
                        <?php if(!empty($row['link_google_scholar']) && $row['link_google_scholar'] != '-'): ?>
                            <a href="<?= htmlspecialchars($row['link_google_scholar']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">Google Scholar</a>
                        <?php endif; ?>
                        
                        <?php if(!empty($row['link_sinta']) && $row['link_sinta'] != '-'): ?>
                            <a href="<?= htmlspecialchars($row['link_sinta']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">Sinta</a>
                        <?php endif; ?>

                        <a href="mailto:<?= htmlspecialchars($row['email_dosen']); ?>" class="btn btn-outline-primary btn-sm">Email</a>
                    </div>

                    <div class="section-title">Pendidikan, Sertifikasi & Mata Kuliah</div>

                    <div class="info-card">
                        <h5>Pendidikan</h5>
                        <ul>
                            <?php if (!empty($pendidikan_list)): ?>
                                <?php foreach ($pendidikan_list as $pendidikan): ?>
                                    <li>
                                        <span><?= htmlspecialchars(trim($pendidikan)); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span>Data pendidikan belum tersedia.</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h5>Sertifikasi</h5>
                        <ul>
                            <?php if (!empty($sertifikasi_list)): ?>
                                <?php foreach ($sertifikasi_list as $sertifikat): ?>
                                    <li>
                                        <span><?= htmlspecialchars(trim($sertifikat)); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span>Belum ada data</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h5>Mata Kuliah</h5>
                        
                        <?php if (!empty($mk_genap) && count($mk_genap) > 0 && $mk_genap[0] != ""): ?>
                            <div class="mb-2" style="color:#0047AB; font-weight:600;">Semester Genap</div>
                            <ul>
                                <?php foreach ($mk_genap as $mk): ?>
                                    <?php if(trim($mk) != ''):  ?>
                                        <li><span><?= htmlspecialchars(trim($mk)); ?></span></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($mk_ganjil) && count($mk_ganjil) > 0 && $mk_ganjil[0] != ""): ?>
                            <div class="mt-3 mb-2" style="color:#0047AB; font-weight:600;">Semester Ganjil</div>
                            <ul>
                                <?php foreach ($mk_ganjil as $mk): ?>
                                    <?php if(trim($mk) != ''):  ?>
                                        <li><span><?= htmlspecialchars(trim($mk)); ?></span></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (empty($mk_genap) && empty($mk_ganjil)): ?>
                            <ul><li><span>Data mata kuliah belum tersedia.</span></li></ul>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    
    <?php include 'footer.php'; ?>
</body>
</html>