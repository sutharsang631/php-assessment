
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
$items_per_page = 3;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$start_from = ($page - 1) * $items_per_page;


$sql_count = "SELECT COUNT(id) AS total FROM products";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_products = $row_count['total'];
$total_pages = ceil($total_products / $items_per_page);

// Limit the page number within the valid range
$page = max(1, min($page, $total_pages));

// Fetch products based on pagination
if ($category_filter !== '') {
    $sql = "SELECT * FROM products WHERE category = '$category_filter' LIMIT $start_from, $items_per_page";
} else {
    $sql = "SELECT * FROM products LIMIT $start_from, $items_per_page";
}

$result = $conn->query($sql);
// Handle product category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if ($category_filter !== '') {
    $sql = "SELECT * FROM products WHERE category = '$category_filter' LIMIT $start_from, $items_per_page";
} else {
    $sql = "SELECT * FROM products LIMIT $start_from, $items_per_page";
}

$result = $conn->query($sql);
// Add product to cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // If the product is already in the cart, increase the quantity, otherwise add it to the cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity']++;
    } else {
        $_SESSION['cart'][$product_id]['quantity'] = 1;
    }
}
$sql_categories = "SELECT DISTINCT category FROM products";
$result_categories = $conn->query($sql_categories);
$categories = array();

if ($result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="shop.css">
</head>
<body>
    <h1>Products</h1>

    <div>
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
            <button type="submit">Apply Filter</button>
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
        
   
    
    <div id="logout-container">
        <a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>
    
        <div id="logout-container">
        <a href="shop.php?logout=1">Logout</a>
    </div>


    <!-- Pagination Links -->
    <div class="pagination">
  <?php if ($total_pages > 1): ?>
    <?php if ($page === 1): ?>
      <span class="disabled-link">Prev</span>
    <?php else: ?>
      <a href="shop.php?page=<?php echo $page - 1; ?>">Prev</a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <?php if ($i === $page): ?>
        <span class="current-page"><?php echo $i; ?></span>
      <?php else: ?>
        <a href="shop.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
      <?php endif; ?>
    <?php endfor; ?>
 
    
    <?php if ($page == $total_pages): ?>
      <span class="disabled-link">Next</span>
    <?php else: ?>
      <a href="shop.php?page=<?php echo $page+1; ?>">Next</a>
    <?php endif; ?>
  <?php endif; ?>
</div>

</body>
</html>
