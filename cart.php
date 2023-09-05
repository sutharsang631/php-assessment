<?php
session_start();

include 'connect.php';
require_once 'CartClass.php'; // Include the cartclass.php file

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    $cartClass = new CartClass($conn); // Create an instance of the CartClass

    if (isset($_POST['update_cart'])) {
        $cartClass->updateCart($cart, $_POST);
        $_SESSION['cart'] = $cart;
    } elseif (isset($_POST['place_order'])) {
        $user_id = $_SESSION['user_id'];

        $cartClass->placeOrder($user_id, $cart);
    } elseif (isset($_POST['remove'])) {
        $product_id_to_remove = $_POST['remove_product_id'];
        $cartClass->removeProduct($product_id_to_remove);
    }
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_amount = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/cart3.css">
</head>
<body>
<h1>Cart</h1>
<div id="cart-container">
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php foreach ($cart as $product_id => $item): ?>
            <?php
            $sql = "SELECT * FROM products WHERE id = $product_id";
            $result = $conn->query($sql);

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $total = $row['price'] * $item['quantity'];
                $total_amount += $total;
            }
            ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td>
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="number" name="quantity[<?php echo $product_id; ?>]" value="<?php echo $item['quantity']; ?>" min="0" max="10">
                        <button type="submit" name="update_cart">Update</button>
                    </form>
                </td>

                <td>$<?php echo $row['price']; ?></td>
                <td>$<?php echo number_format($total, 2); ?></td>
                <td class="logout-container">
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="remove_product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" name="remove">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><b>Total</b></td>
            <td colspan="2"><b>$<?= number_format($total_amount, 2) ?></b></td>
        </tr>
    </table>
</div>
<?php if (count($cart) > 0): ?>
    <form action="cart.php" method="POST" class="center-button">
        <button id="btn" type="submit" name="place_order">Place Order</button>
    </form>
<?php endif; ?>
<div id="logout-container">
    <a href="shop.php">Continue Shopping</a>
    <a href="cart.php?logout=1">Logout</a>
</div>
</body>
</html>