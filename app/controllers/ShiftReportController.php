<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/shift.php';
require_once __DIR__ . '/../models/transaction.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

class ShiftReportController
{
    private $shift;
    private $transaction;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        AuthMiddleware::check();
        RoleMiddleware::only(['admin', 'kasir']);

        $db = (new Database())->connect();
        $this->shift = new Shift($db);
        $this->transaction = new Transaction($db);
    }

    // =====================
    // HALAMAN REPORT
    // =====================
    public function report()
    {
        $from = $_GET['from'] ?? date('Y-m-d');
        $to   = $_GET['to'] ?? date('Y-m-d');

        $role = $_SESSION['user']['role'];
        $user_id = $_SESSION['user']['id'];

        if ($role === 'admin') {
            $shifts = $this->shift->reportByDateAndUser($from, $to);
        } else {
            $shifts = $this->shift->reportByDateAndUser($from, $to, $user_id);
        }

        // 🔥 TAMBAHKAN INI
        foreach ($shifts as &$s) {
            $s['payments'] = $this->transaction
                ->totalByShiftAndPayment($s['id']);

            $s['products'] = $this->transaction
                ->soldProductsByShift($s['id']);
        }
        unset($s);

        require __DIR__ . '/../views/report/shift.php';
    }


    // =====================
    // PRINT
    // =====================
    public function print()
{
    $from = $_GET['from'] ?? null;
    $to   = $_GET['to'] ?? null;

    if (!$from || !$to) {
        die('Tanggal tidak valid');
    }

    $role    = $_SESSION['user']['role'];
    $user_id = $_SESSION['user']['id'];

    if ($role === 'admin') {
        $shifts = $this->shift->reportByDateAndUser($from, $to);
    } else {
        $shifts = $this->shift->reportByDateAndUser($from, $to, $user_id);
    }

    foreach ($shifts as &$s) {
        $s['payments'] = $this->transaction
            ->totalByShiftAndPayment($s['id']);

        $s['products'] = $this->transaction
            ->soldProductsByShift($s['id']);
    }
    unset($s);

    require __DIR__ . '/../views/report/shift_print.php';
}

}
