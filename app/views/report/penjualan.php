<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Laporan Penjualan</h1>

    <form method="get" class="form-inline mb-3">
        <input type="hidden" name="controller" value="penjualan">
        <input type="hidden" name="action" value="index">
        <div class="form-group mr-2">
    <label>From:</label>
    <input
        type="date"
        name="from"
        class="form-control ml-2"
        value="<?= htmlspecialchars($_GET['from'] ?? '') ?>"
        required
    >
</div>

<div class="form-group mr-2">
    <label>To:</label>
    <input
        type="date"
        name="to"
        class="form-control ml-2"
        value="<?= htmlspecialchars($_GET['to'] ?? '') ?>"
        required
    >
</div>

<div class="form-group mr-2">
    <label>Payment:</label>
    <select name="payment_method" class="form-control ml-2">
        <option value="">Semua</option>
        <option value="cash" <?= ($_GET['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
        <option value="qris" <?= ($_GET['payment_method'] ?? '') === 'qris' ? 'selected' : '' ?>>QRIS</option>
    </select>
</div>

<button class="btn btn-primary">Filter</button>

    </form>
<a target="_blank"
  href="?controller=penjualan&action=printReport&start=<?= $from ?>&end=<?= $to ?>"
   class="btn btn-primary">
   <i class="fas fa-print"></i> Cetak Laporan
</a>


    <?php if(count($transactions) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Pembayaran</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($transactions as $i => $t): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= $t['created_at'] ?></td>
                        <td><?= $t['cashier'] ?></td>
                        <td><?= $t['payment_method'] ?></td>
                        <td>Rp <?= number_format($t['total'],0,',','.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total Penjualan:</th>
                    <th>Rp <?= number_format($totalSum,0,',','.') ?></th>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p>Tidak ada transaksi untuk periode ini.</p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
