<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/transaction.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

class PenjualanController
{
    private $transaction;

    public function __construct()
    {
        // WAJIB: start session di controller
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Middleware dipanggil DI SINI agar semua method aman
        AuthMiddleware::check();
    RoleMiddleware::only(['admin','kasir']); 

        $db = (new Database())->connect();
        $this->transaction = new Transaction($db);
    }

    // ======================
    // HALAMAN LAPORAN
    // ======================
    public function index()
{
    $transactions = [];
    $totalSum = 0;

    $from = $_GET['from'] ?? '';
    $to   = $_GET['to'] ?? '';
    $payment_method = $_GET['payment_method'] ?? null;

    if ($from && $to) {
        $transactions = $this->transaction->reportByDate(
            $from,
            $to,
            $payment_method
        );

        foreach ($transactions as $t) {
            $totalSum += $t['total'];
        }
    }

    require __DIR__ . '/../views/report/penjualan.php';
}

   public function printReport()
{
    $start = $_GET['start'] ?? null;
    $end   = $_GET['end'] ?? null;

    $transactions = [];
    $grandTotal = 0;

    if ($start && $end) {
        $transactions = $this->transaction->reportByDate($start, $end);

        foreach ($transactions as $t) {
            $grandTotal += $t['total'];
        }
    }

    require __DIR__ . '/../views/report/print.php';
}


}

