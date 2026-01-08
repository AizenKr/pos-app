<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">

    <h1 class="h3 mb-4">Transaksi</h1>

    <!-- Tombol transaksi baru -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAdd">
        <i class="fas fa-plus"></i> Transaksi Baru
    </button>

    <!-- FLASH MESSAGE -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error'] ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- TABEL TRANSAKSI -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th width="50">No</th>
                <th class="sortable" data-type="date">
                    Tanggal <i class="fas fa-sort"></i>
                </th>
                <th>Total</th>
                <th class="sortable" data-type="text">
                    Pembayaran <i class="fas fa-sort"></i>
                </th>
                <th>Kasir</th>
                <th class="sortable" data-type="status">
                    Status <i class="fas fa-sort"></i>
                </th>
                <th width="220">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $i => $t): ?>
                <tr class="<?= $t['status'] === 'void' ? 'table-secondary' : '' ?>">
                    <td><?= $i + 1 ?></td>
                    <td data-value="<?= strtotime($t['created_at']) ?>">
                        <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                    </td>
                    <td>
                        Rp <?= number_format($t['total'], 0, ',', '.') ?>
                    </td>
                    <td data-value="<?= strtolower($t['payment_method']) ?>">
                        <?= htmlspecialchars($t['payment_method']) ?>
                    </td>

                    <td><?= htmlspecialchars($t['cashier']) ?></td>
                    <td data-value="<?= $t['shift_status'] === 'open' ? 1 : 2 ?>">
                        <small>
                            <?php if ($t['shift_status'] === 'open'): ?>
                                <span class="badge badge-info">SHIFT OPEN</span>
                            <?php else: ?>
                                <span class="badge badge-danger">SHIFT CLOSED</span>
                            <?php endif; ?>
                        </small>
                        <br>
                        <?php if ($t['status'] === 'void'): ?>
                            <span class="badge badge-danger">VOID</span>

                        <?php else: ?>
                            <span class="badge badge-success">PAID</span>

                            <br>


                        <?php endif; ?>
                    </td>


                    <td>

                        <!-- DETAIL -->
                        <button
                            class="btn btn-info btn-sm btn-detail"
                            data-id="<?= $t['id'] ?>">
                            Detail
                        </button>

                        <!-- PRINT -->
                        <?php if ($t['status'] === 'paid'): ?>
                            <a target="_blank"
                                href="/pos/public/?controller=transaction&action=print&id=<?= $t['id'] ?>"
                                class="btn btn-sm btn-secondary">
                                <i class="fas fa-print"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled>
                                <i class="fas fa-print"></i>
                            </button>
                        <?php endif; ?>


                        <!-- VOID (ADMIN ONLY) -->
                        <!-- =========================
     VOID TRANSAKSI
========================= -->

                        <!-- KASIR -->
                        <?php if ($_SESSION['user']['role'] === 'kasir'): ?>

                            <?php if ($t['status'] === 'paid' && $t['shift_status'] === 'open'): ?>
                                <button
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#voidModal<?= $t['id'] ?>">
                                    Ajukan VOID
                                </button>

                            <?php elseif ($t['shift_status'] === 'closed'): ?>
                                <span class="badge badge-danger">
                                    Shift Closed
                                </span>

                            <?php elseif ($t['status'] === 'pending_void'): ?>
                                <span class="badge badge-warning">Menunggu Admin</span>

                            <?php elseif ($t['status'] === 'void'): ?>
                                <span class="badge badge-secondary">VOID</span>

                            <?php endif; ?>

                        <?php endif; ?>



                        <!-- ADMIN -->
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>

                            <?php if ($t['status'] === 'paid'): ?>
                                <!-- ADMIN VOID LANGSUNG -->
                                <button
                                    class="btn btn-danger btn-sm"
                                    data-toggle="modal"
                                    data-target="#voidModal<?= $t['id'] ?>">
                                    VOID
                                </button>

                            <?php elseif ($t['status'] === 'pending_void'): ?>
                                <!-- ADMIN APPROVE / REJECT -->
                                <button
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#voidModal<?= $t['id'] ?>">
                                    Proses VOID
                                </button>

                            <?php elseif ($t['status'] === 'void'): ?>
                                <span class="badge badge-secondary">VOID</span>
                            <?php endif; ?>

                        <?php endif; ?>


                        <div class="modal fade"
                            id="voidModal<?= $t['id'] ?>"
                            tabindex="-1"
                            role="dialog"
                            aria-labelledby="voidModalLabel<?= $t['id'] ?>"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form method="post"
                                    action="/pos/public/?controller=transaction&action=void&id=<?= $t['id'] ?>"
                                    class="modal-content">

                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="voidModalLabel<?= $t['id'] ?>">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Konfirmasi VOID Transaksi
                                        </h5>
                                        <button type="button"
                                            class="close text-white"
                                            data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="alert alert-warning">

                                            <strong>PERINGATAN!</strong><br>
                                            Transaksi ini akan dibatalkan dan
                                            <strong>stok produk akan dikembalikan</strong>.
                                        </div>

                                        <table class="table table-sm table-borderless mb-3">
                                            <tr>
                                                <td width="40%">No Transaksi</td>
                                                <td>: <strong>#<?= $t['id'] ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal</td>
                                                <td>: <?= $t['created_at'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Total</td>
                                                <td>: <strong>Rp <?= number_format($t['total'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                        </table>

                                        <?php if (
                                            $_SESSION['user']['role'] === 'kasir'
                                            || ($t['status'] === 'paid' && $_SESSION['user']['role'] === 'admin')
                                        ): ?>
                                            <div class="form-group">
                                                <label>Alasan VOID</label>
                                                <textarea name="reason"
                                                    class="form-control"
                                                    rows="3"
                                                    required></textarea>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <strong>Alasan Kasir:</strong><br>
                                                <?= htmlspecialchars($t['void_reason']) ?>
                                            </div>
                                        <?php endif; ?>


                                    </div>

                                    <div class="modal-footer">

                                        <button type="button"
                                            class="btn btn-secondary"
                                            data-dismiss="modal">
                                            Batal
                                        </button>

                                        <?php if ($_SESSION['user']['role'] === 'kasir'): ?>
                                            <button type="submit"
                                                class="btn btn-warning">
                                                <i class="fas fa-paper-plane"></i> Ajukan VOID
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                            <button type="submit"
                                                name="action"
                                                value="approve"
                                                class="btn btn-danger">
                                                <i class="fas fa-check"></i> VOID
                                            </button>

                                            <button type="submit"
                                                name="action"
                                                value="reject"
                                                class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        <?php endif; ?>

                                    </div>


                                </form>
                            </div>
                        </div>


                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

</div>


<!-- =========================
     MODAL TRANSAKSI BARU
========================= -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog modal-lg">
        <form method="post"
            action="/pos/public/?controller=transaction&action=store"
            class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Transaksi Baru</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-row mb-3">
                    <div class="col-md-6">
                        <input type="text"
                            id="searchProduct"
                            class="form-control"
                            placeholder="Cari nama produk...">
                    </div>

                    <div class="col-md-6">
                        <select id="filterCategory" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="product-grid">
                    <?php foreach ($products as $p): ?>
                        <label class="product-card <?= $p['stock'] <= 0 ? 'disabled' : '' ?>"
                            data-name="<?= strtolower($p['name']) ?>"
                            data-category="<?= $p['category_id'] ?>">

                            <input type="checkbox"
                                class="product-check"
                                name="items[<?= $p['id'] ?>][product_id]"
                                value="<?= $p['id'] ?>"
                                <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>

                            <div class="product-body">
                                <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                                <div class="product-price">
                                    Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                </div>

                                <input type="hidden"
                                    class="price-input"
                                    value="<?= $p['price'] ?>">

                                <?php if ($p['stock'] > 0): ?>
                                    <input type="number"
                                        class="form-control qty-input"
                                        name="items[<?= $p['id'] ?>][quantity]"
                                        value="1"
                                        min="1"
                                        max="<?= $p['stock'] ?>">
                                <?php else: ?>
                                    <input type="number"
                                        class="form-control"
                                        value="0"
                                        disabled>
                                <?php endif; ?>
                            </div>

                        </label>

                    <?php endforeach; ?>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="Cash">Cash</option>
                        <option value="Qris">QRIS</option>
                    </select>
                </div>
                <hr>

                <div class="form-group">
                    <label>
                        <strong>Total: Rp <span id="totalAmount">0</span></strong>
                    </label>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">
                    Simpan Transaksi
                </button>
            </div>

        </form>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-body" id="detailModalBody">
            <div class="text-center py-5">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchProduct');
        const categorySelect = document.getElementById('filterCategory');
        const cards = document.querySelectorAll('.product-card');

        function filterProducts() {
            const keyword = searchInput.value.toLowerCase();
            const category = categorySelect.value;

            cards.forEach(card => {
                const name = card.dataset.name;
                const cat = card.dataset.category;

                const matchName = name.includes(keyword);
                const matchCat = !category || cat === category;

                card.style.display = (matchName && matchCat) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterProducts);
        categorySelect.addEventListener('change', filterProducts);
    });
</script>


<script>
    function calculateTotal() {
        let total = 0;

        document.querySelectorAll('.product-card').forEach(card => {
            const checkbox = card.querySelector('.product-check');

            // ⛔ cegah barang stok habis
            if (!checkbox || checkbox.disabled || !checkbox.checked) return;

            const price = parseInt(card.querySelector('.price-input').value);
            const qtyInput = card.querySelector('.qty-input');
            const qty = parseInt(qtyInput.value || 0);

            // ⛔ cegah qty 0 / negatif
            if (qty <= 0) return;

            total += price * qty;
        });

        document.getElementById('totalAmount').innerText =
            total.toLocaleString('id-ID');
    }

    // auto hitung
    document.addEventListener('change', e => {
        if (e.target.classList.contains('product-check') ||
            e.target.classList.contains('qty-input')) {
            calculateTotal();
        }
    });
</script>



<script>
    document.addEventListener('focusin', e => {
        if (e.target.classList.contains('qty-input')) {
            const card = e.target.closest('.product-card');
            const checkbox = card.querySelector('.product-check');

            // ⛔ jangan auto-check jika stok habis
            if (checkbox.disabled) {
                e.target.blur();
                return;
            }

            checkbox.checked = true;
            calculateTotal();
        }
    });
</script>
<script>
    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('btn-detail')) return;

        const id = e.target.dataset.id;
        const body = document.getElementById('detailModalBody');

        body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border"></div>
        </div>
    `;

        $('#detailModal').modal('show');

        fetch(`/pos/public/?controller=transaction&action=detail&id=${id}`)
            .then(res => res.text())
            .then(html => body.innerHTML = html)
            .catch(() => {
                body.innerHTML =
                    '<div class="alert alert-danger">Gagal memuat data</div>';
            });
    });
</script>

<script>
document.querySelectorAll('.sortable').forEach(header => {
    let asc = true;

    header.addEventListener('click', () => {
        const table = header.closest('table');
        const tbody = table.querySelector('tbody');

        // 🔑 FIX UTAMA DI SINI
        const rows = Array.from(tbody.querySelectorAll(':scope > tr'));

        const colIndex = header.cellIndex;
        const type = header.dataset.type;

        rows.sort((a, b) => {
            let A = a.children[colIndex]?.dataset.value || '';
            let B = b.children[colIndex]?.dataset.value || '';

            if (type === 'date' || type === 'number') {
                A = parseInt(A) || 0;
                B = parseInt(B) || 0;
            }

            if (A > B) return asc ? 1 : -1;
            if (A < B) return asc ? -1 : 1;
            return 0;
        });

        asc = !asc;

        rows.forEach((row, i) => {
            row.children[0].innerText = i + 1; // update No
            tbody.appendChild(row);
        });
    });
});
</script>


<style>
    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable i {
        margin-left: 4px;
        color: #999;
    }

    .sortable:hover i {
        color: #333;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }

    .product-card {
        position: relative;
        background: #126dd4ff;
        border: 2px solid #00c3ffff;
        border-radius: 14px;
        padding: 14px;
        cursor: pointer;
        transition: all .25s ease;
        color: #fff;
    }

    .product-card:hover {
        transform: translateY(-3px);
        border-color: #0dcaf0;
        box-shadow: 0 0 15px rgba(13, 202, 240, .4);
    }

    .product-card input.product-check {
        display: none;
    }

    .product-card .product-body {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .product-name {
        font-weight: 600;
        font-size: 15px;
    }

    .product-price {
        color: #0dcaf0;
        font-weight: bold;
    }

    .product-stock {
        font-size: 12px;
        opacity: .7;
    }

    .qty-input {
        margin-top: 6px;
    }

    /* AKTIF (checkbox dicentang) */
    .product-card:has(input:checked) {
        border-color: #00ff9c;
        box-shadow: 0 0 18px rgba(0, 255, 156, .6);
    }

    /* STOK HABIS */
    .product-card.disabled {
        opacity: .4;
        pointer-events: none;
        filter: grayscale(1);
    }

    .product-card.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>


<?php require __DIR__ . '/../layouts/footer.php'; ?>