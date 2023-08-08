<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: shop.php");
    exit();
}

include 'connect.php';

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
        <button onclick="redirectToSignup()">Sign Up</button>
    </div>
    <?php
    if (isset($error_message)) {
        echo '<p style="color: red; text-align:center;">' . $error_message . '</p>';
    }
    ?>
    <div id="loginForm">
        <h3>Login</h3>
        <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
    </div>
    <script>
        function toggleForm(formId) {
            var loginForm = document.getElementById('loginForm');

            if (formId === 'loginForm') {
                loginForm.style.display = 'block';
                signupForm.style.display = 'none';
            }
        }

        function redirectToSignup() {
            window.location.href = "signup.php";
        }
    </script>
</body>
</html>
