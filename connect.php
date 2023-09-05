<?php
$servername = "localhost";
$username = "root";
$password = "Seetha@123";
$dbname = "king";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
