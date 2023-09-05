<?php

require_once 'AdminPage.php';

$host = "localhost";
$username = "root";
$password = "Seetha@123";
$database = "king";

$adminPage = new AdminPage($host, $username, $password, $database);
$adminPage->checkAdminSession();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$admin_orders_info = $adminPage->getAdminOrders($page);
$admin_orders = $admin_orders_info['admin_orders'];
$total_pages = $admin_orders_info['total_pages'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delivered']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    if ($adminPage->updateOrderStatus($order_id)) {
        header("Location: admin.php?page=$page");
        exit();
    } else {
        echo "Error updating order status.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/admin1.css">
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
    <?php foreach ($admin_orders as $order): ?>
      <tr>
      <td><?= $order['order_id']; ?></td>
      <td><?= $order['username']; ?></td>
      <td><?= $order['product_name']; ?></td>
      <td><?= $order['quantity']; ?></td>
      <td>$<?= $order['price']; ?></td>
      <td>$<?= number_format($order['total'], 2); ?></td>
      <td><?= $order['order_date']; ?></td>
      <td><?= ($order['status'] === 'delivered') ? 'Delivered' : 'Processing'; ?></td>
      <td>
          <?php if ($order['status'] !== 'delivered'): ?>
              <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="POST">
                  <input type="hidden" name="order_id" value="<?= $order['order_id']; ?>">
                  <button type="submit" name="delivered">Delivered</button>
              </form>
          <?php endif; ?>
      </td>
  </tr>

    <?php endforeach; ?>
</table>

<div class="pagination">
  <?php if ($page > 1): ?>
    <a href="admin.php?page=<?= $page - 1; ?>">Prev</a>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <?php if ($page === $i): ?>
      <span class="current-page"><?= $i; ?></span>
    <?php else: ?>
      <a href="admin.php?page=<?= $i; ?>"><?= $i; ?></a>
    <?php endif; ?>
  <?php endfor; ?>

  <?php if ($page < $total_pages): ?>
    <a href="admin.php?page=<?= $page + 1; ?>">Next</a>
  <?php endif; ?>
</div>

<div id=logout-container >
    <a class=logout-container href="addproduct.php">Add Product</a>
    <a class=logout-container href="shop.php?logout=1">Logout</a>
</div>

</body>
</html>

<?php
$adminPage->closeConnection();
?>
