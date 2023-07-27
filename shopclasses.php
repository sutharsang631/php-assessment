
<?php
class DatabaseConnect
{
    // Database connection properties
    private $host = "localhost";
    private $username = "root";
    private $password = "Seetha@123";
    private $database = "king";
    protected $conn;

    public function __construct()
    {
        // Create connection
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
}

class Shop extends DatabaseConnect
{
    private $items_per_page = 4;

    public function applyFilters(
        $page = 1,
        $category_filter = '',
        $search_query = '',
        $min_price = '',
        $max_price = ''
    ) {
        $start_from = ($page - 1) * $this->items_per_page;

        // product category filter
        if ($category_filter !== '') {
            $category_filter = $this->conn->real_escape_string($category_filter);
        }

        // search query
        if ($search_query !== '') {
            $search_query = $this->conn->real_escape_string($search_query);
        }

        // price filter
        if ($min_price !== '') {
            $min_price = $this->conn->real_escape_string($min_price);
        }
        if ($max_price !== '') {
            $max_price = $this->conn->real_escape_string($max_price);
        }

        // Form submitted to apply filters
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['apply_filter'])) {
            //here only we  Apply both category and price filters when both are set
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
            // Fetch all products without filters this is default
            $sql = "SELECT * FROM products";
        }

        // Add price filter to the query when category is not set
        if ($category_filter === '' && $min_price !== '' && $max_price !== '') {
            $sql .= ($search_query === '') ? " WHERE " : " AND ";
            $sql .= "price BETWEEN $min_price AND $max_price";
        }

        // Add pagination to the query
        $sql_count = "SELECT COUNT(id) AS total FROM ($sql) AS total_products";
        $result_count = $this->conn->query($sql_count);
        $row_count = $result_count->fetch_assoc();
        $total_products = $row_count['total'];
        $total_pages = ceil($total_products / $this->items_per_page);

        $sql .= " LIMIT $start_from, $this->items_per_page";

        $result = $this->conn->query($sql);

        // Fetch product categories for filter dropdown
        $sql_categories = "SELECT DISTINCT category FROM products";
        $result_categories = $this->conn->query($sql_categories);
        $categories = array();

        if ($result_categories->num_rows > 0) {
            while ($row = $result_categories->fetch_assoc()) {
                $categories[] = $row['category'];
            }
        }

        return array(
            'total_pages' => $total_pages,
            'total_products' => $total_products,
            'result' => $result,
            'categories' => $categories
        );
    }

    public function addToCart($product_id)
    {
        // Add product to cart
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
            $product_id = $_POST['product_id'];

            // If the product is already in the cart, increase the quantity, otherwise add it to the cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity']++;
            } else {
                $sql_product = "SELECT * FROM products WHERE id = $product_id";
                $result_product = $this->conn->query($sql_product);

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
    }
}
?>