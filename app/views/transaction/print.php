<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Struk</title>

    <style>
        /* =========================
   RESET
========================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* =========================
   TAMPILAN NORMAL (LAYAR)
========================= */
        body {
            font-family: monospace;
            background: #f5f5f5;
        }

        .receipt {
            width: 48mm;
            /* 🔥 PENTING */
            margin: 0 auto;
            background: #fff;
            padding: 4px;
            /* kecilkan */
            font-size: 10px;
            /* kecilkan */
        }

        table {
            width: 100%;
            font-size: 10px;
        }

        td {
            padding: 1px 0;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        table {
            width: 100%;
            font-size: 11px;
        }

        td {
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        .total {
            font-weight: bold;
        }

        .btn-print {
            width: 100%;
            padding: 6px;
            margin-top: 10px;
            cursor: pointer;
        }

        /* =========================
   MODE PRINT (PALING PENTING)
========================= */
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }

            html,
            body {
                width: 58mm;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }

            .receipt {
                width: 48mm;
                padding: 3px;
                border: none;
            }

            .btn-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="receipt" id="receipt">

        <div class="center">
            <strong>POS APP</strong><br>
            ====================
        </div>

        Kasir : <?= htmlspecialchars($transaction['user_name']) ?><br>
        Tgl : <?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?><br>
        No : <?= $transaction['id'] ?> <br>
        Pembayaran : <?= htmlspecialchars($transaction['payment_method']) ?><br>
        <div class="line"></div>

        <table>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td colspan="2"><?= htmlspecialchars($item['name']) ?></td>
                </tr>
                <tr>
                    <td>
                        <?= $item['quantity'] ?> x <?= number_format($item['price'], 0, ',', '.') ?>
                    </td>
                    <td class="right">
                        <?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?>
                    </td>

                </tr>
            <?php endforeach; ?>
        </table>

        <div class="line"></div>

        <table>
            <tr class="total">
                <td>TOTAL</td>
                <td class="right">
                    Rp <?= number_format($transaction['total'], 0, ',', '.') ?>
                </td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="center">
            Terima kasih 🙏<br>
            Barang yang sudah dibeli<br>
            tidak dapat dikembalikan
        </div>

        <button class="btn-print" onclick="printReceipt()">Cetak Struk</button>

    </div>

    <script>
        let alreadyPrinted = false;

        function printReceipt() {
            if (alreadyPrinted) return;
            alreadyPrinted = true;

            window.print();

            // tutup tab agar printer berhenti total
            setTimeout(() => {
                window.close();
            }, 1000);
        }
    </script>

</body>

</html>