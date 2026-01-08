<?php

class RoleMiddleware
{
    public static function only($roles = [])
    {
        // WAJIB
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /pos/public');
            exit;
        }

        if (!in_array($_SESSION['user']['role'], $roles)) {
            http_response_code(403);
            echo '403 - Akses ditolak';
            exit;
        }
    }
}
