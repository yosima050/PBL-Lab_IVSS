<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-microchip"></i>
        </div>
        <div class="sidebar-brand-text mx-3">LAB IVSS</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <?php if (isset($role) && $role === 'admin_sistem') : ?>
        <div class="sidebar-heading">Administrasi</div>

        <li class="nav-item">
            <a class="nav-link" href="pendaftaran.php">
                <i class="fas fa-fw fa-user-plus"></i>
                <span>Pendaftaran Baru</span>
                <?php if (!empty($pendingCount) && $pendingCount > 0): ?>
                    <span class="badge badge-danger badge-counter"><?= (int)$pendingCount ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseData" aria-expanded="true" aria-controls="collapseData">
                <i class="fas fa-fw fa-database"></i>
                <span>Data Master</span>
            </a>
            <div id="collapseData" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="mahasiswa.php">Data Mahasiswa</a>
                    <a class="collapse-item" href="dosen.php">Data Dosen &amp; Riset</a>
                    <a class="collapse-item" href="users.php">Manajemen User</a>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <?php if (isset($role) && $role === 'admin_berita') : ?>
        <div class="sidebar-heading">Konten Publik</div>

        <li class="nav-item">
            <a class="nav-link" href="berita.php">
                <i class="fas fa-fw fa-newspaper"></i>
                <span>Berita &amp; Pengumuman</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="aktivitas.php">
                <i class="fas fa-fw fa-camera"></i>
                <span>Aktivitas &amp; Galeri</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="fasilitas.php">
                <i class="fas fa-fw fa-tools"></i>
                <span>Fasilitas Lab</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if (isset($role) && $role === 'ketua_lab') : ?>
        <div class="sidebar-heading">Persetujuan</div>

        <li class="nav-item">
            <a class="nav-link" href="approval.php">
                <i class="fas fa-fw fa-check-circle"></i>
                <span>Approval Anggota</span>
                <?php if (!empty($waitingApproval) && $waitingApproval > 0): ?>
                    <span class="badge badge-danger badge-counter"><?= (int)$waitingApproval ?></span>
                <?php endif; ?>
            </a>
        </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>