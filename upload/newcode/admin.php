<?php
// admin.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: shop.php");
    exit();
}
// Database connection
$host = "localhost";
$username = "root";
$password = "Seetha@123";
$database = "king";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination
$items_per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $items_per_page;

// Fetch admin orders from the database with pagination
$admin_orders = array();
$count_sql = "SELECT COUNT(id) AS total_orders FROM orders";
$count_result = $conn->query($count_sql);
$row = $count_result->fetch_assoc();
$total_orders = $row['total_orders'];

$total_pages = ceil($total_orders / $items_per_page);

$sql = "SELECT * FROM orders LIMIT $start_from, $items_per_page";
$result = $conn->query($sql);

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
        $user_result = $conn->query($user_sql);
        $user_row = $user_result->fetch_assoc();

        $product_sql = "SELECT name, price FROM products WHERE id = $product_id";
        $product_result = $conn->query($product_sql);
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delivered']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Update the status of the order to "Delivered" in the database
    $update_sql = "UPDATE orders SET status = 'delivered' WHERE id = $order_id";
    if ($conn->query($update_sql) === TRUE) {
        // Successfully updated the status, reload the page
        header("Location: admin.php");
        exit();
    } else {
        echo "Error updating order status: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <!-- <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="shop.css"> -->
    <link rel="stylesheet" href="admin.css"> 
</head>
<body>

<h2>User Ordered Details</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
        <th>Order Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php
    foreach ($admin_orders as $order) {
        echo '<tr>';
        echo '<td>' . $order['order_id'] . '</td>';
        echo '<td>' . $order['username'] . '</td>';
        echo '<td>' . $order['product_name'] . '</td>';
        echo '<td>' . $order['quantity'] . '</td>';
        echo '<td>$' . $order['price'] . '</td>';
        echo '<td>$' . number_format($order['total'], 2) . '</td>';
        echo '<td>' . $order['order_date'] . '</td>';
        echo '<td>' . ($order['status'] === 'delivered' ? 'Delivered' : 'Processing') . '</td>';
        echo '<td>';
        if ($order['status'] !== 'delivered') {
            echo '<form action="admin.php" method="POST">';
            echo '<input type="hidden" name="order_id" value="' . $order['order_id'] . '">';
            echo '<button type="submit" name="delivered">Delivered</button>';
            echo '</form>';
        }
        echo '</td>';
        echo '</tr>';
    }
    ?>
</table>

<!-- Pagination Links -->
<div class="pagination">
  <?php if ($page > 1): ?>
    <a href="admin.php?page=<?php echo $page - 1; ?>">Prev</a>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <?php if ($page === $i): ?>
      <span class="current-page"><?php echo $i; ?></span>
    <?php else: ?>
      <a href="admin.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endif; ?>
  <?php endfor; ?>

  <?php if ($page < $total_pages): ?>
    <a href="admin.php?page=<?php echo $page + 1; ?>">Next</a>
  <?php endif; ?>
</div>

<div id=logout-container >
    <a class=logout-container href="addproduct.php">Add Product</a>
    <a class=logout-container href="shop.php?logout=1">Logout</a>
</div>
</body>
</html>
