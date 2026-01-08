<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/shift.php';
require_once __DIR__ . '/../models/transaction.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

class ShiftController
{
    private $shift;
    private $transaction;

    public function __construct()
    {
        AuthMiddleware::check();

        $db = (new Database())->connect();
        $this->shift = new Shift($db);
        $this->transaction = new Transaction($db);
    }

    public function open()
    {
       

        $user_id = $_SESSION['user']['id'];

        $existing = $this->shift->getOpenShift($user_id);
        if ($existing) {
            $_SESSION['error'] = 'Shift masih terbuka';
            header('Location: /pos/public/?controller=dashboard');
            exit;
        }

        $this->shift->open($user_id);
       $_SESSION['success'] = 'Shift dibuka';
header('Location: /pos/public/?controller=dashboard&action=index');
exit;

    }

   public function close()
{
    AuthMiddleware::check();

    $user_id = $_SESSION['user']['id'];
    $shift = $this->shift->getOpenShift($user_id);

    if (!$shift) {
        $_SESSION['error'] = 'Tidak ada shift aktif';
        header('Location: /pos/public/?controller=dashboard&action=index');
        exit;
    }

    // ✅ AMBIL SHIFT ID
    $shift_id = $shift['id'];

    // ✅ HITUNG TOTAL TRANSAKSI BERDASARKAN SHIFT
    $summary = $this->transaction->totalByShift($shift_id);

    // ✅ SIMPAN KE TABEL SHIFTS (INI YANG MEMBUAT REPORT TERISI)
    $this->shift->closeShift(
        $shift_id,
        $summary['total_trx'],
        $summary['total_sales']
    );

    $_SESSION['success'] = 'Shift berhasil ditutup';
    header('Location: /pos/public/?controller=dashboard&action=index');
    exit;
}

public function report()
{
    AuthMiddleware::check();

    $role = $_SESSION['user']['role'];
    $user_id = $_SESSION['user']['id'];

    if ($role === 'admin') {
        $shifts = $this->shift->all();
    } else {
        $shifts = $this->shift->byUser($user_id);
    }

   foreach ($shifts as &$s) {
    $s['payments'] = $this->transaction
        ->totalByShiftAndPayment($s['id']);
}
unset($s); // 🔥 WAJIB

    require __DIR__ . '/../views/shift/report.php';
}


 public function detail()
{
    AuthMiddleware::check();

    $shift_id = $_GET['id'] ?? null;
    if (!$shift_id) {
        echo 'Shift tidak valid';
        return;
    }

    $shift = $this->shift->find($shift_id);

    $summary = $this->transaction->totalByShift($shift_id);
    $products = $this->transaction->soldProductsByShift($shift_id);

    require __DIR__ . '/../views/shift/_detail_modal.php';
}
}
