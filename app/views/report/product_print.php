<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Produk</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .period {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
        }

        .right {
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<button class="no-print" onclick="window.print()">🖨️ Print</button>

<h2>LAPORAN PRODUK TERJUAL</h2>
<div class="period">
    Periode: <?= $from ?> s/d <?= $to ?>
</div>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Produk</th>
        <th>Kategori</th>
        <th class="right">Qty</th>
        <th class="right">Total</th>
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
            <td class="right"><?= $p['qty_sold'] ?></td>
            <td class="right">
                Rp <?= number_format($p['total_sales'],0,',','.') ?>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
    <tfoot>
    <tr>
        <th colspan="3" class="right">TOTAL</th>
        <th class="right"><?= $grandQty ?></th>
        <th class="right">
            Rp <?= number_format($grandTotal,0,',','.') ?>
        </th>
    </tr>
    </tfoot>
</table>

</body>
</html>
