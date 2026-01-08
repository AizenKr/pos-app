<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

class ProductController
{
    private $product;
    private $category;

    public function __construct()
    {
        $db = (new Database())->connect();
        $this->product  = new Product($db);
        $this->category = new Category($db);
    }

    public function index()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $products   = $this->product->allWithCategory();
        $categories = $this->category->all();

        require __DIR__ . '/../views/product/index.php';
    }

    public function store()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pos/public/?controller=product&action=index');
            exit;
        }

        $name        = trim($_POST['name'] ?? '');
        $category_id = $_POST['category_id'] ?? null;
        $price       = $_POST['price'] ?? null;
        $stock       = $_POST['stock'] ?? null;

        if ($name === '' || !$category_id || $price === null || $stock === null) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            header('Location: /pos/public/?controller=product&action=index');
            exit;
        }

        $this->product->create([
            'name'        => $name,
            'category_id' => $category_id,
            'price'       => $price,
            'stock'       => $stock
        ]);

        $_SESSION['success'] = 'Produk berhasil ditambahkan';
        header('Location: /pos/public/?controller=product&action=index');
        exit;
    }

    public function update()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pos/public/?controller=product&action=index');
            exit;
        }

        $id          = $_POST['id'] ?? null;
        $name        = trim($_POST['name'] ?? '');
        $category_id = $_POST['category_id'] ?? null;
        $price       = $_POST['price'] ?? null;
        $stock       = $_POST['stock'] ?? null;

        if (!$id || $name === '' || !$category_id || $price === null || $stock === null) {
            $_SESSION['error'] = 'Data produk tidak valid';
            header('Location: /pos/public/?controller=product&action=index');
            exit;
        }

        $this->product->update($id, [
            'name'        => $name,
            'category_id' => $category_id,
            'price'       => $price,
            'stock'       => $stock
        ]);

        $_SESSION['success'] = 'Produk berhasil diperbarui';
        header('Location: /pos/public/?controller=product&action=index');
        exit;
    }

    public function delete()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->product->delete($id);
            $_SESSION['success'] = 'Produk berhasil dihapus';
        }

        header('Location: /pos/public/?controller=product&action=index');
        exit;
    }



}
