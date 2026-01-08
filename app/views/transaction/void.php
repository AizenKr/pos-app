<h4>Void Transaksi #<?= $transaction['id'] ?></h4>

<form method="post">
    <div class="form-group">
        <label>Alasan Void</label>
        <textarea name="reason" class="form-control" required></textarea>
    </div>

    <button class="btn btn-danger">Konfirmasi Void</button>
    <a href="/pos/public/?controller=transaction&action=index"
       class="btn btn-secondary">Batal</a>
</form>
