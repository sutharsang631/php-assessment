<?php
// class.php

class AdminPage
{
    private $conn;
    private $items_per_page = 10;

    public function __construct($host, $username, $password, $database)
    {
        $this->conn = new mysqli($host, $username, $password, $database);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function checkAdminSession()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            header("Location: shop.php");
            exit();
        }
    }

    public function getAdminOrders($page)
    {
        $admin_orders = array();
        $start_from = ($page - 1) * $this->items_per_page;

        $count_sql = "SELECT COUNT(id) AS total_orders FROM orders";
        $count_result = $this->conn->query($count_sql);
        $row = $count_result->fetch_assoc();
        $total_orders = $row['total_orders'];

        $total_pages = ceil($total_orders / $this->items_per_page);

        $sql = "SELECT * FROM orders LIMIT $start_from, $this->items_per_page";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $order_id = $row['id'];
                $user_id = $row['user_id'];
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];
                $order_date = $row['order_date'];
                $status = $row['status'];

                // Fetch user details and product details based on user_id and product_id
                $user_sql = "SELECT username FROM users WHERE id = $user_id";
                $user_result = $this->conn->query($user_sql);
                $user_row = $user_result->fetch_assoc();

                $product_sql = "SELECT name, price FROM products WHERE id = $product_id";
                $product_result = $this->conn->query($product_sql);
                $product_row = $product_result->fetch_assoc();

                // Add the order details to the $admin_orders array
                $admin_orders[] = array(
                    'order_id' => $order_id,
                    'username' => $user_row['username'],
                    'product_name' => $product_row['name'],
                    'quantity' => $quantity,
                    'price' => $product_row['price'],
                    'total' => $product_row['price'] * $quantity,
                    'order_date' => $order_date,
                    'status' => $status,
                );
            }
        }

        return array('admin_orders' => $admin_orders, 'total_pages' => $total_pages);
    }

    public function updateOrderStatus($order_id)
    {
        $update_sql = "UPDATE orders SET status = 'delivered' WHERE id = $order_id";
        if ($this->conn->query($update_sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    public function closeConnection()
    {
        $this->conn->close();
    }
}
