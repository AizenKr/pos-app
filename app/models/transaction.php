<?php

class Transaction
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /* =====================================================
     * LIST TRANSAKSI (INDEX)
     * ===================================================== */
    public function all()
    {
        $sql = "
      SELECT 
    t.*,
    s.status AS shift_status,
    s.id     AS shift_id,
    u.name   AS cashier
FROM transactions t
JOIN shifts s ON s.id = t.shift_id
JOIN users u ON u.id = t.user_id
ORDER BY t.created_at DESC

        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =====================================================
     * FILTER LAPORAN (HANYA PAID)
     * ===================================================== */ 
    public function totalAllPaid()
{
    $stmt = $this->db->prepare("
        SELECT 
            COUNT(*) as total_trx,
            COALESCE(SUM(total),0) as total_sales
        FROM transactions
        WHERE status = 'paid'
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function totalByPaymentMethod($payment_method)
{
    $stmt = $this->db->prepare("
        SELECT 
            COUNT(*) AS total_trx,
            COALESCE(SUM(total),0) AS total_amount
        FROM transactions
        WHERE status = 'paid'
          AND payment_method = ?
    ");
    $stmt->execute([$payment_method]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
   public function reportByDate(
    $from,
    $to,
    $payment_method = null,
    $shift_id = null
) {
    $sql = "
        SELECT 
            t.*,
            u.name AS cashier
        FROM transactions t
        JOIN users u ON u.id = t.user_id
        WHERE DATE(t.created_at) BETWEEN ? AND ?
          AND t.status = 'paid'
    ";

    $params = [$from, $to];

    if ($payment_method) {
        $sql .= " AND t.payment_method = ?";
        $params[] = $payment_method;
    }

    if ($shift_id) {
        $sql .= " AND t.shift_id = ?";
        $params[] = $shift_id;
    }

    $sql .= " ORDER BY t.created_at ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    /* =====================================================
     * LAPORAN CETAK (HANYA PAID)
     * ===================================================== */
    public function getTransactions($start, $end)
    {
        $stmt = $this->db->prepare("
            SELECT 
                t.id,
                t.total,
                t.created_at,
                u.name AS user_name
            FROM transactions t
            JOIN users u ON u.id = t.user_id
            WHERE t.status = 'paid'
              AND t.created_at BETWEEN :start AND :end
            ORDER BY t.created_at DESC
        ");

        $stmt->execute([
            ':start' => $start . ' 00:00:00',
            ':end'   => $end . ' 23:59:59'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =====================================================
     * STRUK — TRANSAKSI + KASIR
     * ===================================================== */
    public function findWithUser($id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                t.*,
                u.name AS user_name
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE t.id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =====================================================
     * STRUK — ITEM + PRODUK
     * ===================================================== */
    public function getItemsWithProduct($transaction_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                ti.quantity,
                ti.price,
                p.name
            FROM transaction_items ti
            JOIN products p ON p.id = ti.product_id
            WHERE ti.transaction_id = ?
        ");

        $stmt->execute([$transaction_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function summaryForShift($user_id, $from, $to)
{
    $stmt = $this->db->prepare("
        SELECT
            COUNT(*) AS total_trx,
            COALESCE(SUM(total),0) AS total_amount
        FROM transactions
        WHERE user_id = ?
          AND status = 'paid'
          AND created_at BETWEEN ? AND ?
    ");

    $stmt->execute([$user_id, $from, $to]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    /* =====================================================
     * ITEM TRANSAKSI (UNTUK VOID)
     * ===================================================== */
    public function getItems($transaction_id)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM transaction_items
            WHERE transaction_id = ?
        ");

        $stmt->execute([$transaction_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =====================================================
     * KASIR AJUKAN VOID
     * ===================================================== */
    public function requestVoid($id, $user_id, $reason)
    {
        $stmt = $this->db->prepare("
            UPDATE transactions
            SET 
                status = 'pending_void',
                void_reason = ?,
                void_requested_by = ?
            WHERE id = ?
              AND status = 'paid'
        ");

        return $stmt->execute([$reason, $user_id, $id]);
    }

    /* =====================================================
     * ADMIN SETUJUI VOID
     * ===================================================== */
    public function approveVoid($id, $admin_id)
    {
        // Ambil item transaksi
        $items = $this->getItems($id);

        // Kembalikan stok
        foreach ($items as $item) {
            $this->db->prepare("
                UPDATE products
                SET stock = stock + ?
                WHERE id = ?
            ")->execute([
                $item['quantity'],
                $item['product_id']
            ]);
        }

        // Update status transaksi
        $stmt = $this->db->prepare("
            UPDATE transactions
            SET 
                status = 'void',
                void_approved_by = ?,
                void_approved_at = NOW()
            WHERE id = ?
              AND status = 'pending_void'
        ");

        return $stmt->execute([$admin_id, $id]);
    }

    /* =====================================================
     * ADMIN TOLAK VOID
     * ===================================================== */
    public function rejectVoid($id)
    {
        $stmt = $this->db->prepare("
            UPDATE transactions
            SET 
                status = 'paid',
                void_reason = NULL,
                void_requested_by = NULL
            WHERE id = ?
              AND status = 'pending_void'
        ");

        return $stmt->execute([$id]);
    }

    /* =====================================================
     * DASHBOARD — PRODUK TERLARIS (PAID SAJA)
     * ===================================================== */
    public function topProducts($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.name,
                SUM(ti.quantity) AS total_sold
            FROM transaction_items ti
            JOIN products p ON p.id = ti.product_id
            JOIN transactions t ON t.id = ti.transaction_id
            WHERE t.status = 'paid'
            GROUP BY p.id
            ORDER BY total_sold DESC
            LIMIT ?
        ");

        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function summaryByUser($user_id, $from)
{
    $stmt = $this->db->prepare("
        SELECT 
            COUNT(*) AS total_trx,
            COALESCE(SUM(total),0) AS total_amount
        FROM transactions
        WHERE user_id = ?
          AND status = 'paid'
          AND created_at >= ?
    ");
    $stmt->execute([$user_id, $from]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    /* =====================================================
     * DETAIL TRANSAKSI BIASA
     * ===================================================== */
    public function find($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM transactions WHERE id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =====================================================
     * SIMPAN TRANSAKSI
     * ===================================================== */
    public function create($user_id, $total, $payment_method, $shift_id = null)
    {
        $stmt = $this->db->prepare("
           INSERT INTO transactions (user_id, shift_id, total, payment_method, status)
        VALUES (?, ?, ?, ?,'paid')
    ");

        $stmt->execute([$user_id, $shift_id, $total, $payment_method]);
        return $this->db->lastInsertId();
    }
    public function filterByStatus($status)
{
    $stmt = $this->db->prepare("
        SELECT * FROM transactions
        WHERE status = ?
    ");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function totalByShift($shift_id)
{
    $stmt = $this->db->prepare("
        SELECT 
            COUNT(*) as total_trx,
            COALESCE(SUM(total),0) as total_sales
        FROM transactions
        WHERE shift_id = ?
          AND status = 'paid'
    ");
    $stmt->execute([$shift_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


public function totalByShiftAndPayment($shift_id)
{
    $stmt = $this->db->prepare("
        SELECT 
            payment_method,
            COUNT(*) AS total_trx,
            SUM(total) AS total_amount
        FROM transactions
        WHERE shift_id = ?
          AND status = 'paid'
        GROUP BY payment_method
    ");
    $stmt->execute([$shift_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function soldProductsByShift($shift_id)
{
    $stmt = $this->db->prepare("
        SELECT
            p.name,
            SUM(ti.quantity) AS quantity,
            SUM(ti.quantity * ti.price) AS total
        FROM transaction_items ti
        JOIN transactions t ON t.id = ti.transaction_id
        JOIN products p ON p.id = ti.product_id
        WHERE t.shift_id = ?
          AND t.status = 'paid'
        GROUP BY ti.product_id
        ORDER BY quantity DESC
    ");
    $stmt->execute([$shift_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function inOpenShift()
{
    $stmt = $this->db->prepare("
        SELECT 
            t.*,
            u.name AS cashier,
            s.status AS shift_status
        FROM transactions t
        JOIN shifts s ON s.id = t.shift_id
        JOIN users u ON u.id = t.user_id
        WHERE s.status = 'open'
        ORDER BY t.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function inClosedShift()
{
    $stmt = $this->db->prepare("
        SELECT 
            t.*,
            u.name AS cashier,
            s.status AS shift_status
        FROM transactions t
        JOIN shifts s ON s.id = t.shift_id
        JOIN users u ON u.id = t.user_id
        WHERE s.status = 'closed'
        ORDER BY t.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
