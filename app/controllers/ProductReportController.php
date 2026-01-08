<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ProductReport.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

class ProductReportController
{
    private $report;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        AuthMiddleware::check();
        RoleMiddleware::only(['admin','kasir']);

        $db = (new Database())->connect();
        $this->report = new ProductReport($db);
    }

    // ==========================
    // HALAMAN REPORT
    // ==========================
    public function report()
    {
        $from = $_GET['from'] ?? date('Y-m-d');
        $to   = $_GET['to'] ?? date('Y-m-d');

        $role = $_SESSION['user']['role'];
        $user_id = ($role === 'admin') ? null : $_SESSION['user']['id'];

        $products = $this->report->reportByDate($from, $to, $user_id);

        require __DIR__ . '/../views/report/product.php';
    }

    // ==========================
    // PRINT REPORT
    // ==========================
    public function print()
    {
        $from = $_GET['from'] ?? null;
        $to   = $_GET['to'] ?? null;

        if (!$from || !$to) {
            die('Periode tidak valid');
        }

        $role = $_SESSION['user']['role'];
        $user_id = ($role === 'admin') ? null : $_SESSION['user']['id'];

        $products = $this->report->reportByDate($from, $to, $user_id);

        require __DIR__ . '/../views/report/product_print.php';
    }
public function top()
{
    $from = $_GET['from'] ?? date('Y-m-d');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $products = $this->report->topProducts($from, $to);

    require __DIR__ . '/../views/report/product_top.php';
}

    
 

public function chartView()
{
    require __DIR__ . '/../views/report/product.php';
}
public function chart()
{
    $from = $_GET['from'] ?? date('Y-m-d');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $data = $this->report->topProducts($from, $to, 10);

    header('Content-Type: application/json');
    echo json_encode($data);
}



}
