<?php
// update_order.php

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Access denied");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id'])) {
    $host = "localhost";
    $username = "root";
    $password = "Seetha@123";
    $database = "king";
    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $order_id = $_POST['order_id'];

    // Update the order status as "Shipped" in the database
    $sql = "UPDATE orders SET status = 'Shipped' WHERE order_id = $order_id";
    if ($conn->query($sql) === TRUE) {
        echo "Order marked as shipped successfully";
    } else {
        echo "Error updating order: " . $conn->error;
    }
}
?>
