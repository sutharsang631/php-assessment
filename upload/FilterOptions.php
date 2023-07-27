<?php
// FilterOptions.php

class FilterOptions {
    private $categories = array();
    private $min_price;
    private $max_price;
    private $search_query;

    public function __construct($categories, $min_price, $max_price, $search_query) {
        $this->categories = $categories;
        $this->min_price = $min_price;
        $this->max_price = $max_price;
        $this->search_query = $search_query;
    }

    public function getCategories() {
        return $this->categories;
    }

    public function getMinPrice() {
        return $this->min_price;
    }

    public function getMaxPrice() {
        return $this->max_price;
    }

    public function getSearchQuery() {
        return $this->search_query;
    }
}
?>
