<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Beranda</title>

<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/styleube.css" rel='stylesheet' type='text/css' />
<link href="PBL_Frontend/navbar.css" rel='stylesheet' type='text/css' />

<script src="js/bootstrap.js"></script>

<meta name="keywords" content="Flooring Responsive web template, Bootstrap Web Templates, Flat Web Templates, Andriod Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />

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

</head>
<body>

<?php
// Tampilkan alert JS jika ada flash message, lalu hapus dari session
if (!empty($_SESSION['flash']) && !empty($_SESSION['flash']['message'])) {
    $alertMsg = $_SESSION['flash']['message'];
    // Hapus flash agar tidak tampil ulang
    unset($_SESSION['flash']);
    echo '<script>document.addEventListener("DOMContentLoaded", function(){ alert(' . json_encode($alertMsg) . '); });</script>';
}
?>

<?php include 'PBL_Frontend/navbar.php'; ?>
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
					<p>Intelligent Vision and Smart Systems</p>
					<a class="hvr-bounce-to-left btn-kontak" href="#">Kontak Kami</a>
					<i class="fa fa-envelope"></i>
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
				 <h2>Etiam ornare nisi eget quam pretium ipsum semper.</h2>
				 <p>Vestibulum augue nisi, mattis et mattis sed, commodo id turpis. Maecenas quis felis enim. Integer lacinia in ex quis laoreet.
				 Aliquam justo urna, ullamcorper non pellentesque sit amet, ultrices in lacus. Curabitur vitae nisl vel tellus rutrum ullamcorper.
				 Proin volutpat, magna eget posuere laoreet, est massa lobortis mi, a commodo dui nisi eget risus.</p>
				 <p>Maecenas eget magna volutpat, tincidunt urna id, imperdiet mi. Suspendisse dignissim eros sit amet nulla faucibus tristique quis ac libero.Vestibulum molestie maximus felis, rhoncus dignissim metus.</p>
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
				<div class="col-md-4 service-box" style="visibility: visible;">
					<figure class="icon">
						<img src="Asset/Rosa-Andrie-Asmara_2.jpg" alt="Alat 1">
					</figure>
					<h5>Proin eget ipsum ultrices</h5>
					<p>Sed ut perspiciis iste natus error sit voluptatem accusantium doloremque laudantium elerisque ipsum vehicula pharetra laoreet.</p>
				</div>
				<div class="col-md-4 service-box wow bounceIn animated" data-wow-delay="0.4s" style="visibility: visible;">
					<figure class="icon">
						<img src="Asset/IMG_20251105_123108.jpg" alt="Alat 2">
					</figure>
					<h5>Proin eget ipsum ultrices</h5>
					<p>Sed ut perspiciis iste natus error sit voluptatem accusantium doloremque laudantium elerisque ipsum vehicula pharetra laoreet.</p>
				</div>
				<div class="col-md-4 service-box wow bounceIn animated" data-wow-delay="0.4s" style="visibility: visible;">
					<figure class="icon">
						<img src="Asset/IMG_20251105_123108.jpg">
					</figure>
					<h5>Proin eget ipsum ultrices</h5>
					<p>Sed ut perspiciis iste natus error sit voluptatem accusantium doloremque laudantium elerisque ipsum vehicula pharetra laoreet.</p>
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
							auto: true,
							pager: false,
							nav: false,
							speed: 500,
							namespace: "callbacks",
							before: function () {
							  $('.events').append("<li>before event fired.</li>");
							},
							after: function () {
							  $('.events').append("<li>after event fired.</li>");
							}
						  });
					
						});
					</script>
					<div  id="top" class="callbacks_container">
						<ul class="rslides" id="slider2">
							<li>
								<div class="testimonial-grids">
									<div class="testimonial-left">
										<img src="Asset/Rosa-Andrie-Asmara_2.jpg" alt="" />
									</div>
									<div class="testimonial-right">
										<h5>Pak Rosa</h5>
										<p><span>"</span> Sebagai seorang profesional di bidang desain dengan pengalaman lebih dari 8 tahun, saya memiliki passion dalam menciptakan solusi visual yang tidak hanya estetis tetapi juga fungsional.
										Pendekatan saya menggabungkan pemikiran strategis dengan perhatian terhadap detail untuk menghasilkan karya yang sesuai dengan identitas merek dan kebutuhan pengguna.<span>"</span>
										</p>
									</div>
									<div class="clearfix"> </div>
								</div>
							</li>
							<li>
								<div class="testimonial-grids">
									<div class="testimonial-left">
										<img src="Asset/unnamed.jpg" alt="" />
									</div>
									<div class="testimonial-right">
										<h5>David Smith</h5>
										<p><span>"</span>Creative Director dengan spesialisasi dalam brand development dan digital design.
										Berpengalaman memimpin tim untuk menciptakan kampanye yang impactful dan konsisten across berbagai platform.<span>"</span>
										</p>
									</div>
									<div class="clearfix"> </div>
								</div>
							</li>
							<li>
								<div class="testimonial-grids">
									<div class="testimonial-left">
										<img src="Asset/Gemini_Generated_Image_8liqz88liqz88liq.png" alt="" />
									</div>
									 <div class="testimonial-right">
										<h5>Lora  Alance</h5>
										<p><span>"</span>Senior Graphic Designer yang berfokus pada pengembangan brand identity dan visual storytelling.
										Berpengalaman menciptakan desain yang tidak hanya menarik secara visual, tetapi juga efektif dalam menyampaikan pesan merek.<span>"</span>
										</p>
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
			 <div class="col-md-4 event-grid-sec">
				 <div class="event-time text-center">
					 <p>03/2015</p>
				 </div>
				 <div class="event-grid_pic">
					 <img src="Asset/pp.png" alt=""/>
					 <h3><a href="https://jti.polinema.ac.id/jurusan-teknologi-informasi-politeknik-negeri-malang-berhasil-meraih-juara-2-umum-pada-kompetensi-mahasiswa-informatika-politeknik-nasional-kmipn-2025-yang-berlangsung-pada-tanggal-13-16-oktober-2/" target="_blank">Jurusan Teknologi Informasi Politeknik Negeri Malang berhasil meraih juara 2 umum pada Kompetensi Mahasiswa Informatika Politeknik Nasional (KMIPN) 2025 yang berlangsung pada tanggal 13 – 16 Oktober 2025 di Politeknik Negeri Padang dengan perolehan 1 emas, 1 perak dan 1 perunggu</a></h3>
					 <p>Dengan penuh kebanggaan dan rasa syukur, Jurusan Teknologi Informasi Politeknik Negeri Malang (TI Polinema) kembali menorehkan prestasi gemilang di tingkat</p>
<<<<<<< HEAD
=======
					 <div class="more"><a href="https://jti.polinema.ac.id/jurusan-teknologi-informasi-politeknik-negeri-malang-berhasil-meraih-juara-2-umum-pada-kompetensi-mahasiswa-informatika-politeknik-nasional-kmipn-2025-yang-berlangsung-pada-tanggal-13-16-oktober-2/" target="_blank">> Read More</a></div>
>>>>>>> 90301ecd3d451330be25094abe264ab394e9b779
				 </div>
			 </div>
			 <div class="col-md-4 event-grid-sec">
				 <div class="event-time text-center">
					 <p>02/2015</p>
				 </div>
				 <div class="event-grid_pic">
					 <img src="Asset/peng.png" alt=""/>
					 <h3><a href="https://jti.polinema.ac.id/mahasiswa-jurusan-teknologi-informasi-politeknik-negeri-malang-raih-juara-3-hackathon-it-fest-2025-yang-diselenggarakan-oleh-himpunan-jurusan-teknologi-informasi-politeknik-negeri-samarinda/" target="_blank">Mahasiswa Jurusan Teknologi Informasi Politeknik Negeri Malang Raih Juara 3 Hackathon – IT Fest 2025 yang diselenggarakan oleh Himpunan Jurusan Teknologi Informasi Politeknik Negeri Samarinda</a></h3>
					 <p>Malang, 30 Oktober 2025 – Kabar membanggakan datang dari Jurusan Teknologi Informasi Politeknik Negeri Malang! Tim terbaik kita berhasil meraih Juara</p>
<<<<<<< HEAD
=======
					 <div class="more"><a href="https://jti.polinema.ac.id/mahasiswa-jurusan-teknologi-informasi-politeknik-negeri-malang-raih-juara-3-hackathon-it-fest-2025-yang-diselenggarakan-oleh-himpunan-jurusan-teknologi-informasi-politeknik-negeri-samarinda/" target="_blank">> Read More</a></div>
>>>>>>> 90301ecd3d451330be25094abe264ab394e9b779
				 </div>
			 </div>
			 <div class="col-md-4 event-grid-sec">
				 <div class="event-time text-center">
					 <p>04/2015</p>
				 </div>
				 <div class="event-grid_pic">
					 <img src="Asset/asd.png" alt=""/>
					 <h3><a href="https://jti.polinema.ac.id/mahasiswa-jurusan-teknologi-informasi-kembali-menorehkan-prestasi-membanggakan-dalam-ajang-creanomic-2025-yang-diselenggarakan-oleh-bem-fakultas-vokasi-universitas-brawijaya/" target="_blank">Mahasiswa Jurusan Teknologi Informasi kembali menorehkan prestasi membanggakan dalam ajang CREANOMIC 2025 yang diselenggarakan oleh BEM Fakultas Vokasi Universitas Brawijaya</a></h3>
					 <p>Selamat untuk Tim Delta Dev – Juara 1 CREANOMIC 2025! Kabar membanggakan kembali datang dari Jurusan Teknologi Informasi Politeknik Negeri</p>
<<<<<<< HEAD
=======
					 <div class="more"><a href="https://jti.polinema.ac.id/mahasiswa-jurusan-teknologi-informasi-kembali-menorehkan-prestasi-membanggakan-dalam-ajang-creanomic-2025-yang-diselenggarakan-oleh-bem-fakultas-vokasi-universitas-brawijaya/" target="_blank">> Read More</a></div>
>>>>>>> 90301ecd3d451330be25094abe264ab394e9b779
				 </div>
			 </div>
			 <div class="clearfix"></div>
		 </div>
	 </div>
</div>

<?php include __DIR__ . '/PBL_Frontend/footer.php'; ?>

<!-- tetapkan skrip UItoTop dan anchor (tidak dihapus) -->
<script type="text/javascript">
        $(document).ready(function() {
        $().UItoTop({ easingType: 'easeOutQuart' });
});
</script>
<a href="#to-top" id="toTop" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>

</body>
</html>