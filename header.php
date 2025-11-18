<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- header -->
<div class="top-header">
	<div class="container">
			<div class="logo">
				<a href="index.php">
					<img src="images/logo.png" alt="Politeknik Negeri Malang" />
				</a>
			</div>
		<div class="search">
			<form>
				<input type="submit" value="">
				<input type="text" value="" placeholder="Search...">
			</form>
		</div>
		<div class="top-menu">
			<span class="menu"><img src="images/menu.png" alt=""></span>
			<ul class="nav1">
				<li class="<?php echo $current=='index.php' ? 'active' : ''; ?>"><a href="index.php">Home</a></li>
				<li class="<?php echo $current=='about.php' ? 'active' : ''; ?>"><a href="about.php">About</a></li>
				<li><a class="scroll" href="#services">Services</a></li>
				<li class="<?php echo $current=='typo.php' ? 'active' : ''; ?>"><a href="typo.php">News</a></li>
				<li class="<?php echo $current=='gallery.php' ? 'active' : ''; ?>"><a href="gallery.php">Gallery</a></li>
				<li class="<?php echo $current=='contact.php' ? 'active' : ''; ?>"><a href="contact.php">Mail</a></li>
			</ul>
		</div>
		<script>
			$( "span.menu" ).click(function() {
				$( "ul.nav1" ).slideToggle( 300, function() {
					// Animation complete.
				});
			});
		</script>
		<div class="clearfix"></div>
	</div>
</div>

