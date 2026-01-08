<h1>Edit Kategori</h1>

<form method="post" action="/pos/public/?controller=category&action=update">
    <input type="hidden" name="id" value="<?= $category['id'] ?>">
    <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
    <button type="submit">Update</button>
</form>
