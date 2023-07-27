<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

$user_id = $_SESSION['user_id'];

// Pagination
$items_per_page = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $items_per_page;

// Fetch user's orders and their status with pagination
$sql = "SELECT o.id, p.name as product_name, o.quantity, p.price, (o.quantity * p.price) as total, o.order_date, o.status, p.image_url
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.user_id = $user_id
        LIMIT $start_from, $items_per_page";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <link rel="stylesheet" href="hist.css"> 
</head>
<body>
    <h1>Order History</h1>
    <?php
    if ($result->num_rows > 0) {
        echo '<table>';
        echo '<tr>
                <th>Order ID</th>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Order Date</th>
                <th>Status</th>
                
            </tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td><img src="upload/' . $row['image_url'] . '" alt="' . $row['product_name'] . '" height="50"></td>';
            echo '<td>' . $row['product_name'] . '</td>';
            echo '<td>' . $row['quantity'] . '</td>';
            echo '<td>$' . $row['price'] . '</td>';
            echo '<td>$' . number_format($row['total'], 2) . '</td>';
            echo '<td>' . $row['order_date'] . '</td>';
            echo '<td>' . ($row['status'] === 'delivered' ? 'Delivered' : 'Processing') . '</td>';
            
            echo '</tr>';
        }
        echo '</table>';

        // Pagination Links
        $count_sql = "SELECT COUNT(id) AS total_orders FROM orders WHERE user_id = $user_id";
        $count_result = $conn->query($count_sql);
        $row = $count_result->fetch_assoc();
        $total_orders = $row['total_orders'];

        $total_pages = ceil($total_orders / $items_per_page);

        echo '<div class="pagination">';
        if ($page > 1) {
            echo '<a href="history.php?page=' . ($page - 1) . '">Prev</a>';
        }
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($page === $i) {
                echo '<span class="current-page">' . $i . '</span>';
            } else {
                echo '<a href="history.php?page=' . $i . '">' . $i . '</a>';
            }
        }
        if ($page < $total_pages) {
            echo '<a href="history.php?page=' . ($page + 1) . '">Next</a>';
        }
        echo '</div>';
    } else {
        echo '<p>No order history available.</p>';
    }
    ?>
    <div class="back-container">
        <a href="shop.php">Back to Cart</a>
    </div>
    <div class="logout-container">
        <a href="shop.php?logout=true">Logout</a>
    </div>
</body>
</html>
