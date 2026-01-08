<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Produk</h1>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAdd">
        <i class="fas fa-plus"></i> Tambah Produk
    </button>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['category_name']) ?></td>
                        <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                        <td><?= $p['stock'] ?></td>
                        <td>

                            <!-- BUTTON EDIT (PENTING) -->
                            <button class="btn btn-sm btn-warning"
                                data-toggle="modal"
                                data-target="#modalEdit"
                                data-id="<?= $p['id'] ?>"
                                data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                                data-category="<?= $p['category_id'] ?>"
                                data-price="<?= $p['price'] ?>"
                                data-stock="<?= $p['stock'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- DELETE -->
                            <a href="/pos/public/?controller=product&action=delete&id=<?= $p['id'] ?>"
                               onclick="return confirm('Hapus produk?')"
                               class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD ================= -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog">
        <form method="post"
              action="/pos/public/?controller=product&action=store"
              class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" name="name" class="form-control mb-2" placeholder="Nama Produk" required>

                <select name="category_id" class="form-control mb-2" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="number" name="price" class="form-control mb-2" placeholder="Harga" required>
                <input type="number" name="stock" class="form-control" placeholder="Stok" required>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <form method="post"
              action="/pos/public/?controller=product&action=update"
              class="modal-content">

            <input type="hidden" name="id" id="edit_id">

            <div class="modal-header">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" name="name" id="edit_name" class="form-control mb-2">

                <select name="category_id" id="edit_category" class="form-control mb-2">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="number" name="price" id="edit_price" class="form-control mb-2">
                <input type="number" name="stock" id="edit_stock" class="form-control">
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

<!-- ================= SCRIPT MODAL EDIT ================= -->
<script>
$(document).ready(function () {

    $('#modalEdit').on('show.bs.modal', function (e) {

        let b = $(e.relatedTarget);

        $('#edit_id').val(b.data('id'));
        $('#edit_name').val(b.data('name'));
        $('#edit_category').val(b.data('category'));
        $('#edit_price').val(b.data('price'));
        $('#edit_stock').val(b.data('stock'));

    });

});
</script>
