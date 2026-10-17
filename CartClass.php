<?php
class Cart {
    public function __construct($db = null) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
    }

    public function add($product_id, $quantity) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    public function getCartItems() {
        return $_SESSION['cart'];
    }

    public function clear() {
        unset($_SESSION['cart']);
    }
}
?>