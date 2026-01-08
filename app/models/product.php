<?php
class Product
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function allWithCategory()
    {
        $sql = "
            SELECT 
                products.*,
                categories.name AS category_name
            FROM products
            JOIN categories ON categories.id = products.category_id
            ORDER BY products.id DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // tambahan untuk TransactionController
    public function all()
    {
        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reduceStock($id, $qty)
    {
        $stmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$qty, $id]);
    }
public function find($id)
{
    $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function lowStock($threshold = 5)
{
    $stmt = $this->db->prepare("SELECT * FROM products WHERE stock <= ? ORDER BY stock ASC");
    $stmt->execute([$threshold]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO products (name, category_id, price, stock)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['name'],
            $data['category_id'],
            $data['price'],
            $data['stock']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE products
             SET name = ?, category_id = ?, price = ?, stock = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['category_id'],
            $data['price'],
            $data['stock'],
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM products WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
    public function reportByDate($from, $to, $user_id = null)
{
    $sql = "
        SELECT 
            p.id,
            p.name,
            SUM(ti.quantity) AS total_qty,
            SUM(ti.quantity * ti.price) AS total_sales,
            AVG(ti.price) AS avg_price
        FROM transaction_items ti
        JOIN transactions t ON t.id = ti.transaction_id
        JOIN products p ON p.id = ti.product_id
        WHERE DATE(t.created_at) BETWEEN ? AND ?
          AND t.status = 'paid'
    ";

    $params = [$from, $to];

    if ($user_id) {
        $sql .= " AND t.user_id = ?";
        $params[] = $user_id;
    }

    $sql .= "
        GROUP BY p.id
        ORDER BY total_qty DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
