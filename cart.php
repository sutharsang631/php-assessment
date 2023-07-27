<?php
session_start();

include 'connect.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
if (isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_cart'])) {
        $cart = $_SESSION['cart'];

        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if ($quantity == 0) {
                unset($cart[$product_id]);
            } else {
                $cart[$product_id]['quantity'] = $quantity;
            }
        }

        $_SESSION['cart'] = $cart;

    } elseif (isset($_POST['place_order'])) {
        $user_id = $_SESSION['user_id'];
        $cart = $_SESSION['cart'];

        foreach ($cart as $product_id => $item) {
            $quantity = $item['quantity'];
            $order_date = date('Y-m-d H:i:s');

            $sql = "INSERT INTO orders (user_id, product_id, quantity, order_date) VALUES ($user_id, $product_id, $quantity, '$order_date')";
            $conn->query($sql);
        }

 
        $_SESSION['cart'] = array();
    }
}


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
        <?php
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

        if (count($cart) > 0) {
            $total_amount = 0;

            foreach ($cart as $product_id => $item) {
                $sql = "SELECT * FROM products WHERE id = $product_id";
                $result = $conn->query($sql);

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $total = $row['price'] * $item['quantity'];
                    $total_amount += $total;

                    echo '<tr>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>';
                    echo '<form action="cart.php" method="POST">';
                    echo '<input type="hidden" name="product_id" value="' . $product_id . '">';
                    echo '<input type="number" name="quantity[' . $product_id . ']" value="' . $item['quantity'] . '" min="0" max="10">';
                    echo '<button type="submit" name="update_cart">Update</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '<td>$' . $row['price'] . '</td>';
                    echo '<td>$' . number_format($total, 2) . '</td>';
                    echo '<td class="logout-container"  ><a href="cart.php?remove=' . $product_id . '">Remove</a></td>';
                    echo '</tr>';
                }
            }

            echo '<tr>';
            echo '<td colspan="3"><b>Total</b></td>';
            echo '<td colspan="2"><b>$' . number_format($total_amount, 2) . '</b></td>';
            echo '</tr>';
        } else {
            echo '<tr><td colspan="5">Your cart is empty</td></tr>';
        }

        if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
            unset($_SESSION['cart'][$_GET['remove']]);
            header("Location: cart.php");
            exit();
        }

        ?>
 
    </table>
    </div>
    <?php if (count($cart) > 0) : ?>
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
