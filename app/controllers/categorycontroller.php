<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

class CategoryController
{
    private $category;

    public function __construct()
    {
        $db = (new Database())->connect();
        $this->category = new Category($db);
    }

    public function index()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $categories = $this->category->all();
        require __DIR__ . '/../views/category/index.php';
    }

    public function store()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pos/public/?controller=category&action=index');
            exit;
        }

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            $_SESSION['error'] = 'Nama kategori wajib diisi';
            header('Location: /pos/public/?controller=category&action=index');
            exit;
        }

        $this->category->create($name);

        $_SESSION['success'] = 'Kategori berhasil ditambahkan';
        header('Location: /pos/public/?controller=category&action=index');
        exit;
    }

    public function update()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pos/public/?controller=category&action=index');
            exit;
        }

        $id   = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');

        if (!$id || $name === '') {
            $_SESSION['error'] = 'Data kategori tidak valid';
            header('Location: /pos/public/?controller=category&action=index');
            exit;
        }

        $this->category->update($id, $name);

        $_SESSION['success'] = 'Kategori berhasil diperbarui';
        header('Location: /pos/public/?controller=category&action=index');
        exit;
    }

    public function delete()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->category->delete($id);
            $_SESSION['success'] = 'Kategori berhasil dihapus';
        }

        header('Location: /pos/public/?controller=category&action=index');
        exit;
    }
}
