<?php
class TransactionItem
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO transaction_items 
             (transaction_id, product_id, quantity, price) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['transaction_id'],
            $data['product_id'],
            $data['quantity'],
            $data['price']
        ]);
    }

    public function byTransaction($transaction_id)
    {
        $stmt = $this->db->prepare(
            "SELECT ti.*, p.name 
             FROM transaction_items ti
             JOIN products p ON ti.product_id = p.id
             WHERE ti.transaction_id = ?"
        );
        $stmt->execute([$transaction_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
