<?php
// User.php

class User
{
    public static function login($username, $password)
    {
        global $conn;

        $sql = "SELECT id, is_admin FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $is_admin);
            $stmt->fetch();
            $_SESSION['user_id'] = $user_id;

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

    public static function register($username, $password)
    {
        global $conn;

        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $success_message = "User registration successful. Please log in.";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }

    public static function logout()
    {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    public static function checkUserLoggedIn()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
    }
}
