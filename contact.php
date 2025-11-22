<!DOCTYPE HTML>
<html>
<head>
    <title>IVSS | Kontak Kami</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
    <link href="css/styleube.css" rel='stylesheet' type='text/css' />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/move-top.js"></script>
    <script type="text/javascript" src="js/easing.js"></script>
</head>
<body>

<?php include 'header.php'; ?>

<div class="top-banner"></div>	 

<div class="contact-section-custom">
    <div class="container">
        <div class="contact-header text-center">
            <h3>Hubungi Kami</h3>
            <p>Punya pertanyaan atau ingin berkolaborasi? Silakan kirim pesan kepada kami.</p>
        </div>
        
        <div class="row contact-content">
             <div class="col-md-7 contact-form-area">
                <form>
                    <div class="form-input">
                        <input type="text" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-input">
                        <input type="text" placeholder="Email" required>
                    </div>
                    <div class="form-input">
                        <input type="text" placeholder="No. Telepon">
                    </div>
                    <div class="form-input">
                        <textarea placeholder="Tulis pesan Anda di sini..." required></textarea>
                    </div>
                    <div class="form-submit">
                        <input type="submit" value="KIRIM PESAN">
                    </div>
                </form>
             </div>
             
             <div class="col-md-5 contact-info-area">
                <div class="info-item">
                    <div class="icon">
                        <i class="fas fa-map-marker-alt"></i> </div>
                    <div class="details">
                        <h5>Alamat</h5>
                        <p>Gedung A10 Lt 1, Jurusan Teknologi Informasi,<br>
                        Politeknik Negeri Malang.<br>
                        Jl. Soekarno Hatta No.9, Jatimulyo,<br>
                        Kec. Lowokwaru, Kota Malang 65141</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="icon">
                        <i class="fas fa-phone-alt"></i> </div>
                    <div class="details">
                        <h5>Telepon</h5>
                        <p>(0341) 404424, 404425</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="icon">
                        <i class="fas fa-envelope"></i> </div>
                    <div class="details">
                        <h5>Email</h5>
                        <p><a href="mailto:cs@polinema.ac.id">cs@polinema.ac.id</a></p>
                    </div>
                </div>
             </div>
        </div>
    </div>
    
    <div class="contact-map-full">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.448783966924!2d112.61248431432828!3d-7.952473681436263!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7882792895a5f1%3A0x504672c5b9e31e5!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sid!2sid!4v1625632483245!5m2!1sid!2sid" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>		 

<div class="footer">
	 <div class="container">
		 <div class="footer-grids">
			 <div class="col-md-6 ftr-grid1">
				 <h4>Tentang Kami</h4>
				 <p>Sistem Inventaris dan Peminjaman Barang Jurusan Teknologi Informasi Politeknik Negeri Malang.</p>
			 </div>
			 <div class="col-md-6 news-ltr">
				 <h4>Hubungi Kami</h4>
				 <p>Gedung A10 Lt 1, JTI Polinema.<br>Jl. Soekarno Hatta No.9, Malang.</p>
			</div>
			 <div class="clearfix"></div>
		 </div>		 
	 </div>
</div>
<div class="copywrite">
	 <div class="container">
			 <p> © 2025 IVSS. All rights reserved</p>
	 </div>
</div> 

<script type="text/javascript">
    $(document).ready(function() {
        $().UItoTop({ easingType: 'easeOutQuart' });
    });
</script>
<a href="#to-top" id="toTop" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>

</body>
</html>