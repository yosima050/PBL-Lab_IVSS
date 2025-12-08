<?php
session_start();

include 'dashboard/db.php'; 


// 2. QUERY MENGAMBIL 3 BERITA TERBARU
try {
    // Ambil id, judul, isi, foto, dan tanggal
    // Urutkan berdasarkan tanggal terbaru, ambil 3 saja
    $stmt = $pdo->prepare("SELECT id_berita, judul_berita, isi_berita, foto_berita, created_at_berita 
                           FROM public.berita 
                           ORDER BY created_at_berita DESC 
                           LIMIT 3");
    $stmt->execute();
    $berita_terbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Jika error, set array kosong agar website tidak crash
    $berita_terbaru = []; 
    // echo "Error: " . $e->getMessage(); // Uncomment untuk debug
}
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Beranda - Lab IVSS</title>

<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/styleube.css" rel='stylesheet' type='text/css' />
<link href="navbar.css" rel='stylesheet' type='text/css' />
<link href="footer.css" rel='stylesheet' type='text/css' />

<!-- Material Symbols (ikon) -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<style>
/* Override kuat agar tombol ToTop hanya tampil sebagai ikon */
a#toTop, #toTop {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 44px !important;
  height: 44px !important;
  font-size: 0 !important;         /* sembunyikan teks jika ada */
  line-height: 0 !important;
  text-indent: -9999px !important; /* ekstra aman */
  white-space: nowrap !important;
  overflow: visible !important;
  position: fixed !important;
  right: 20px !important;
  bottom: 24px !important;
  background: #0047AB !important;
  color: transparent !important;
  border-radius: 50% !important;
  box-shadow: 0 6px 18px rgba(0,0,0,0.18) !important;
  z-index: 9999 !important;
  cursor: pointer !important;
  text-decoration: none !important;
}

/* Hilangkan pseudo-element yang mungkin menambahkan teks vertikal */
a#toTop::before, a#toTop::after, #toTop::before, #toTop::after {
  content: none !important;
  display: none !important;
}

/* Pastikan SVG tampil */
a#toTop svg, #toTop svg {
  width: 18px !important;
  height: 18px !important;
  fill: #fff !important;
  display: block !important;
}

/* Material Symbols icon visible (override the font-size:0 on the container) */
a#toTop .material-symbols-outlined,
#toTop .material-symbols-outlined {
  font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  font-size: 18px !important;
  line-height: 1 !important;
  color: #fff !important;
  display: inline-block !important;
  text-indent: 0 !important;
}

/* Penyesuaian mobile */
@media (max-width: 576px) {
  a#toTop, #toTop { right: 12px !important; bottom: 18px !important; width: 40px !important; height: 40px !important; }
}
</style>

<script src="js/bootstrap.js"></script>

<meta name="keywords" content="Lab IVSS, Politeknik Negeri Malang, Intelligent Vision, Smart Systems" />

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/responsiveslides.min.js"></script>
<script type="text/javascript" src="js/move-top.js"></script>
<script type="text/javascript" src="js/easing.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $(".scroll").click(function(event) {
            event.preventDefault();
            $('html,body').animate({scrollTop:$(this.hash).offset().top},900);
        });
    });
</script>

<script type="text/javascript">
    // HAPUS / COMMENT OUT pemanggilan UItoTop jika ada:
    // $(document).ready(function() { $().UItoTop({ easingType: 'easeOutQuart' }); });

    // Ganti dengan script kustom: tampilkan tombol saat scroll dan scroll-to-top saat diklik
    (function($){
        $(function(){
            var $toTop = $('#toTop');
            // awalnya sembunyikan agar plugin tidak mengganggu (jika ada styling lain)
            $toTop.hide();

            $(window).on('scroll.toTop', function(){
                if ($(this).scrollTop() > 200) {
                    $toTop.fadeIn(220);
                } else {
                    $toTop.fadeOut(200);
                }
            });

            $toTop.on('click', function(e){
                e.preventDefault();
                $('html, body').animate({ scrollTop: 0 }, 600);
            });
        });
    })(jQuery);
</script>

<style>
    /* Tambahan CSS agar gambar berita rapi */
    .event-grid_pic img {
        width: 100%;
        height: 200px; /* Tinggi tetap agar sejajar */
        object-fit: cover;
        border-radius: 5px;
    }
    .event-time p {
        font-size: 14px;
        font-weight: bold;
    }
    .event-grid-sec h3 a {
        text-decoration: none;
        color: #333;
        font-size: 16px;
        font-weight: bold;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Batasi judul 2 baris */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .event-grid-sec h3 a:hover {
        color: #0047AB;
    }
</style>

</head>
<body>

<?php
// Tampilkan alert JS jika ada flash message
if (!empty($_SESSION['flash']) && !empty($_SESSION['flash']['message'])) {
    $alertMsg = $_SESSION['flash']['message'];
    unset($_SESSION['flash']);
    echo '<script>document.addEventListener("DOMContentLoaded", function(){ alert(' . json_encode($alertMsg) . '); });</script>';
}
?>

<?php include 'navbar.php'; ?>

<script src="js/responsiveslides.min.js"></script>
<script>
    $(function () {
      $("#slider").responsiveSlides({
        auto: false,
        nav: false,
        speed: 500,
        namespace: "callbacks",
        pager: true,
      });
    });
</script>

<div class="header-slider">
    <div class="slider">
        <div class="callbacks_container">
            <ul class="rslides" id="slider">
                <div class="slid banner1">                
                    <div class="caption">
                        <h3>Intelligent Vision and Smart Systems</h3>
                        <p>Sistem Visi Cerdas dan Sistem Pintar</p>
                        <a class="hvr-bounce-to-left btn-kontak" href="profilLab.php">Kenali Kami Lebih Lanjut</a>
                    </div>
                </div>
            </ul>
        </div>
    </div>
</div>

<div class="content">
     <div class="container">
         <div class="content-grids">
             <div class="col-md-6 content-left">
                 <img src='Asset/Lab.jpg' class="img-responsive" alt=""/>
             </div>
             <div class="col-md-6 content-right">
                 <h2>Selamat Datang di Laboratorium Visi Cerdas dan Sistem Cerdas</h2>
                 <p>Sebuah ruang kolaborasi di mana kami mengeksplorasi masa depan kecerdasan buatan. Kami percaya pada kekuatan mesin yang tidak hanya dapat melihat lingkungannya dengan cerdas, tetapi juga memahami, mempelajari, dan mengambil keputusan autonom untuk menyelesaikan masalah yang kompleks.</p>
             </div>
             <div class="clearfix"></div>
         </div>
     </div>
</div>      

<div id="services" class="services">
     <div class="container">
            <div class="service-info">
                <h3>Peralatan Lab</h3>
            </div>
            <div class="specialty-grids-top">
                <div class="col-md-4 service-box">
                    <figure class="icon">
                        <img src="Asset/perlab2.jpg" alt="Alat 1">
                    </figure>
                    <h5>Proyektor LCD Epson</h5>
                    <p>Fasilitas proyektor berkualitas tinggi untuk mendukung kegiatan presentasi dan pembelajaran visual di laboratorium.</p>
                </div>
                <div class="col-md-4 service-box wow bounceIn animated" data-wow-delay="0.4s">
                    <figure class="icon">
                        <img src="Asset/Sony_Alpha.jpg" alt="Alat 2">
                    </figure>
                    <h5>Sony Alpha a6400</h5>
                    <p>Kamera mirrorless canggih untuk pengambilan data citra berkualitas tinggi dalam penelitian computer vision.</p>
                </div>
                <div class="col-md-4 service-box wow bounceIn animated" data-wow-delay="0.4s">
                    <figure class="icon">
                        <img src="Asset/perlab1.jpg">
                    </figure>
                    <h5>Kamera Lentern Softbox</h5>
                    <p>Peralatan pencahayaan profesional untuk memastikan kondisi cahaya optimal saat pengambilan dataset visual.</p>
                </div>
                <div class="clearfix"> </div>
            </div>
     </div>    
</div>

<div class="testimonial">
    <div class="container">
        <script>
            $(function () {
              $("#slider2").responsiveSlides({
                auto: true,     // Automatically slide
                pager: false,   // Disable pagination dots
                nav: false,     // Disable prev/next arrows
                speed: 500,     // Transition speed
                namespace: "callbacks",
              });
            });
        </script>

        <div id="top" class="callbacks_container">
            <ul class="rslides" id="slider2">
                
                <li>
                    <div class="testimonial-grids">
                        <div class="testimonial-left">
                            <img src="Asset/Rosa-Andrie-Asmara_2.jpg" alt="Pak Rosa" />
                        </div>
                        <div class="testimonial-right">
                            <h5>Prof. Dr. Eng. Rosa Andrie Asmara, ST., MT.</h5>
                            <p><span>"</span>Sebagai peneliti utama, saya melihat potensi besar mahasiswa Polinema dalam mengembangkan solusi AI yang berdampak nyata bagi industri.<span>"</span></p>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </li>

                <li>
                    <div class="testimonial-grids">
                        <div class="testimonial-left">
                            <img src="Asset/buvivifix.png" alt="Bu Vivi" onerror="this.src='Asset/default_profile.jpg'" />
                        </div>
                        <div class="testimonial-right">
                            <h5>Vivi Nur Wijayaningrum, S.Kom, M.Kom</h5>
                            <p><span>"</span>Laboratorium ini adalah tempat terbaik untuk mengasah kemampuan Artificial Intelligence. Fasilitas yang memadai sangat mendukung riset kami.<span>"</span></p>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </li>

                <li>
                    <div class="testimonial-grids">
                        <div class="testimonial-left">
                            <img src="Asset/buA1.png" alt="Bu Mamluatul" onerror="this.src='Asset/default_profile.jpg'" />
                        </div>
                        <div class="testimonial-right">
                            <h5>Mamluatul Hani'ah, S.Kom., M.Kom</h5>
                            <p><span>"</span>Kolaborasi antara dosen dan mahasiswa di sini sangat kuat. Kami fokus menciptakan inovasi teknologi yang tidak hanya canggih, tapi juga bermanfaat.<span>"</span></p>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>

<div class="projects">
     <div class="container">
            <div class="projects-info">
                <h3>Berita & Pengumuman</h3>
            </div>
         
         <div class="event-grids">
             
             <?php if (count($berita_terbaru) > 0): ?>
                 <?php foreach ($berita_terbaru as $row): ?>
                     <div class="col-md-4 event-grid-sec">
                         
                         <div class="event-time text-center">
                             <p><?= date('m/Y', strtotime($row['created_at_berita'])); ?></p>
                         </div>
                         
                         <div class="event-grid_pic">
                             <img src="uploads/<?= htmlspecialchars($row['foto_berita']); ?>" 
                                  alt="<?= htmlspecialchars($row['judul_berita']); ?>" 
                                  onerror="this.src='Asset/default_news.png';">
                             
                             <h3>
                                 <a href="detail_berita.php?id=<?= $row['id_berita']; ?>">
                                     <?= htmlspecialchars($row['judul_berita']); ?>
                                 </a>
                             </h3>
                             
                             <p>
                                 <?= htmlspecialchars(substr($row['isi_berita'], 0, 120)) . '...'; ?>
                             </p>
                         </div>
                     </div>
                 <?php endforeach; ?>
             <?php else: ?>
                 <div class="col-md-12 text-center">
                     <p>Belum ada berita terbaru.</p>
                 </div>
             <?php endif; ?>

             <div class="clearfix"></div>
         </div>
     </div>
</div>

<?php include 'footer.php'; ?>

<!-- Ganti markup tombol to-top agar menampilkan ikon Material Symbol -->
<a href="#top" id="toTop" title="Kembali ke atas" aria-label="Kembali ke atas">
    <span class="material-symbols-outlined" aria-hidden="true">chevron_line_up</span>
</a>

</body>
</html>