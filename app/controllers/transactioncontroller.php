<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/transaction.php';
require_once __DIR__ . '/../models/transactionitem.php';
require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ . '/../models/shift.php';
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';


class TransactionController
{
    private $transaction;
    private $item;
    private $product;
    private $category;
    private $shift;
    public function __construct()
    {
        $db = (new Database())->connect();
        $this->transaction = new Transaction($db);
        $this->item        = new TransactionItem($db);
        $this->product     = new Product($db);
        $this->category = new Category($db);
        $this->shift = new Shift($db);
    }

    // Halaman list transaksi
    public function index()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin', 'kasir']);

        if ($_SESSION['user']['role'] === 'kasir') {
            if (!$this->shift->hasOpenShift($_SESSION['user']['id'])) {
                $_SESSION['error'] = 'Silakan buka shift terlebih dahulu';
                header('Location: /pos/public/?controller=dashboard&action=index');
                exit;
            }
        }

        $transactions = $this->transaction->all();
        $products     = $this->product->all();
        $categories   = $this->category->all();
        $openShiftTransactions   = $this->transaction->inOpenShift();
        $closedShiftTransactions = $this->transaction->inClosedShift();


        require __DIR__ . '/../views/transaction/index.php';
    }

    // Simpan transaksi baru
    public function store()
    {
        AuthMiddleware::check();
        RoleMiddleware::only(['admin', 'kasir']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pos/public/?controller=transaction&action=index');
            exit;
        }

        $items = array_filter($_POST['items'] ?? [], function ($item) {
            return isset($item['product_id']) && isset($item['quantity']);
        });

        if (count($items) === 0) {
            $_SESSION['error'] = 'Transaksi kosong';
            header('Location: /pos/public/?controller=transaction&action=index');
            exit;
        }
        // AMBIL SEMUA PRODUK SEKALI (CACHE)
        $productsCache = [];
        foreach ($this->product->all() as $p) {
            $productsCache[$p['id']] = $p;
        }


        // CEK STOK
        foreach ($items as $i) {
            $product = $this->product->find($i['product_id']);
            if ($i['quantity'] > $product['stock']) {
                $_SESSION['error'] = "Stok {$product['name']} tidak cukup";
                header('Location: /pos/public/?controller=transaction&action=index');
                exit;
            }
        }

        // HITUNG TOTAL
        $total = 0;

        foreach ($items as $i) {
            $product = $productsCache[$i['product_id']] ?? null;

            if (!$product) {
                $_SESSION['error'] = 'Produk tidak ditemukan';
                header('Location: /pos/public/?controller=transaction&action=index');
                exit;
            }

            if ($i['quantity'] > $product['stock']) {
                $_SESSION['error'] = "Stok {$product['name']} tidak cukup";
                header('Location: /pos/public/?controller=transaction&action=index');
                exit;
            }

            $total += $product['price'] * $i['quantity'];
        }


        // ===============================
        // AMBIL SHIFT AKTIF
        // ===============================
        $shift_id = null;

        if ($_SESSION['user']['role'] === 'kasir') {
            $shift = $this->shift->getOpenShift($_SESSION['user']['id']);

            if (!$shift) {
                $_SESSION['error'] = 'Shift belum dibuka';
                header('Location: /pos/public/?controller=dashboard&action=index');
                exit;
            }

            $shift_id = $shift['id'];
        }

        $paymentMethod = $_POST['payment_method'] ?? null;

        if (!$paymentMethod) {
            $_SESSION['error'] = 'Metode pembayaran wajib dipilih';
            header('Location: /pos/public/?controller=transaction&action=index');
            exit;
        }

        $trx_id = $this->transaction->create(
            $_SESSION['user']['id'],
            $total,
            $paymentMethod,
            $shift_id
        );

        foreach ($items as $i) {
            $product = $productsCache[$i['product_id']];

            $this->item->create([
                'transaction_id' => $trx_id,
                'product_id'     => $product['id'],
                'price'          => $product['price'], // ⬅️ DARI DB
                'quantity'       => $i['quantity']
            ]);

            $this->product->reduceStock(
                $product['id'],
                $i['quantity']
            );
        }


        $_SESSION['success'] = 'Transaksi berhasil disimpan';
        header('Location: /pos/public/?controller=transaction&action=index');
        exit;
    }



    public function void()
    {
        AuthMiddleware::check();

        $id = $_GET['id'] ?? null;
        if (!$id) die('ID transaksi tidak valid');

        $transaction = $this->transaction->find($id);
        if (!$transaction) die('Transaksi tidak ditemukan');

        // ======================
        // KASIR → AJUKAN VOID
        // ======================
        if ($_SESSION['user']['role'] === 'kasir') {

            if ($transaction['status'] !== 'paid') {
                $_SESSION['error'] = 'Transaksi tidak bisa diajukan VOID';
                header('Location: /pos/public/?controller=transaction&action=index');
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $reason = $_POST['reason'] ?? '-';

                $this->transaction->requestVoid(
                    $id,
                    $_SESSION['user']['id'],
                    $reason
                );

                $_SESSION['success'] = 'Permintaan VOID dikirim ke admin';
                header('Location: /pos/public/?controller=transaction&action=index');
                exit;
            }
            $shift = $this->shift->find($transaction['shift_id']);

            if ($shift['status'] === 'closed') {
                $_SESSION['error'] = 'Transaksi dari shift tertutup tidak bisa di-VOID';
                header('Location: ...');
                exit;
            }

            return;
        }

        // ======================
        // ADMIN
        // ======================
        if ($_SESSION['user']['role'] === 'admin') {

            // 🔴 ADMIN VOID LANGSUNG (tanpa request kasir)
            if (
                $transaction['status'] === 'paid'
                && $_SERVER['REQUEST_METHOD'] === 'POST'
                && !isset($_POST['action'])
            ) {
                $reason = $_POST['reason'] ?? 'Void oleh admin';

                // ⬅️ PAKAI void(), BUKAN approveVoid()
                $this->transaction->void($id, $reason);

                $_SESSION['success'] = 'Transaksi berhasil di-VOID';
                header('Location: /pos/public/?controller=transaction&action=index');
                exit;
            }

            // 🟡 ADMIN APPROVE / REJECT VOID DARI KASIR
            if ($transaction['status'] === 'pending_void') {

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    if ($_POST['action'] === 'approve') {
                        $this->transaction->approveVoid(
                            $id,
                            $_SESSION['user']['id']
                        );
                        $_SESSION['success'] = 'VOID disetujui';
                    }

                    if ($_POST['action'] === 'reject') {
                        $this->transaction->rejectVoid($id);
                        $_SESSION['success'] = 'VOID ditolak';
                    }

                    header('Location: /pos/public/?controller=transaction&action=index');
                    exit;
                }
            }

            $_SESSION['error'] = 'Aksi VOID tidak valid';
            header('Location: /pos/public/?controller=transaction&action=index');
            exit;
        }


        die('Akses ditolak');
    }

    public function print()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die('ID transaksi tidak ditemukan');
        }

        $transaction = $this->transaction->findWithUser($id);
        $items       = $this->transaction->getItemsWithProduct($id);

        if (!$transaction) {
            die('Transaksi tidak ditemukan');
        }

        require __DIR__ . '/../views/transaction/print.php';
    }

    // Detail transaksi
    public function detail()
    {
        AuthMiddleware::check();


        $transaction_id = $_GET['id'] ?? null;
        if (!$transaction_id) {
            header('Location: /pos/public/?controller=transaction&action=index');
            exit;
        }

        $transaction = $this->transaction->find($transaction_id);
        $items       = $this->item->byTransaction($transaction_id);
        require __DIR__ . '/../views/transaction/detail.php';
    }
    
}
