<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Kategori</h1>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAddCategory">
        <i class="fas fa-plus"></i> Tambah Kategori
    </button>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($categories as $i => $c): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td>

                            <!-- EDIT -->
                            <button class="btn btn-sm btn-warning"
                                data-toggle="modal"
                                data-target="#modalEditCategory"
                                data-id="<?= $c['id'] ?>"
                                data-name="<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- DELETE -->
                            <a href="/pos/public/?controller=category&action=delete&id=<?= $c['id'] ?>"
                               onclick="return confirm('Hapus kategori?')"
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
<div class="modal fade" id="modalAddCategory">
    <div class="modal-dialog">
        <form method="post"
              action="/pos/public/?controller=category&action=store"
              class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" name="name" class="form-control" placeholder="Nama Kategori" required>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade" id="modalEditCategory">
    <div class="modal-dialog">
        <form method="post"
              action="/pos/public/?controller=category&action=update"
              class="modal-content">

            <input type="hidden" name="id" id="edit_category_id">

            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" name="name" id="edit_category_name" class="form-control">
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

    $('#modalEditCategory').on('show.bs.modal', function (e) {

        let b = $(e.relatedTarget);

        $('#edit_category_id').val(b.data('id'));
        $('#edit_category_name').val(b.data('name'));

    });

});
</script>
