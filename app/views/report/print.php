<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>

    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 4px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            font-size: 11px;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        td {
            vertical-align: middle;
        }

        td.right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            background: #fafafa;
        }

        .footer {
            margin-top: 25px;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 10mm;
            }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="margin-bottom:10px;">
    <button onclick="window.print()">Cetak</button>
    <button onclick="window.close()">Tutup</button>
</div>

<div class="header">
    <h2>POS APP</h2>
    <p>Laporan Transaksi</p>
    <p>
        Periode:
        <strong>
            <?= htmlspecialchars($_GET['start'] ?? '-') ?>
            s/d
            <?= htmlspecialchars($_GET['end'] ?? '-') ?>
        </strong>
    </p>
</div>

<table>
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="15%">ID Transaksi</th>
            <th width="35%">Tanggal</th>
            <th width="25%">Kasir</th>
            <th width="10%">Pembayaran</th>
            <th width="20%">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($transactions)): ?>
            <tr>
                <td colspan="5" style="text-align:center;">Tidak ada data</td>
            </tr>
        <?php else: ?>
            <?php $grand = 0; ?>
            <?php foreach ($transactions as $i => $t): ?>
                <tr>
                    <td style="text-align:center;"><?= $i + 1 ?></td>
                    <td><?= $t['id'] ?></td>
                       <td><?= date('d-m-Y H:i', strtotime($t['created_at'])) ?></td>
                    <td><?= htmlspecialchars($t['cashier']) ?></td>
                    <td><?= htmlspecialchars($t['payment_method']) ?></td>
                    <td class="right">
                        Rp <?= number_format($t['total'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php $grand += $t['total']; ?>
            <?php endforeach; ?>

            <tr class="total-row">
                <td colspan="5" class="right">TOTAL PENDAPATAN</td>
                <td class="right">
                    Rp <?= number_format($grand, 0, ',', '.') ?>
                </td>
                <td></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    <div>
        Dicetak oleh:<br>
        <strong><?= $_SESSION['user']['name'] ?? '-' ?></strong>
    </div>
    <div>
        Tanggal cetak:<br>
        <?= date('d-m-Y H:i') ?>
    </div>
</div>

</body>
</html>
