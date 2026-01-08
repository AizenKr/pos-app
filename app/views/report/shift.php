<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Shift Report</h1>
    <form method="get" class="form-inline mb-3">
        <input type="hidden" name="controller" value="shiftreport">
        <input type="hidden" name="action" value="report">

        <div class="form-group mr-2">
            <label class="mr-2">Dari</label>
            <input type="date"
                name="from"
                class="form-control"
                value="<?= $_GET['from'] ?? date('Y-m-d') ?>">
        </div>

        <div class="form-group mr-2">
            <label class="mr-2">Sampai</label>
            <input type="date"
                name="to"
                class="form-control"
                value="<?= $_GET['to'] ?? date('Y-m-d') ?>">
        </div>

        <button class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
    <form method="get" action="/pos/public/">
        <input type="hidden" name="controller" value="shiftreport">
        <input type="hidden" name="action" value="print">
        <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">
        <input type="hidden" name="to" value="<?= htmlspecialchars($to) ?>">

        <button type="submit" class="btn btn-primary mb-3">
            <i class="fa fa-print"></i> Print Laporan Shift
        </button>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <th>Kasir</th>
                <?php endif; ?>
                <th>Buka</th>
                <th>Tutup</th>
                <th>Total Transaksi</th>
                <th>Total Penjualan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($shifts as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>

                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <td><?= htmlspecialchars($s['cashier'] ?? '-') ?></td>
                    <?php endif; ?>

                    <td><?= date('d/m/Y H:i', strtotime($s['opened_at'])) ?></td>
                    <td>
                        <?= $s['closed_at']
                            ? date('d/m/Y H:i', strtotime($s['closed_at']))
                            : '-' ?>
                    </td>

                    <td><?= $s['total_transactions'] ?></td>
                    <td>
                        <strong>
                            Rp <?= number_format($s['total_amount'], 0, ',', '.') ?>
                        </strong>

                        <!-- PAYMENT -->
                        <?php if (!empty($s['payments'])): ?>
                            <hr style="margin:6px 0">
                            <?php foreach ($s['payments'] as $p): ?>
                                <small>
                                    <?= strtoupper($p['payment_method']) ?> :
                                    Rp <?= number_format($p['total_amount'], 0, ',', '.') ?>
                                    (<?= $p['total_trx'] ?> trx)
                                </small><br>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- PRODUK TERJUAL -->
                        <?php if (!empty($s['products'])): ?>
                            <hr style="margin:6px 0">
                            <?php foreach ($s['products'] as $p): ?>
                                <small>
                                    <?= htmlspecialchars($p['name']) ?> :
                                    <?= $p['quantity'] ?> pcs
                                    (Rp <?= number_format($p['total'], 0, ',', '.') ?>)
                                </small><br>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($s['status'] === 'open'): ?>
                            <span class="badge badge-success">
                                OPEN
                            </span>
                        <?php else: ?>
                            <span class="badge badge-secondary">
                                CLOSED
                            </span>
                            <br>
                            <small class="text-muted">
                                Durasi:
                                <?= round(
                                    (strtotime($s['closed_at']) - strtotime($s['opened_at'])) / 3600,
                                    1
                                ) ?> jam
                            </small>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>




<?php require __DIR__ . '/../layouts/footer.php'; ?>