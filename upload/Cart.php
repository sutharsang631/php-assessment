<?php
// Cart.php

require_once "Database.php";

class Cart {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addToCart($product_id, $quantity) {
        // Check if the product exists and is available in stock
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false; // Product not found
        }

        $row = $result->fetch_assoc();
        if ($row['stock'] === 0 || $quantity > $row['stock']) {
            return false; // Product out of stock or insufficient quantity
        }

        // Check if the product is already in the cart
        if ($this->isProductInCart($product_id)) {
            $this->updateCartItem($product_id, $quantity);
        } else {
            $this->insertCartItem($product_id, $quantity);
        }

        return true;
    }

    private function isProductInCart($product_id) {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return false;
        }

        return isset($_SESSION['cart'][$product_id]);
    }

    private function updateCartItem($product_id, $quantity) {
        $cart = $_SESSION['cart'];
        $cart[$product_id]['quantity'] = $quantity;
        $_SESSION['cart'] = $cart;
    }

    private function insertCartItem($product_id, $quantity) {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
        $sql = "SELECT name, price FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $cart[$product_id] = array(
            'name' => $row['name'],
            'price' => $row['price'],
            'quantity' => $quantity
        );

        $_SESSION['cart'] = $cart;
    }

    public function removeFromCart($product_id) {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return;
        }

        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    public function clearCart() {
        $_SESSION['cart'] = array();
    }

    public function getCartItems() {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    }

    public function getTotalAmount() {
        $total_amount = 0;

        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }
        }

        return $total_amount;
    }
}
?>
