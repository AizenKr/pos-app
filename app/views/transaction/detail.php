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
<div class="container-fluid">
    <div class="trx-modal">

        <!-- =========================
             HEADER DETAIL
        ========================= -->
        <h5 class="mb-3">Detail Penjualan</h5>

        <div class="trx-header">
            <div class="row">
                <div class="col-6 label">Tanggal</div>
                <div class="col-6 value">
                    <?= date('d M Y H:i', strtotime($transaction['created_at'])) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-6 label">Cara Bayar</div>
                <div class="col-6 value">
                    <?= htmlspecialchars($transaction['payment_method']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-6 label trx-total">Total Penjualan</div>
                <div class="col-6 value trx-total">
                    Rp <?= number_format($transaction['total'],0,',','.') ?>
                </div>
            </div>
        </div>

        <!-- =========================
             DETAIL PRODUK
        ========================= -->
        <h6 class="mb-2">Detail Produk</h6>

        <table class="table table-bordered trx-table">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td class="text-center"><?= $item['quantity'] ?></td>
                        <td class="text-right">
                            Rp <?= number_format($item['price'],0,',','.') ?>
                        </td>
                        <td class="text-right">
                            Rp <?= number_format($item['price'] * $item['quantity'],0,',','.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-right mt-3">
    <button class="btn btn-secondary" data-dismiss="modal">
        Close
    </button>
</div>


    </div>
</div>

