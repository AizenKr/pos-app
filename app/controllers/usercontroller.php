<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

class UserController
{
    private $user;

    public function __construct()
    {
        $db = (new Database())->connect();
        $this->user = new User($db);
    }

    // Halaman daftar user
    public function index()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $users = $this->user->all();
        require __DIR__ . '/../views/user/index.php';
    }

    // Form tambah user
    public function create()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);
        require __DIR__ . '/../views/user/create.php';
    }

    // Simpan user baru
    public function store()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $name     = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'kasir';

        if ($name=='' || $username=='' || $password=='') {
            $_SESSION['error'] = 'Semua field wajib diisi';
            header('Location: /pos/public/?controller=user&action=create');
            exit;
        }

        if ($this->user->usernameExists($username)) {
            $_SESSION['error'] = 'Username sudah digunakan';
            header('Location: /pos/public/?controller=user&action=create');
            exit;
        }

        $this->user->create([
            'name' => $name,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role
        ]);

        $_SESSION['success'] = 'User berhasil ditambahkan';
        header('Location: /pos/public/?controller=user&action=index');
        exit;
    }

    // Form edit user
    public function edit()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $user = $this->user->find($_GET['id']);
        require __DIR__ . '/../views/user/edit.php';
    }

    // Update user
    public function update()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $id       = $_POST['id'] ?? null;
        $name     = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $role     = $_POST['role'] ?? 'kasir';

        if (!$id || $name=='' || $username=='') {
            $_SESSION['error'] = 'Data tidak valid';
            header('Location: /pos/public/?controller=user&action=index');
            exit;
        }

        $this->user->update($id, [
            'name' => $name,
            'username' => $username,
            'role' => $role
        ]);

        $_SESSION['success'] = 'User berhasil diperbarui';
        header('Location: /pos/public/?controller=user&action=index');
        exit;
    }

    // Hapus user
    public function delete()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin']);

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->user->delete($id);
            $_SESSION['success'] = 'User berhasil dihapus';
        }
        header('Location: /pos/public/?controller=user&action=index');
        exit;
    }
}
