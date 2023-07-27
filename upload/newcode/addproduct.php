<?php
// addproduct.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: shop.php");
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

// Add new product
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    // Upload the image file
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_url = $_FILES['image']['name'];
        $upload_directory = 'images/';
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_directory . $image_url);
    }

    // Prepare and execute the SQL query to insert the new product into the database
    $sql = "INSERT INTO products (name, description, category, price, image_url) VALUES ('$name', '$description', '$category', $price, '$image_url')";
    if ($conn->query($sql) === TRUE) {
        // Product added successfully, redirect back to admin page
        header("Location: admin.php");
        exit();
    } else {
        $error_message = "Error adding product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Add New Product</h2>
<form action="addproduct.php" method="POST" enctype="multipart/form-data">
    <label>Name:</label>
    <input type="text" name="name" required><br>
    <label>Description:</label>
    <textarea name="description" required></textarea><br>
    <label>Category:</label>
    <input type="text" name="category" required><br>
    <label>Price:</label>
    <input type="number" name="price" step="0.01" required><br>
    <label>Image:</label>
    <input type="file" name="image" accept="image/*" required><br>
    <button type="submit" name="add_product">Add Product</button>
</form>

<div>
    <a href="admin.php">Back to Admin Panel</a>
    <a href="shop.php?logout=1">Logout</a>
</div>
</body>
</html>
