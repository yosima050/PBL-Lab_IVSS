<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Flooring a Interior category Flat Bootstarp Responsive Website Template | Home :: w3layouts</title>

<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/styleube.css" rel='stylesheet' type='text/css' />

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

<?php include 'header.php'; ?>
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

					<a class="hvr-bounce-to-left btn-right" href="#">Kontak Kami</a>
					</div>
				</div>
				<div class="slid banner2">				  
				  <div class="caption">
					<h3>Intelligent Vision and Smart Systems</h3>
					<p>Intelligent Vision and Smart Systems</p>
					
					<a class="hvr-bounce-to-left btn-right" href="#">Kontak Kami</a>
					</div>
				</div>
				<div class="slid banner3">				  
				  <div class="caption">
					<h3>Intelligent Vision and Smart Systems</h3>
					<p>Intelligent Vision and Smart Systems</p>
					
					<a class="hvr-bounce-to-left btn-right" href="#">Kontak Kami</a>
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
				<div class="col-md-4 service-box" style="visibility: visible; -webkit-animation-delay: 0.4s;">
					<figure class="icon">
						<span class="glyphicon3 glyphicon-home" aria-hidden="true"></span>
					</figure>
					<h5>Proin eget ipsum ultrices</h5>
					<p>Sed ut perspiciis iste natus error sit voluptatem accusantium doloremque laudantium elerisque ipsum vehicula pharetra laoreet.</p>
				</div>
				<div class="col-md-4 service-box wow bounceIn animated" data-wow-delay="0.4s" style="visibility: visible; -webkit-animation-delay: 0.4s;">
					<figure class="icon">
						<span class="glyphicon3 glyphicon-time" aria-hidden="true"></span>
					</figure>
					<h5>Proin eget ipsum ultrices</h5>
					<p>Sed ut perspiciis iste natus error sit voluptatem accusantium doloremque laudantium elerisque ipsum vehicula pharetra laoreet.</p>
				</div>
				<div class="col-md-4 service-box wow bounceIn animated" data-wow-delay="0.4s" style="visibility: visible; -webkit-animation-delay: 0.4s;">
					<figure class="icon">
						<span class="glyphicon3 glyphicon-edit" aria-hidden="true"></span>
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
							pager: true,
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
										<img src="images/t1.jpg" alt="" />
									</div>
									<div class="testimonial-right">
										<h5>Mary Wilson</h5>
										<p><span>"</span> Lorem ipsum dolor sit amet consec tetuer adi piscing elit Praesent vestibulum 
											molestie lacus consec tetuer piscing voluptatem accusantium doloremque laudantium elerisque ipsum vehicula pharetra elit Praesent vestibulum lacus<span>"</span>
										</p>
									</div>
									<div class="clearfix"> </div>
								</div>
							</li>
							<li>
								<div class="testimonial-grids">
									<div class="testimonial-left">
										<img src="images/t3.jpg" alt="" />
									</div>
									<div class="testimonial-right">
										<h5>David Smith</h5>
										<p><span>"</span> Lorem ipsum dolor sit amet consec tetuer adi piscing elit Praesent vestibulum 
											molestie lacus consec tetuer piscing voluptatem accusantium doloremque laudantium elerisque ipsum vehicula pharetra elit Praesent vestibulum lacus<span>"</span>
										</p>
									</div>
									<div class="clearfix"> </div>
								</div>
							</li>
							<li>
								<div class="testimonial-grids">
									<div class="testimonial-left">
										<img src="images/t2.jpg" alt="" />
									</div>
									<div class="testimonial-right">
										<h5>Lora  Alance</h5>
										<p><span>"</span> Lorem ipsum dolor sit amet consec tetuer adi piscing elit Praesent vestibulum 
											molestie lacus consec tetuer piscing voluptatem accusantium doloremque laudantium elerisque ipsum vehicula pharetra elit Praesent vestibulum lacus<span>"</span>
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
				<h3>Our Projects</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vel risus non mauris volutpat pellentesque. Sed rhoncus, arcu nec euismod ultrices tellus nulla varius tellus ac blandit nunc ex vel felis pellentesque imperdiet imperdiet nibh</p>
			</div>
		 <div class="event-grids">
			 <div class="col-md-4 event-grid-sec">
				 <div class="event-time text-center">
					 <p>03/2015</p>
				 </div>
				 <div class="event-grid_pic">
					 <img src="images/pc.jpg" alt=""/>
					 <h3><a href="#">Morbi pellentesque urna scelerisque justo rutrum.</a></h3>
					 <p>Nullam placerat aliquet nisl id finibus. Nulla mollis mattis magna in hendrerit. Pellentesque nunc nisl, dapibus eget erat non,
					 sagittis accumsan dolor.</p>
					 <div class="more"><a href="single.php">> Read More</a></div>
				 </div>
			 </div>
			 <div class="col-md-4 event-grid-sec">
				 <div class="event-time text-center">
					 <p>02/2015</p>
				 </div>
				 <div class="event-grid_pic">
					 <img src="images/pc1.jpg" alt=""/>
					 <h3><a href="#">Morbi pellentesque urna scelerisque justo rutrum.</a></h3>
					 <p>Nullam placerat aliquet nisl id finibus. Nulla mollis mattis magna in hendrerit. Pellentesque nunc nisl, dapibus eget erat non,
					 sagittis accumsan dolor.</p>
					 <div class="more"><a href="single.php">> Read More</a></div>
				 </div>
			 </div>
			 <div class="col-md-4 event-grid-sec">
				 <div class="event-time text-center">
					 <p>04/2015</p>
				 </div>
				 <div class="event-grid_pic">
					 <img src="images/pc2.jpg" alt=""/>
					 <h3><a href="#">Morbi pellentesque urna scelerisque justo rutrum.</a></h3>
					 <p>Nullam placerat aliquet nisl id finibus. Nulla mollis mattis magna in hendrerit. Pellentesque nunc nisl, dapibus eget erat non,
					 sagittis accumsan dolor.</p>
					 <div class="more"><a href="single.php">> Read More</a></div>
				 </div>
			 </div>
			 <div class="clearfix"></div>
		 </div>
	 </div>
</div>

	<div class="team">
			<div class="team-top heading">
				<h3>Our Team</h3>
			</div>
			<div class="team-bottom">
				<ul class="ch-grid">
					<li>
						<div class="ch-item ch-img-1">				
							<div class="ch-info-wrap">
								<div class="ch-info">
									<div class="ch-info-front ch-img-1"></div>
									<div class="ch-info-back">
										<h3>Bears Type</h3>
										<p>by Josh Schott</p>
									</div>	
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="ch-item ch-img-2">
							<div class="ch-info-wrap">
								<div class="ch-info">
									<div class="ch-info-front ch-img-2"></div>
									<div class="ch-info-back">
										<h3>Salon Spaces illustrations</h3>
										<p>by Jeremy Slagle</p>
									</div>
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="ch-item ch-img-3">
							<div class="ch-info-wrap">
								<div class="ch-info">
									<div class="ch-info-front ch-img-3"></div>
									<div class="ch-info-back">
										<h3>Leadership Series #3</h3>
										<p>by Dustin Leer</p>
									</div>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>
	</div>
	<!--team-end--> 
		 </div>
 </div>	

<!-- footer -->
<div class="footer">
	 <div class="container">
		 <div class="footer-grids">
			 <div class="col-md-6 ftr-grid1">
				 <h4>About</h4>
				 <p>Nam ac interdum dui, eget iaculis augue. Cras sagittis orci leo, quis luctus diam sollicitudin in. Nullam in convallis sem. Aliquam erat felis, iaculis ac semper et, congue feugiat nibh. Nullam commodo fermentum venenatis.</p>
				 <div class="social">
					<ul>
						<li><a href="#"><i class="facebok"> </i></a></li>
						<li><a href="#"><i class="twiter"> </i></a></li>
						<li><a href="#"><i class="in"> </i></a></li>
						<li><a href="#"><i class="goog"> </i></a></li>						
							<div class="clearfix"></div>	
					</ul>
				 </div>
			 </div>
			 <div class="col-md-6 news-ltr">
				 <h4>Newsletter</h4>
				 <p>Aenean sagittis est eget elit pulvinar cursus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non purus at risus consequat finibus.</p>
				 <form>					 
					  <input type="text" class="text" value="Enter Email" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Enter Email';}">
					 <input type="submit" value="Subscribe">
					 <div class="clearfix"></div>
				 </form>
			</div>
			 <div class="clearfix"></div>
		 </div>		 
	 </div>
</div>
<div class="copywrite">
	 <div class="container">
			 <p> © 2015 Flooring. All rights reserved | Design by <a href="http://w3layouts.com/">W3layouts</a></p>
	 </div>
</div>
<!---->
<script type="text/javascript">
		$(document).ready(function() {
				/*
				var defaults = {
				containerID: 'toTop', // fading element id
				containerHoverID: 'toTopHover', // fading element hover id
				scrollSpeed: 1200,
				easingType: 'linear' 
				};
				*/
		$().UItoTop({ easingType: 'easeOutQuart' });
});
</script>
<a href="#to-top" id="toTop" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>

	 
</body>
</html>