<?php
// shop.php

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

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Pagination
$items_per_page = 4;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $items_per_page;

// Handle product category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Handle search query
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Handle price filter
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

// Form submitted to apply filters
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['apply_filter'])) {
    // Apply both category and price filters when both are set
    if ($category_filter !== '' && $min_price !== '' && $max_price !== '') {
        $sql = "SELECT * FROM products WHERE category = '$category_filter' AND price BETWEEN $min_price AND $max_price";
    } else {
        // Apply category and/or search filters
        $sql = "SELECT * FROM products";

        $sql_filters = array();

        if ($category_filter !== '') {
            $sql_filters[] = "category = '$category_filter'";
        }

        if ($search_query !== '') {
            $sql_filters[] = "name LIKE '%$search_query%'";
        }

        if (!empty($sql_filters)) {
            $sql .= " WHERE " . implode(" AND ", $sql_filters);
        }
    }
} else {
    // Fetch all products without filters
    $sql = "SELECT * FROM products";
}

// Add price filter to the query when category is not set
if ($category_filter === '' && $min_price !== '' && $max_price !== '') {
    $sql .= ($search_query === '') ? " WHERE " : " AND ";
    $sql .= "price BETWEEN $min_price AND $max_price";
}

// Add pagination to the query
$sql_count = "SELECT COUNT(id) AS total FROM ($sql) AS total_products";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_products = $row_count['total'];
$total_pages = ceil($total_products / $items_per_page);

$sql .= " LIMIT $start_from, $items_per_page";

$result = $conn->query($sql);

// Fetch product categories for filter dropdown
$sql_categories = "SELECT DISTINCT category FROM products";
$result_categories = $conn->query($sql_categories);
$categories = array();

if ($result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Add product to cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // If the product is already in the cart, increase the quantity, otherwise add it to the cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity']++;
    } else {
        $sql_product = "SELECT * FROM products WHERE id = $product_id";
        $result_product = $conn->query($sql_product);

        if ($result_product->num_rows == 1) {
            $row_product = $result_product->fetch_assoc();
            $_SESSION['cart'][$product_id] = array(
                'name' => $row_product['name'],
                'price' => $row_product['price'],
                'quantity' => 1
            );
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <!-- <link rel="stylesheet" href="shop.css"> -->
    <link rel="stylesheet" href="shop1.css"> 

</head>
<body>
    <h1>Products</h1>

    <div class=cov>
        <form action="shop.php" method="GET">
            <label for="search">Search Product:</label>
            <input type="text" name="search" id="search" value="<?php echo $search_query; ?>">
            <button type="submit" name="apply_filter">Search</button>
        </form>
        <form action="shop.php" method="GET">
            <label for="category">Filter by Category:</label>
            <select id="category" name="category">
                <option value="">All</option>
                <?php
                foreach ($categories as $category) {
                    echo '<option value="' . $category . '"';
                    if ($category_filter === $category) {
                        echo ' selected';
                    }
                    echo '>' . ucfirst($category) . '</option>';
                }
                ?>
            </select>
            <label for="min_price">Min Price:</label>
            <input type="text" name="min_price" value="<?php echo $min_price; ?>" placeholder="Min Price">
            <label for="max_price">Max Price:</label>
            <input type="text" name="max_price" value="<?php echo $max_price; ?>" placeholder="Max Price">
            <button type="submit" name="apply_filter">Apply Filter</button>
        </form>
    </div>

    <div id="products-container">
        <?php
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product-item">';
            echo '<img src="' . $row['image_url'] . '" alt="' . $row['name'] . '">';
            echo '<h2>' . $row['name'] . '</h2>';
            echo '<p>' . $row['description'] . '</p>';
            echo '<p>Price: $' . $row['price'] . '</p>';
            echo '<form action="shop.php" method="POST">';
            echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
            echo '<button type="submit">Add to Cart</button>';
            echo '</form>';
            echo '</div>';
        }
        ?>

 
    </div>
    <div id="mini-cart">
    <h2>Mini Cart</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <!-- <th>Action</th> -->
        </tr>
        <?php
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $item) {
                echo '<tr>';
                echo '<td>' . $item['name'] . '</td>';
                echo '<td>' . $item['quantity'] . '</td>';
                // echo '<td><a href="shop.php?remove=' . $product_id . '">Remove</a></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">Your cart is empty</td></tr>';
        }

        // Remove product from cart
        if (isset($_GET['remove']) && isset($cart[$_GET['remove']])) {
            unset($cart[$_GET['remove']]);
            $_SESSION['cart'] = $cart;
            header("Location: cart.php");
            exit();
        }
        ?>
    </table>
    <p><a href="cart.php">View Full Cart</a></p>
</div>

    <div id="logout-container">
        <a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
        <a  href="shop.php?logout=1">Logout</a>
    </div>
    <?php echo "<br>"." $total_products " ."  products found"?>
    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($total_pages > 1): ?>
            <?php if ($page === 1): ?>
                <span class="disabled-link">Prev</span>
            <?php else: ?>
                <a href="shop.php?page=<?php echo $page - 1; ?>&category=<?php echo $category_filter; ?>&search=<?php echo $search_query; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&apply_filter=1">Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="current-page"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="shop.php?page=<?php echo $i; ?>&category=<?php echo $category_filter; ?>&search=<?php echo $search_query; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&apply_filter=1"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page == $total_pages): ?>
                <span class="disabled-link">Next</span>
            <?php else: ?>
                <a href="shop.php?page=<?php echo $page + 1; ?>&category=<?php echo $category_filter; ?>&search=<?php echo $search_query; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&apply_filter=1">Next</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>
