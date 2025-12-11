<?php
// Mencegah error jika file ini dipanggil dua kali
if (defined('NAVBAR_PHP_INCLUDED')) return;
define('NAVBAR_PHP_INCLUDED', true);

// --- PERBAIKAN DI SINI ---
// Gunakan SCRIPT_NAME. Ini akan selalu mendeteksi 'index.php' 
// bahkan jika URL browser hanya 'localhost/' atau 'localhost/folder/'
$current_page = basename($_SERVER['SCRIPT_NAME']); 

// Fungsi helper untuk mengecek menu aktif
if (!function_exists('is_active')) {
    function is_active($names) {
        global $current_page;
        
        // Ubah input menjadi array agar bisa menerima string tunggal atau array
        $names = (array) $names; 
        
        foreach ($names as $n) {
            // Bandingkan nama file yang dituju dengan file yang sedang dibuka
            if ($n === $current_page) {
                return ' active';
            }
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
                    <li class="nav-item"><a href="index.php" class="nav-link<?= is_active('index.php', '...') ?>">Beranda</a></li>
                    
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle<?= is_active(['profilLab.php', 'fasilitas.php']) ?>">Profil Lab</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profilLab.php">Tentang Kami</a></li>
                            <li><a class="dropdown-item" href="profilLab.php#visi-misi">Visi & Misi</a></li>
                            <li><a class="dropdown-item" href="fasilitas.php">Fasilitas Lab</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle<?= is_active(['anggota.php','dosen.php','Sorotan_Publikasi.php','produk.php', 'produk2.php', 'Profil_dosen.php']) ?>">Anggota & Riset</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="anggota.php">Anggota Lab</a></li>
                            <li><a class="dropdown-item" href="Sorotan_Publikasi.php">Fokus Riset</a></li>
                            <li><a class="dropdown-item" href="Sorotan_Publikasi.php">Sorotan Publikasi</a></li>
                            <li><a class="dropdown-item" href="produk.php">Produk dan Riset</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle<?= is_active(['Berita_Pengumuman.php','Aktivitas_Dokumen.php', 'Agenda.php', 'detail_berita.php', 'pengumuman.php', 'Detail_Aktivitas.php']) ?>">Berita & Aktivitas</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="Berita_Pengumuman.php">Berita dan Pengumuman</a></li>
                            <li><a class="dropdown-item" href="Aktivitas_Dokumen.php">Aktivitas dan Dokumentasi</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item"><a href="dashboard/daftarMember.php" class="nav-link join-btn<?= is_active('dashboard/daftarMember.php') ?>">Join Us!</a></li>
                </ul>
            </nav>
        </div>
    </header>
</div>