<?php
// login.php

session_start();

if (isset($_SESSION['user_id'])) {
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

// Insert admin credentials (Use this command only once to add the admin credentials)
// $sql_insert_admin = "INSERT INTO users (username, password, is_admin) VALUES ('admin', 'admin@123', 1)";
// $conn->query($sql_insert_admin);

// User login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, is_admin FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        if ($username === 'admin' && $password === 'admin@123') {
            $_SESSION['is_admin'] = true;
            header("Location: admin.php");
            exit();
        } else {
            header("Location: shop.php");
            exit();
        }
    } else {
        $error_message = "Invalid username or password";
    }
}

// New user sign-up
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if ($conn->query($sql) === TRUE) {
        $success_message = "User registration successful. Please log in.";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<!-- <link rel="stylesheet" href="style.css"> -->
<link rel="stylesheet" href="login.css">
<body>
    <h1>Login</h1>
    <?php
    if (isset($error_message)) {
        echo '<p style="color: red;">' . $error_message . '</p>';
    }
    if (isset($success_message)) {
        echo '<p style="color: green;">' . $success_message . '</p>';
    }
    ?>
     <div>
        <button onclick="toggleForm('loginForm')">Login</button>
        <button onclick="toggleForm('signupForm')">Sign Up</button>
    </div>
    <div id="loginForm">
        <h2>Login</h2>
        <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
    </div>
    <div id="signupForm" style="display:none;">
        <h2>Sign Up</h2>
    <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" name="signup" value="Sign Up">
    </form>
    </div>

   

    <script>
        function toggleForm(formId) {
            var loginForm = document.getElementById('loginForm');
            var signupForm = document.getElementById('signupForm');

            if (formId === 'loginForm') {
                loginForm.style.display = 'block';
                signupForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                signupForm.style.display = 'block';
            }
        }
    </script>
</body>
</html>
