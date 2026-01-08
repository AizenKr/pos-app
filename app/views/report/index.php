<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">

    <h1 class="h3 mb-4">Laporan</h1>

    <div class="row">
        <!-- LAPORAN TRANSAKSI -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">
                        <i class="fas fa-fw fa-receipt text-danger"></i>
                        Laporan Transaksi
                    </h5>

                    <p class="card-text text-muted">
                        Rekap transaksi
                    </p>

                    <a href="/pos/public/?controller=penjualan&action=index"
                        class="btn btn-danger mt-auto">
                        Pilih
                    </a>

                </div>
            </div>
        </div>

        <!-- LAPORAN SHIFT -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">
                        <i class="fas fa-clock text-primary"></i>
                        Laporan Shift
                    </h5>

                    <p class="card-text text-muted">
                        Rekap transaksi per shift kasir.
                    </p>

                    <a href="/pos/public/?controller=shiftreport&action=report"
                        class="btn btn-primary mt-auto">
                        Pilih
                    </a>
                </div>
            </div>
        </div>

        <!-- LAPORAN PRODUK TERJUAL -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">
                        <i class="fas fa-box text-success"></i>
                        Produk Terjual
                    </h5>

                    <p class="card-text text-muted">
                        Laporan produk paling banyak terjual.
                    </p>

                    <a href="/pos/public/?controller=productreport&action=report"
                        class="btn btn-success mt-auto">
                        Pilih
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>