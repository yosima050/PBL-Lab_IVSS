<?php
// Footer Component
// Jangan taruh <html> atau <body> di sini, karena ini hanya potongan (include)
?>

<!-- Panggil CSS Footer -->
<link rel="stylesheet" href="footer.css">

<footer class="site-footer">
    <div class="container">
        
        <!-- BAGIAN ATAS (3 KOLOM) -->
        <div class="footer-content">
            
            <!-- KOLOM 1: Logo & Alamat -->
            <div class="footer-col-1">
                <div class="footer-logo-area">
                    <!-- Ganti dengan logo burung hantu Anda -->
                    <img src="images/logo-ivss.png" alt="Logo IVSS" class="footer-logo-img">
                    <div class="footer-brand-text">
                        <h3>LABORATORIUM <br><span>IVSS</span></h3>
                    </div>
                </div>
                
                <div class="footer-address">
                    <h4>BLU POLITEKNIK NEGERI MALANG</h4>
                    <p>Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang.<br>
                    Jawa Timur 65141</p>
                </div>
            </div>

            <!-- KOLOM 2: Website Lainnya -->
            <div class="footer-col-2">
                <h4 class="footer-col-title">Website Polinema Lainnya</h4>
                <ul class="footer-links">
                    <li><a href="https://www.polinema.ac.id" target="_blank">Polinema.ac.id</a></li>
                </ul>

                <h4 class="footer-col-title" style="margin-top: 20px;">Website Lainnya</h4>
                <ul class="footer-links">
                    <li><a href="https://sinta.kemdikbud.go.id" target="_blank">SINTA</a></li>
                </ul>
            </div>

            <!-- KOLOM 3: Nama Anggota -->
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

        <!-- BAGIAN BAWAH: Menu Navigasi Footer -->
        <div class="footer-bottom">
            <ul class="footer-bottom-nav">
                <li><a href="profil.php">Profil Lab</a></li>
                <li><a href="berita.php">Berita & Aktivitas</a></li>
                <li><a href="fasilitas.php">Fasilitas Lab</a></li>
                <li><a href="publikasi.php">Riset & Publikasi</a></li>
            </ul>
        </div>

    </div>
</footer>