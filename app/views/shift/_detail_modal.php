<style>
.trx-modal {
    background: #2e59d9;
    color: #e5e7eb;
    border-radius: 10px;
    padding: 20px;
}

.trx-header {
    margin-bottom: 20px;
    font-size: 14px;
}

.trx-header .row {
    border-bottom: 1px solid #ffffffff;
    padding: 6px 0;
}

.trx-header .label {
    color: #ffffffff;
}

.trx-header .value {
    text-align: right;
    font-weight: 500;
}

.trx-total {
    font-size: 16px;
    font-weight: bold;
    color: #f7f8f8ff;
}

.trx-table th {
    background: #20418fff;
    color: #e5e7eb;
    border-color: #ffffffff;
    font-size: 13px;
}

.trx-table td {
    background: #20418fff;
    color: #e5e7eb;
    border-color: #fcfcfcff;
    font-size: 13px;
}

.trx-table td,
.trx-table th {
    vertical-align: middle;
}
</style>


<div class="trx-modal">

    <h5 class="mb-3">Detail Tutup Buku</h5>

    <!-- HEADER SHIFT -->
    <div class="trx-header">
        <div class="row">
            <div class="col-6 label">Kasir</div>
            <div class="col-6 value"><?= htmlspecialchars($shift['cashier'] ?? '-') ?></div>
        </div>
        <div class="row">
            <div class="col-6 label trx-total">Total Transaksi</div>
            <div class="col-6 value trx-total"><?= $summary['total_trx'] ?></div>
        </div>
        <div class="row">
            <div class="col-6 label trx-total">Total Penjualan</div>
            <div class="col-6 value trx-total">
                Rp <?= number_format($summary['total_sales'],0,',','.') ?>
            </div>
        </div>
    </div>

    <!-- DETAIL PRODUK -->
    <h6 class="mb-2">Produk Terjual</h6>

    <table class="table table-bordered trx-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td class="text-center"><?= $p['quantity'] ?></td>
                    <td class="text-right">
                        Rp <?= number_format($p['total'],0,',','.') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-right mt-3">
        <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    </div>

</div>