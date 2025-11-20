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

        <div class="top-menu">
            <span class="menu"><img src="images/menu.png" alt=""></span>
            <ul class="nav1">
                <li class="<?php echo $current=='index.php' ? 'active' : ''; ?>"><a href="index.php">Beranda</a></li>
                <li class="<?php echo $current=='about.php' ? 'active' : ''; ?>"><a href="about.php">Profil Lab</a></li>
                <li class="<?php echo $current=='typo.php' ? 'active' : ''; ?>"><a href="typo.php">Anggota & Riset</a></li>
                <li class="<?php echo $current=='gallery.php' ? 'active' : ''; ?>"><a href="gallery.php">Berita & Aktivitas</a></li>
                <li class="<?php echo $current=='contact.php' ? 'active' : ''; ?>"><a href="contact.php">Join Us!</a></li>
            </ul>
        </div>

        <div class="clearfix"></div>
    </div> <!-- /.container -->
</div> <!-- /.top-header -->

<script>
    $( "span.menu" ).click(function() {
        $( "ul.nav1" ).slideToggle( 300 );
    });
</script>

