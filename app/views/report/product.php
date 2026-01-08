<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Laporan Produk Terjual</h1>

    <!-- FILTER -->
    <form method="get" class="form-inline mb-3">
        <input type="hidden" name="controller" value="productreport">
        <input type="hidden" name="action" value="report">

        <div class="form-group mr-2">
            <label class="mr-2">Dari</label>
            <input type="date" name="from" class="form-control"
                value="<?= $_GET['from'] ?? date('Y-m-d') ?>">
        </div>

        <div class="form-group mr-2">
            <label class="mr-2">Sampai</label>
            <input type="date" name="to" class="form-control"
                value="<?= $_GET['to'] ?? date('Y-m-d') ?>">
        </div>

        <button class="btn btn-primary mr-2">
            <i class="fas fa-filter"></i> Filter
        </button>

        <!-- PRINT -->
        <a href="/pos/public/?controller=productReport&action=print&from=<?= $from ?>&to=<?= $to ?>"
            target="_blank"
            class="btn btn-danger">
            <i class="fas fa-print"></i> Print
        </a>

    </form>

    <!-- TABLE -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th width="40">No</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th class="text-right">Qty Terjual</th>
                <th class="text-right">Total Penjualan</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $grandQty = 0;
            $grandTotal = 0;
            ?>

            <?php foreach ($products as $i => $p): ?>
                <?php
                $grandQty += $p['qty_sold'];
                $grandTotal += $p['total_sales'];
                ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['category'] ?? '-') ?></td>
                    <td class="text-right"><?= $p['qty_sold'] ?></td>
                    <td class="text-right">
                        Rp <?= number_format($p['total_sales'], 0, ',', '.') ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
        <tfoot>
            <tr class="font-weight-bold">
                <td colspan="3" class="text-right">TOTAL</td>
                <td class="text-right"><?= $grandQty ?></td>
                <td class="text-right">
                    Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="container-fluid">
    <h1 class="h3 mb-4">📊 Grafik Produk Terlaris</h1>

    <canvas id="productChart" height="120"></canvas>
</div>


</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const from = '<?= $_GET['from'] ?? date('Y-m-d') ?>';
const to   = '<?= $_GET['to'] ?? date('Y-m-d') ?>';

fetch(`/pos/public/?controller=productreport&action=chart&from=${from}&to=${to}`)
    .then(res => res.json())
    .then(data => {
        console.log(data); // 🔥 DEBUG (lihat di console browser)

        if (!data.length) {
            alert('Tidak ada data');
            return;
        }

        const labels = data.map(p => p.name);
        const qty = data.map(p => Number(p.total_qty)); // ✅ FIX

        new Chart(document.getElementById('productChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: qty
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });
    });
</script>


<?php require __DIR__ . '/../layouts/footer.php'; ?>