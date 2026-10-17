<?php
class Product {
    private $conn;
    public $id;
    public $name;
    public $price;
    public $stock;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM products";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateStock($product_id, $quantity) {
        $query = "UPDATE products SET stock = stock - :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":id", $product_id);
        return $stmt->execute();
    }
}
?>