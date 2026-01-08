<h1>Edit Produk</h1>

<form method="post" action="/pos/public/?controller=product&action=update">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">

    <select name="category_id" required>
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"
                <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
    <input type="number" name="price" value="<?= $product['price'] ?>" required>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required>

    <button type="submit">Update</button>
</form>
