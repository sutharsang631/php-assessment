<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$host = "localhost";
$username = "root";
$password = "**rock**";
$database = "king";

$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $back_to_shop = isset($_GET['back_to_shop']) ? $_GET['back_to_shop'] : 'shop.php';


    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
    } else {

        header("Location: shop.php");
        exit();
    }


    if (isset($_POST['add_to_cart'])) {
        $product_id = $_GET['product_id'];
        $quantity = $_POST['quantity'];

        if ($quantity < 1) {

            $quantity = 1;
        } elseif ($quantity > 10) {

            $quantity = 10;
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        } else {
            $sql_product = "SELECT * FROM products WHERE id = $product_id";
            $result_product = $conn->query($sql_product);

            if ($result_product->num_rows == 1) {
                $row_product = $result_product->fetch_assoc();
                $_SESSION['cart'][$product_id] = array(
                    'name' => $row_product['name'],
                    'price' => $row_product['price'],
                    'quantity' => $quantity
                );
            }
        }


        header("Location: pdp.php?product_id=$product_id&back_to_shop=" . urlencode($back_to_shop));
        exit();
    }
} else {
  
    header("Location: shop.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Details</title>
    <link rel="stylesheet" href="css/pdp1.css"> 
    
</head>
<body>


<div class="product-details">
    <h1><?php echo $product['name']; ?></h1>
    <img src="upload/<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
    <p><?php echo $product['description']; ?></p>
    <p>Price: $<?php echo $product['price']; ?></p>
    <form action="pdp.php?product_id=<?php echo $product['id']; ?>&back_to_shop=<?php echo urlencode($back_to_shop); ?>" method="POST">

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo isset($_SESSION['cart'][$product_id]['quantity']) ? $_SESSION['cart'][$product_id]['quantity'] : 1; ?>" min="1" max="10">


        <button type="submit" name="add_to_cart">Update Cart</button>
    </form>
    <a href="<?php echo $back_to_shop; ?>">Back to Shop</a>
</div>



    <div id="mini-cart">
        <h2>Mini Cart</h2>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
            </tr>
            <?php
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $item) {
                    echo '<tr>';
                    echo '<td>' . $item['name'] . '</td>';
                    echo '<td>' . $item['quantity'] . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="2">Your cart is empty</td></tr>';
            }

            ?>
        </table>
        <div id="anchor">
        <a href="cart.php">View Full Cart</a>   
        
        </div>
        
    </div>
</body>
</html>
