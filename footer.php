<?php
// Footer include-only (no DOCTYPE/html/body)

// Try to build a correct href for footer.css (multiple fallbacks)
$localCss = __DIR__ . '/footer.css';
$cssHref = null;

if (file_exists($localCss)) {
    $realLocal = realpath($localCss);
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');

    if ($docRoot && strpos($realLocal, $docRoot) === 0) {
        // path inside document root -> build web path
        $cssHref = str_replace('\\', '/', substr($realLocal, strlen($docRoot)));
        if ($cssHref === '' || $cssHref[0] !== '/') $cssHref = '/' . $cssHref;
    } else {
        // fallback: relative path from project root (adjust if your pages are in subfolders)
        $cssHref = '/PBL_Frontend/footer.css';
    }
}

// Output link tag if we have a candidate, otherwise emit small inline fallback styles
if ($cssHref) {
    echo '<link rel="stylesheet" href="' . htmlspecialchars($cssHref, ENT_QUOTES) . '">';
} else {
    echo "<style>
    /* minimal fallback footer styles */
    .site-footer{background:#001a3d;color:#fff;padding:30px 15px;font-family:Arial,Helvetica,sans-serif}
    .site-footer .container{max-width:1100px;margin:0 auto}
    .site-footer h4{margin:0 0 8px;color:#fff}
    .site-footer ul{padding:0;margin:0;list-style:disc inside}
    </style>";
}
?>

<footer class="site-footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-col-1">
        <div class="footer-logo-area">
          <img src="Asset/logofix.png" alt="Logo" class="footer-logo-img" style="width:72px">
          <div class="footer-brand-text">
            <h3>LABORATORIUM <br><span>IVSS</span></h3>
          </div>
        </div>
        <div class="footer-address">
          <h4>BLU POLITEKNIK NEGERI MALANG</h4>
          <p>Jl. Soekarno Hatta No.9, Malang, Jawa Timur 65141</p>
        </div>
      </div>

      <div class="footer-col-2">
        <h4 class="footer-col-title">Website Polinema</h4>
        <ul class="footer-links">
          <li><a href="https://www.polinema.ac.id" target="_blank">Polinema.ac.id</a></li>
        </ul>
        <h4 class="footer-col-title" style="margin-top:18px">Lainnya</h4>
        <ul class="footer-links">
          <li><a href="https://sinta.kemdikbud.go.id" target="_blank">SINTA</a></li>
        </ul>
      </div>

      <div class="footer-col-3">
        <h4 class="footer-col-title">Nama Anggota Kelompok</h4>
        <ul class="footer-links">
          <li>Yosep Bima Aprillian</li>
          <li>Aurellia Mezaluna Azwa</li>
          <li>Ubaidillah Ulil Absor Abdala</li>
          <li>Revalina Kristanti Putri</li>
          <li>Aamira Faheema Ghania</li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <ul class="footer-bottom-nav">
        <li><a href="profilLab.php">Profil Lab</a></li>
        <li><a href="Berita_Pengumuman.php">Berita & Aktivitas</a></li>
        <li><a href="fasilitas.php">Fasilitas Lab</a></li>
        <li><a href="produk.php">Riset & Publikasi</a></li>
      </ul>
      <div style="margin-top:12px;color:#bcd3ff">&copy; <?= date('Y') ?> LAB IVSS - Politeknik Negeri Malang</div>
    </div>
  </div>
</footer>