<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require __DIR__ . '/../layouts/topbar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Manajemen User</h1>

    <!-- Tombol Tambah User -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAdd">
        <i class="fas fa-plus"></i> Tambah User
    </button>

    <!-- Tabel User -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($users as $i => $u): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-sm btn-warning btn-edit"
                                    data-toggle="modal"
                                    data-target="#modalEdit"
                                    data-id="<?= $u['id'] ?>"
                                    data-name="<?= htmlspecialchars($u['name']) ?>"
                                    data-username="<?= htmlspecialchars($u['username']) ?>"
                                    data-role="<?= htmlspecialchars($u['role']) ?>">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Tombol Hapus -->
                            <a href="/pos/public/?controller=user&action=delete&id=<?= $u['id'] ?>"
                               onclick="return confirm('Hapus user?')"
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

<!-- MODAL ADD -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog">
        <form method="post" action="/pos/public/?controller=user&action=store" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" name="name" class="form-control mb-2" placeholder="Nama" required>
                <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                <select name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                </select>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <form method="post" action="/pos/public/?controller=user&action=update" class="modal-content">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" name="name" id="edit_name" class="form-control mb-2" placeholder="Nama" required>
                <input type="text" name="username" id="edit_username" class="form-control mb-2" placeholder="Username" required>
                <select name="role" id="edit_role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Script untuk mengisi data modal edit -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    $('#modalEdit').on('show.bs.modal', function (e) {
        // Tombol yang memicu modal
        let btn = $(e.relatedTarget);
        // Ambil data dari tombol
        let id = btn.data('id');
        let name = btn.data('name');
        let username = btn.data('username');
        let role = btn.data('role');

        // Isi form modal
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_username').val(username);
        $('#edit_role').val(role);
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
