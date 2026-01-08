<?php

class Shift
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function open($user_id)
    {
        $stmt = $this->db->prepare("
            INSERT INTO shifts (user_id, opened_at)
            VALUES (?, NOW())
        ");
        return $stmt->execute([$user_id]);
    }
public function hasOpenShift($user_id)
{
    $stmt = $this->db->prepare("
        SELECT id FROM shifts
        WHERE user_id = ?
          AND status = 'open'
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

    public function getOpenShift($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM shifts
            WHERE user_id = ?
            AND status = 'open'
            LIMIT 1
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function closeShift($shift_id, $total_trx, $total_amount)
{
    $stmt = $this->db->prepare("
        UPDATE shifts
        SET 
            closed_at = NOW(),
            total_transactions = ?,
            total_amount = ?,
            status = 'closed'
        WHERE id = ?
    ");
    

    return $stmt->execute([
        $total_trx,
        $total_amount,
        $shift_id
    ]);
}


    // ===============================
// ADMIN — semua shift
// ===============================
public function all()
{
    $stmt = $this->db->prepare("
        SELECT *
        FROM shifts
        ORDER BY opened_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// ===============================
// KASIR — shift milik sendiri
// ===============================
public function byUser($user_id)
{
    $stmt = $this->db->prepare("
        SELECT *
        FROM shifts
        WHERE user_id = ?
        ORDER BY opened_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getSummaryByShiftId($shift_id)
{
    $stmt = $this->db->prepare("
        SELECT
            COUNT(id) AS total_transactions,
            COALESCE(SUM(total),0) AS total_amount
        FROM transactions
        WHERE shift_id = ?
          AND status = 'paid'
    ");
    $stmt->execute([$shift_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


public function getSoldProductsByShiftId($shift_id)
{
    $stmt = $this->db->prepare("
        SELECT
            p.name,
            SUM(ti.quantity) AS quantity,
            SUM(ti.quantity * ti.price) AS total
        FROM transaction_items ti
        JOIN transactions t ON t.id = ti.transaction_id
        JOIN products p ON p.id = ti.product_id
        WHERE t.status = 'paid'
          AND t.shift_id = ?
        GROUP BY ti.product_id
        ORDER BY quantity DESC
    ");
    $stmt->execute([$shift_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function reportByDate($from, $to)
{
    $stmt = $this->db->prepare("
        SELECT *
        FROM shifts
        WHERE DATE(opened_at) BETWEEN ? AND ?
        ORDER BY opened_at DESC
    ");
    $stmt->execute([$from, $to]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function reportByDateAndUser($from, $to, $user_id = null)
{
    $sql = "
        SELECT 
            s.*, 
            u.name AS cashier
        FROM shifts s
        JOIN users u ON u.id = s.user_id
        WHERE s.status = 'closed'
          AND DATE(s.opened_at) BETWEEN ? AND ?
    ";

    $params = [$from, $to];

    // jika kasir (filter user)
    if ($user_id !== null) {
        $sql .= " AND s.user_id = ?";
        $params[] = $user_id;
    }

    $sql .= " ORDER BY s.opened_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function find($id)
{
    $stmt = $this->db->prepare("
        SELECT 
            s.*,
            u.name AS cashier
        FROM shifts s
        JOIN users u ON u.id = s.user_id
        WHERE s.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}
