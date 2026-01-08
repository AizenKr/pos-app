<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ . '/../models/transaction.php';
require_once __DIR__ . '/../models/shift.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

class DashboardController
{
    private $product;
    private $category;
    private $transaction;
    private $shift;

    public function __construct()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin', 'kasir']);

        $db = (new Database())->connect();
        $this->product     = new Product($db);
        $this->category    = new Category($db);
        $this->transaction = new Transaction($db);
        $this->shift       = new Shift($db); // ✅ TAMBAHKAN INI
    }

    public function index()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin', 'kasir']);

        $user_id = $_SESSION['user']['id'];

        // 🔑 SHIFT (khusus kasir)
        $shift = null;
        $shiftOpen = false;
       $activeShift = null;

if ($_SESSION['user']['role'] === 'kasir') {

    $shift = $this->shift->getOpenShift($user_id);

    if ($shift) {

        // 🔥 HITUNG REALTIME DARI TRANSAKSI
        $summary = $this->transaction
            ->totalByShift($shift['id']);

        // GABUNGKAN DATA SHIFT + SUMMARY
        $activeShift = array_merge($shift, [
            'total_transactions' => $summary['total_trx'],
            'total_amount'       => $summary['total_sales'],
        ]);
    }
}


       // ===============================
// STATISTIK TRANSAKSI
// ===============================
if ($_SESSION['user']['role'] === 'kasir' && $shiftOpen) {

    // 🔒 KASIR → DARI SHIFT AKTIF
    $shiftSummary = $this->transaction
        ->totalByShift($shift['id']);

    $stats['totalTransaksi']  = $shiftSummary['total_trx'];
    $stats['totalPendapatan'] = $shiftSummary['total_sales'];

} else {

    // 🔓 ADMIN → SEMUA TRANSAKSI
    $all = $this->transaction->totalAllPaid();

    $stats['totalTransaksi']  = $all['total_trx'];
    $stats['totalPendapatan'] = $all['total_sales'];
}

$activeShift = $this->shift->getOpenShift($user_id);
$paymentSummary = [];

if ($activeShift) {
    $paymentSummary = $this->transaction
        ->totalByShiftAndPayment($activeShift['id']);
}


        // DATA UMUM
        $products     = $this->product->allWithCategory();
        $categories   = $this->category->all();

        // ⚠️ AMBIL HANYA TRANSAKSI PAID
        $paidTransactions = $this->transaction->filterByStatus('paid');

        // STATISTIK REAL
        $stats = [
            'totalTransaksi'    => count($paidTransactions),
            'totalPendapatan'   => array_sum(array_column($paidTransactions, 'total')),
            'produkHampirHabis' => $this->product->lowStock(5),
            'produkTerlaris'    => $this->transaction->topProducts(5)
        ];

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
