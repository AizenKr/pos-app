<?php

class ReportController
{
    public function index()
    {
        // auth / role check (opsional sekarang, penting nanti)
        if (!isset($_SESSION['user'])) {
            header('Location: /pos/public/login');
            exit;
        }

        require __DIR__ . '/../views/report/index.php';
    }
}
