<h1>Tambah Produk</h1>

<form method="post" action="/pos/public/?controller=product&action=store">
    <select name="category_id" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="name" placeholder="Nama produk" required>
    <input type="number" name="price" placeholder="Harga" required>
    <input type="number" name="stock" placeholder="Stok" required>

    <button type="submit">Simpan</button>
</form>
