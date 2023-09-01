<?php
session_start();
require_once "ShopC.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$shop = new ShopC();


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}


$items_per_page = 4;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';


$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';


$filterData = $shop->applyFilters($page, $category_filter, $search_query, $min_price, $max_price);

$total_pages = $filterData['total_pages'];
$total_products = $filterData['total_products'];
$result = $filterData['result'];
// print_r($result);
$categories = $filterData['categories'];


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $shop->addToCart($_POST['product_id']);
    $redirectUrl = "shop.php?page=" . urlencode($page) . "&search=" . urlencode($search_query) . "&apply_filter=1";

    if (!empty($category_filter)) {
        $redirectUrl .= "&category=" . urlencode($category_filter);
    }
    if (!empty($min_price)) {
        $redirectUrl .= "&min_price=" . urlencode($min_price);
    }
    if (!empty($max_price)) {
        $redirectUrl .= "&max_price=" . urlencode($max_price);
    }

    header("Location: " . $redirectUrl);
    exit;
}



?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/shop1.css"> 
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
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-item">
            <img src="upload/<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>">
            <h2><?php echo $row['name']; ?></h2>
            <p><?php echo $row['description']; ?></p>
            <p>Price: $<?php echo $row['price']; ?></p>
            <div class="product-item-button">
                <a href="pdp.php?product_id=<?php echo $row['id']; ?>&back_to_shop=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">View Details</a>
            </div>
            <form action="shop.php?page=<?php echo urlencode($page); ?>&category=<?php echo urlencode($category_filter); ?>&search=<?php echo urlencode($search_query); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>&apply_filter=1" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

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




<div id="logout-container">
    <a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    <a href='history.php'>History</a>
    <a  href="shop.php?logout=1">Logout</a>
</div>
<?php echo "<br>"." $total_products " ."  products found"?>

<?php
        include 'mini.html';
        ?>
        
</body>
</html>




