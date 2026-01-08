<?php

class ProductReport
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * LAPORAN PRODUK TERJUAL
     * - by tanggal
     * - optional: by kasir
     */
    public function reportByDate($from, $to, $user_id = null)
    {
        $sql = "
            SELECT
                p.id,
                p.name,
                c.name AS category,
                SUM(ti.quantity) AS qty_sold,
                SUM(ti.quantity * ti.price) AS total_sales
            FROM transaction_items ti
            JOIN transactions t ON t.id = ti.transaction_id
            JOIN products p ON p.id = ti.product_id
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE t.status = 'paid'
              AND DATE(t.created_at) BETWEEN ? AND ?
        ";

        $params = [$from, $to];

        // 🔹 filter kasir (non-admin)
        if ($user_id) {
            $sql .= " AND t.user_id = ?";
            $params[] = $user_id;
        }

        $sql .= "
            GROUP BY p.id
            ORDER BY qty_sold DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

 public function topProducts($from, $to, $limit = 10)
{
    $limit = (int)$limit;

    $sql = "
        SELECT 
            p.name,
            SUM(ti.quantity) AS total_qty
        FROM transaction_items ti
        JOIN transactions t ON t.id = ti.transaction_id
        JOIN products p ON p.id = ti.product_id
        WHERE t.status = 'paid'
          AND DATE(t.created_at) BETWEEN ? AND ?
        GROUP BY p.id
        ORDER BY total_qty DESC
        LIMIT $limit
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$from, $to]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}