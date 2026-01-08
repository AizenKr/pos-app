<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Shift</h1>

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
             <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
            
        <?php foreach ($shifts as $i => $s): ?>
            <tr>
                <td><?= $i + 1 ?></td>

                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <td><?= htmlspecialchars($s['cashier'] ?? '-') ?></td>
                <?php endif; ?>

                <td><?= $s['opened_at'] ?></td>
                <td><?= $s['closed_at'] ?? '-' ?></td>
                <td><?= $s['total_transactions'] ?></td>
                <td>
    <strong>
        Rp <?= number_format($s['total_amount'],0,',','.') ?>
    </strong>

    <?php if (!empty($s['payments'])): ?>
        <hr style="margin:6px 0">

        <?php foreach ($s['payments'] as $p): ?>
            <small>
                <?= strtoupper($p['payment_method']) ?> :
                Rp <?= number_format($p['total_amount'],0,',','.') ?>
                (<?= $p['total_trx'] ?> trx)
            </small><br>
        <?php endforeach; ?>

    <?php else: ?>
        <small class="text-muted">Belum ada transaksi</small>
    <?php endif; ?>
</td>

                <td>
                    <span class="badge badge-<?= $s['status'] === 'open' ? 'success' : 'secondary' ?>">
                        <?= strtoupper($s['status']) ?>
                    </span>
                </td>
                 <td>
        <!-- DETAIL -->
        <button
            class="btn btn-info btn-sm btn-shift-detail"
            data-id="<?= $s['id'] ?>">
            Detail
        </button>
    </td>
            </tr>
        <?php endforeach; ?>
        
        </tbody>
    </table>
</div>
<div class="modal fade" id="shiftDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-body" id="shiftDetailBody">
                <div class="text-center py-5">
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('btn-shift-detail')) return;

    const shiftId = e.target.dataset.id;
    const body = document.getElementById('shiftDetailBody');

    body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border"></div>
        </div>
    `;

    $('#shiftDetailModal').modal('show');

    fetch(`/pos/public/?controller=shift&action=detail&id=${shiftId}`)
        .then(res => res.text())
        .then(html => body.innerHTML = html)
        .catch(() => {
            body.innerHTML =
                '<div class="alert alert-danger">Gagal memuat detail shift</div>';
        });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
