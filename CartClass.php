<?php
class CartClass {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function updateCart(&$cart, $post) {
        foreach ($post['quantity'] as $product_id => $quantity) {
            $quantity = intval($quantity); // Ensure the quantity is an integer
            if ($quantity <= 0) {
                unset($cart[$product_id]); // Remove the item if quantity is zero or negative
            } else {
                $cart[$product_id]['quantity'] = $quantity; // Update the quantity for non-zero values
            }
        }
    }
    
    public function placeOrder($user_id, $cart) {
        try {
            $this->conn->begin_transaction(); // Start a transaction

            foreach ($cart as $product_id => $item) {
                $quantity = $item['quantity'];
                $order_date = date('Y-m-d H:i:s');

                $sql = "INSERT INTO orders (user_id, product_id, quantity, order_date) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $order_date);

                if (!$stmt->execute()) {
                    throw new Exception("Error inserting order.");
                }
            }

            $this->conn->commit(); // Commit the transaction
            $_SESSION['cart'] = [];
        } catch (Exception $e) {
            $this->conn->rollback(); // Rollback the transaction on error
            echo "Error: " . $e->getMessage();
        }
    }

    public function removeProduct($product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}
?>
