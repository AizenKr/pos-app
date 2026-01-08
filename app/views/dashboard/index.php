<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>


<div class="container-fluid">
    <h1 class="h3 mb-4">Dashboard</h1>
    <?php if (!$shift): ?>

        <!-- OPEN SHIFT -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <h4 class="mb-3">Shift belum dibuka</h4>

                <form method="post"
                    action="/pos/public/?controller=shift&action=open">
                    <button class="btn btn-success btn-lg">
                        <i class="fas fa-play"></i> Open Shift
                    </button>
                </form>
            </div>
        </div>

    <?php else: ?>

        <!-- SHIFT AKTIF -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="text-success">Shift Aktif</h5>
                <p>
                    Dibuka:
                    <strong><?= date('d/m/Y H:i', strtotime($shift['opened_at'])) ?></strong>
                </p>

                <form method="post"
                    action="/pos/public/?controller=shift&action=close"
                    onsubmit="return confirm('Yakin tutup shift?')">
                    <button class="btn btn-danger">
                        <i class="fas fa-stop"></i> Close Shift
                    </button>
                </form>
            </div>
        </div>

    <?php endif; ?>

    <div class="row">

        <!-- Card Total Transaksi -->
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    Total Transaksi
                    <div class="h4"><?= $stats['totalTransaksi'] ?></div>
                </div>
            </div>
        </div>

        <!-- Card Total Pendapatan -->
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    Total Pendapatan
                    <div class="h4">Rp <?= number_format($stats['totalPendapatan'], 0, ',', '.') ?></div>
                </div>
            </div>
        </div>

        <!-- Card Produk Hampir Habis -->
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body">
                    Produk Hampir Habis
                    <div class="h4"><?= count($stats['produkHampirHabis']) ?></div>
                </div>
            </div>
        </div>

    </div>
  <?php if ($_SESSION['user']['role'] === 'kasir' && $activeShift): ?>
<div class="alert alert-info">
    <strong>Shift Aktif</strong><br>

    <?php foreach ($paymentSummary as $p): ?>
        <?= strtoupper($p['payment_method']) ?> :
        Rp <?= number_format($p['total_amount'],0,',','.') ?>
        (<?= $p['total_trx'] ?> trx)<br>
    <?php endforeach; ?>
</div>
<?php endif; ?>


    <div class="row">
        <!-- Chart Produk Terlaris -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">Produk Terlaris</div>
                <div class="card-body">
                    <canvas id="chartTopProducts"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartTopProducts').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?= implode(',', array_map(function ($p) {
                            return "'" . $p['name'] . "'";
                        }, $stats['produkTerlaris'])) ?>],
            datasets: [{
                label: 'Terjual (qty)',
                data: [<?= implode(',', array_column($stats['produkTerlaris'], 'total_sold')) ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>