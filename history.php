<?php
session_start();

include 'connect.php';

$user_id = $_SESSION['user_id'];

$items_per_page = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $items_per_page;

try {
    $sql = "SELECT o.id, p.name as product_name, o.quantity, p.price, (o.quantity * p.price) as total, o.order_date, o.status, p.image_url
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.user_id = $user_id
            ORDER BY o.id DESC
            LIMIT $start_from, $items_per_page";

    $result = $conn->query($sql);
    $sql1 = "SELECT COUNT(*) as total_records FROM orders WHERE user_id = $user_id";
    $result1 = $conn->query($sql1);
    $row = $result1->fetch_assoc();
    $total_records = $row['total_records'];
    $total_pages = ceil($total_records / $items_per_page);
} catch (Exception $e) {
    echo "An error occurred while fetching order history.";

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <link rel="stylesheet" href="css/hist.css"> 
</head>
<body>
    <h1>Order History</h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Order Date</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?=$row['id']; ?></td>
                    <td><img src="upload/<?= $row['image_url']; ?>" alt="<?= $row['product_name']; ?>" height="50"></td>
                    <td><?= $row['product_name']; ?></td>
                    <td><?= $row['quantity']; ?></td>
                    <td>$<?= $row['price']; ?></td>
                    <td>$<?= number_format($row['total'], 2); ?></td>
                    <td><?= $row['order_date']; ?></td>
                    <td><?= ($row['status'] === 'delivered' ? 'Delivered' : 'Processing'); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        
        <div class="pagination">
            <?php
           
            ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="history.php?page=<?= $i; ?>"<?php if ($page == $i) echo " class='active'"; ?>><?= $i; ?></a>
            <?php endfor; ?>

        </div>
    <?php else: ?>
        <p>No order history available.</p>
    <?php endif; ?>
    <div class="back-container">
        <a href="shop.php">Back to shop</a>
    </div>
    <div class="logout-container">
        <a href="shop.php?logout=true">Logout</a>
    </div>

</body>
</html>
