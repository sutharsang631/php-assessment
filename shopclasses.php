
<?php
class DatabaseConnect
{
    private $host = "localhost";
    private $username = "root";
    private $password = "**rock**";
    private $database = "king";
    protected $conn;

    public function __construct()
    {

        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

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

        if ($category_filter !== '') {
            $category_filter = $this->conn->real_escape_string($category_filter);
        }

        if ($search_query !== '') {
            $search_query = $this->conn->real_escape_string($search_query);
        }

        if ($min_price !== '') {
            $min_price = $this->conn->real_escape_string($min_price);
        }
        if ($max_price !== '') {
            $max_price = $this->conn->real_escape_string($max_price);
        }

        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['apply_filter'])) {
 
            if ($category_filter !== '' && $min_price !== '' && $max_price !== '') {
                $sql = "SELECT * FROM products WHERE category = '$category_filter' AND price BETWEEN $min_price AND $max_price";
            } else {

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

            $sql = "SELECT * FROM products";
        }

        if ($category_filter === '' && $min_price !== '' && $max_price !== '') {
            $sql .= ($search_query === '') ? " WHERE " : " AND ";
            $sql .= "price BETWEEN $min_price AND $max_price";
        }

        $sql_count = "SELECT COUNT(id) AS total FROM ($sql) AS total_products";
        $result_count = $this->conn->query($sql_count);
        $row_count = $result_count->fetch_assoc();
        $total_products = $row_count['total'];
        $total_pages = ceil($total_products / $this->items_per_page);

        $sql .= " LIMIT $start_from, $this->items_per_page";

        $result = $this->conn->query($sql);

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

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
            $product_id = $_POST['product_id'];

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