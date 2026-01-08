<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Shift</title>

    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
        }

        h2 {
            margin: 0;
            text-align: center;
            letter-spacing: 1px;
        }

        .subtitle {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }

        .meta {
            margin-bottom: 10px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        .center { text-align: center; }
        .right { text-align: right; }

        .section-title {
            font-weight: bold;
            margin-top: 6px;
            border-bottom: 1px solid #000;
        }

        .small {
            font-size: 10px;
        }

        .shift-row {
            page-break-inside: avoid;
        }

        .no-print {
            margin-bottom: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<button class="no-print" onclick="window.print()">🖨️ Cetak</button>

<h2>LAPORAN SHIFT</h2>
<div class="subtitle">
    Periode: <strong><?= $from ?></strong> s/d <strong><?= $to ?></strong>
</div>

<table>
    <thead>
        <tr>
            <th width="30">No</th>
            <th width="110">Kasir</th>
            <th width="120">Buka</th>
            <th width="120">Tutup</th>
            <th>Detail Shift</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach ($shifts as $i => $s): ?>
        <tr class="shift-row">
            <td class="center"><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($s['cashier'] ?? '-') ?></td>
            <td><?= date('d/m/Y H:i', strtotime($s['opened_at'])) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($s['closed_at'])) ?></td>

            <td class="small">

                <div class="section-title">Ringkasan</div>
                Total Transaksi :
                <strong><?= $s['total_transactions'] ?></strong><br>
                Total Penjualan :
                <strong>Rp <?= number_format($s['total_amount'],0,',','.') ?></strong>

                <div class="section-title">Metode Pembayaran</div>
                <?php foreach ($s['payments'] as $p): ?>
                    <?= strtoupper($p['payment_method']) ?> :
                    Rp <?= number_format($p['total_amount'],0,',','.') ?>
                    (<?= $p['total_trx'] ?> trx)<br>
                <?php endforeach; ?>

                <div class="section-title">Produk Terjual</div>
                <?php foreach ($s['products'] as $p): ?>
                    <?= htmlspecialchars($p['name']) ?> —
                    <?= $p['quantity'] ?> pcs
                    (Rp <?= number_format($p['total'],0,',','.') ?>)<br>
                <?php endforeach; ?>

            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>

</body>
</html>
