<?php
// Mencegah error jika file ini dipanggil dua kali
if (defined('NAVBAR_PHP_INCLUDED')) return;
define('NAVBAR_PHP_INCLUDED', true);

// Mendapatkan nama file saat ini untuk logika menu aktif
$current = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Fungsi helper untuk mengecek menu aktif
if (!function_exists('is_active')) {
    function is_active($names) {
        global $current;
        $names = (array) $names; 
        foreach ($names as $n) {
            if (basename($n) === $current) return ' active';
        }
        return '';
    }
}
?>

<div class="polinema-navbar">
    <header class="header">
        <div class="header-container">
            
            <div class="logo">
                <a href="/index.php">
                    <img src="/images/logo.png" alt="Politeknik Negeri Malang" class="logo-img">
                </a>
            </div>
            
            <nav class="navigation">
                <ul class="nav-menu">
                    <li class="nav-item"><a href="../index.php" class="nav-link<?= is_active('../index.php') ?>">Beranda</a></li>
                    
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle<?= is_active(['profilLab.php','visi_misi.php','sejarah.php']) ?>">Profil Lab</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../PBL_Frontend/profilLab.php">Tentang Kami</a></li>
                            <li><a class="dropdown-item" href="../PBL_Frontend/profilLab.php#visi-misi">Visi & Misi</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle<?= is_active(['anggota.php','dosen.php','sorotanpublikasi.php','produkriset.php']) ?>">Anggota & Riset</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="anggota.php">Anggota Lab</a></li>
                            <li><a class="dropdown-item" href="dosen.php">Dosen Peneliti</a></li>
                            <li><a class="dropdown-item" href="riset.php">Fokus Riset</a></li>
                            <li><a class="dropdown-item" href="sorotanpublikasi.php">Sorotan Publikasi</a></li>
                            <li><a class="dropdown-item" href="produkriset.php">Produk dan Riset</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="berita.php" class="nav-link dropdown-toggle<?= is_active(['berita.php','berita_pengumuman.php']) ?>">Berita & Aktivitas</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="berita.php">Berita dan Pengumuman</a></li>
                            <li><a class="dropdown-item" href="aktivitasdokumentasi.php">Aktivitas dan Dokumentasi</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item"><a href="join.php" class="nav-link join-btn<?= is_active('join.php') ?>">Join Us!</a></li>
                </ul>
            </nav>
        </div>
    </header>
</div>