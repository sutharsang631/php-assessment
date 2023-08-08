<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: shop.php");
    exit();
}

include 'connect.php';


function is_valid_password($password)
{
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $password);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!is_valid_password($password)) {
        $error_message = "Password must be at least 6 characters long and include at least one uppercase letter, one lowercase letter, and one digit. Example: Example@123";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            $success_message = "User registration successful. Please log in.";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>shopping Cart</title>
</head>
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="css/style.css">
<body>
    <h1>shopping Cart</h1>
   
     <div class="centerbt">
        <button onclick="toggleForm('loginForm')">Login</button>
        <button onclick="toggleForm('signupForm')">Sign Up</button>
    </div>
    <?php
    if (isset($error_message)) {
        echo '<p style="color: red; text-align:center;">' . $error_message . '</p>';
    }
    if (isset($success_message)) {
        echo '<p style="color: green;text-align:center;">' . $success_message . '</p>';
    }
    ?>
    <div id="loginForm" style="display:none;">
        <h3>Login</h3>
        <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
    </div>
    <div id="signupForm">
        <h3>Sign Up</h3>
    <form action="signup.php" method="POST">
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
