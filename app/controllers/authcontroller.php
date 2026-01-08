<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/user.php';

class AuthController
{
    private $db;
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
        $this->userModel = new User($this->db);
    }

    public function login()
    {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function authenticate()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Username atau password salah';
            header('Location: /pos/public');
            exit;
        }

        $_SESSION['user'] = [
            'id'   => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];

        header('Location: /pos/public/?controller=dashboard&action=index');
        exit;
    }

   public function register()
{
   

    require __DIR__ . '/../views/auth/register.php';
}


    public function storeRegister()
    {
        $name     = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($name == '' || $username == '' || $password == '') {
            $_SESSION['error'] = 'Semua field wajib diisi';
            header('Location: /pos/public/?controller=auth&action=register');
            exit;
        }

        if ($this->userModel->usernameExists($username)) {
            $_SESSION['error'] = 'Username sudah digunakan';
            header('Location: /pos/public/?controller=auth&action=register');
            exit;
        }

        $data = [
            'name'     => $name,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'admin'
        ];

        $this->userModel->create($data);

        $_SESSION['success'] = 'Registrasi berhasil, silakan login';
        header('Location: /pos/public');
        exit;
    }

     public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Hapus semua data session
        $_SESSION = [];

        // Hapus cookie session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();

        // Redirect ke halaman login
        header('Location: /pos/public/?controller=auth&action=login');
        exit;
    }
    
}
