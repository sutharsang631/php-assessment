<?php
class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getProducts($category_filter, $search_query, $min_price, $max_price, $start_from, $items_per_page) {
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

        if ($category_filter === '' && $min_price !== '' && $max_price !== '') {
            $sql .= ($search_query === '') ? " WHERE " : " AND ";
            $sql .= "price BETWEEN $min_price AND $max_price";
        }

        $sql .= " LIMIT $start_from, $items_per_page";

        $result = $this->conn->query($sql);
        return $result;
    }

    public function getProductCategories() {
        $sql_categories = "SELECT DISTINCT category FROM products";
        $result_categories = $this->conn->query($sql_categories);
        $categories = array();

        if ($result_categories->num_rows > 0) {
            while ($row = $result_categories->fetch_assoc()) {
                $categories[] = $row['category'];
            }
        }

        return $categories;
    }
}
?>
