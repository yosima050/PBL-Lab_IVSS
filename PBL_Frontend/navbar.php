<?php
if (defined('NAVBAR_PHP_INCLUDED')) return;
define('NAVBAR_PHP_INCLUDED', true);

$current = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

function is_active($names) {
    global $current;
    $names = (array) $names;
    foreach ($names as $n) {
        if (basename($n) === $current) return ' active';
    }
    return '';
}
?>
<div class="polinema-navbar">
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="../index.php">
                    <img src="/images/logo.png" alt="Politeknik Negeri Malang" class="logo-img">
                </a>
            </div>
            <nav class="navigation">
                <ul class="nav-menu">
                <li><a href="../index.php" class="nav-link<?= is_active('../index.php') ?>">Beranda</a></li>
                <li><a href="profilLab.php" class="nav-link<?= is_active(['profilLab.php','profil.php']) ?>">Profil Lab</a></li>
                <li><a href="anggota.php" class="nav-link<?= is_active('anggota.php') ?>">Anggota & Riset</a></li>
                <li><a href="berita.php" class="nav-link<?= is_active(['berita.php','berita_pengumuman.php']) ?>">Berita & Aktivitas</a></li>
                <li><a href="join.php" class="nav-link join-btn<?= is_active('join.php') ?>">Join Us!</a></li>
                </ul>
            </nav>
        </div>
    </header>
</div>