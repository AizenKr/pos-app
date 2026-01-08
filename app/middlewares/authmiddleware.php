<?php

class AuthMiddleware
{
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /pos/public/?controller=auth&action=login');
            exit;
        }
    }
}
