<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
       href="/pos/public/?controller=dashboard&action=index">
        <div class="sidebar-brand-icon">
            <i class="fas fa-cash-register"></i>
        </div>
        <div class="sidebar-brand-text mx-3">POS App</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="/pos/public/?controller=dashboard&action=index">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- ADMIN MENU -->
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Master Data
        </div>

        <!-- Kategori -->
        <li class="nav-item">
            <a class="nav-link" href="/pos/public/?controller=category&action=index">
                <i class="fas fa-fw fa-tags"></i>
                <span>Kategori</span>
            </a>
        </li>

        <!-- Produk -->
        <li class="nav-item">
            <a class="nav-link" href="/pos/public/?controller=product&action=index">
                <i class="fas fa-fw fa-box"></i>
                <span>Produk</span>
            </a>
        </li>

        <!-- User -->
        <li class="nav-item">
            <a class="nav-link" href="/pos/public/?controller=user&action=index">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>
        </li>

    <?php endif; ?>

    <!-- KASIR / UMUM -->
    <hr class="sidebar-divider">


    <!-- Transaksi -->
    <li class="nav-item">
        <a class="nav-link" href="/pos/public/?controller=transaction&action=index">
            <i class="fas fa-fw fa-receipt"></i>
            <span>Transaksi</span>
        </a>
    </li>
    <li class="nav-item">
    <a class="nav-link"
       href="/pos/public/?controller=shift&action=report">
        <i class="fas fa-clock"></i>
        <span>Shift Report</span>
    </a>
</li>
 <!-- Laporan -->
    <li class="nav-item">
        <a class="nav-link" href="/pos/public/?controller=report&action=index">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Laporan</span>
        </a>
    </li>

</ul>
