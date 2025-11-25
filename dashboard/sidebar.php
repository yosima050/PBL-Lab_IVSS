<?php
// determine current script so we can mark the active menu item
$current = basename($_SERVER['PHP_SELF']);

// helper to output 'active' class for one or multiple filenames
function is_active($names) {
    global $current;
    $names = (array) $names;
    return in_array($current, $names) ? 'active' : '';
}

// helper to expand collapse when one of its children is active
function collapse_show($names) {
    global $current;
    $names = (array) $names;
    return in_array($current, $names) ? 'show' : '';
}
?>
<style>
/* Scoped styles for sidebar active state */
.sidebar .nav-link.active {
  background: rgba(255,255,255,0.12);
  color: #fff !important;
  position: relative;
  border-radius: 0.35rem;
}

/* left indicator bar for the active item */
.sidebar .nav-link.active::after{
  content: "";
  position: absolute;
  left: 0;
  top: 6px;
  bottom: 6px;
  width: 4px;
  background: #ffdd57; /* accent color - change if needed */
  border-radius: 0 3px 3px 0;
}

/* Slight hover to match look */
.sidebar .nav-link:hover{
  background: rgba(255,255,255,0.06);
  color: #fff !important;
}

/* ensure collapse-inner active links also get the indicator */
.collapse-inner .collapse-item.active {
  background: rgba(0,0,0,0.12);
  color: #000 !important;
  position: relative;
}
.collapse-inner .collapse-item.active::after{
  content: "";
  position: absolute;
  left: -8px;
  top: 8px;
  bottom: 8px;
  width: 4px;
  background: #ffdd57;
  border-radius: 0 3px 3px 0;
}
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-microchip"></i>
        </div>
        <div class="sidebar-brand-text mx-3">LAB IVSS</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= is_active('dashboard.php') ?>">
        <a class="nav-link <?= is_active('dashboard.php') ?>" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <?php if (isset($role) && $role === 'admin_sistem') : ?>
        <div class="sidebar-heading">Administrasi</div>

        <li class="nav-item <?= is_active('pendaftaran.php') ?>">
            <a class="nav-link <?= is_active('pendaftaran.php') ?>" href="pendaftaran.php">
                <i class="fas fa-fw fa-user-plus"></i>
                <span>Pendaftaran Baru</span>
                <?php if (!empty($pendingCount) && $pendingCount > 0): ?>
                    <span class="badge badge-danger badge-counter"><?= (int)$pendingCount ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed <?= (in_array($current, ['mahasiswa.php','dosen.php','users.php'])) ? 'active' : '' ?>" href="#" data-toggle="collapse" data-target="#collapseData" aria-expanded="true" aria-controls="collapseData">
                <i class="fas fa-fw fa-database"></i>
                <span>Data Master</span>
            </a>
            <div id="collapseData" class="collapse <?= collapse_show(['mahasiswa.php','dosen.php','users.php']) ?>" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?= is_active('mahasiswa.php') ?>" href="mahasiswa.php">Data Mahasiswa</a>
                    <a class="collapse-item <?= is_active('dosen.php') ?>" href="dosen.php">Data Dosen &amp; Riset</a>
                    <a class="collapse-item <?= is_active('users.php') ?>" href="users.php">Manajemen User</a>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <?php if (isset($role) && $role === 'admin_berita') : ?>
        <div class="sidebar-heading">Konten Publik</div>

        <li class="nav-item <?= is_active('berita_pengumuman.php') ?>">
            <a class="nav-link <?= is_active('berita_pengumuman.php') ?>" href="berita_pengumuman.php">
                <i class="fas fa-fw fa-newspaper"></i>
                <span>Berita &amp; Pengumuman</span>
            </a>
        </li>
        <li class="nav-item <?= is_active('aktivitas.php') ?>">
            <a class="nav-link <?= is_active('aktivitas.php') ?>" href="aktivitas.php">
                <i class="fas fa-fw fa-camera"></i>
                <span>Aktivitas &amp; Galeri</span>
            </a>
        </li>
        <li class="nav-item <?= is_active('fasilitas_lab.php') ?>">
            <a class="nav-link <?= is_active('fasilitas_lab.php') ?>" href="fasilitas_lab.php">
                <i class="fas fa-fw fa-tools"></i>
                <span>Fasilitas Lab</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (isset($role) && $role === 'ketua_lab') : ?>
        <div class="sidebar-heading">Persetujuan</div>

        <li class="nav-item <?= is_active('approval.php') ?>">
            <a class="nav-link <?= is_active('approval.php') ?>" href="approval.php">
                <i class="fas fa-fw fa-check-circle"></i>
                <span>Approval Anggota</span>
                <?php if (!empty($waitingApproval) && $waitingApproval > 0): ?>
                    <span class="badge badge-danger badge-counter"><?= (int)$waitingApproval ?></span>
                <?php endif; ?>
            </a>
        </li>
    <?php endif; ?>

    <!-- LOGOUT -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<!-- Logout confirmation modal (added here so sidebar users get the modal automatically) -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yakin ingin keluar?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Klik "Logout" di bawah jika Anda ingin mengakhiri sesi ini.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>