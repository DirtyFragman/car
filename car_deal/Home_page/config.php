<?php
// config.php
$servername = "localhost";
$username = "root";  // your XAMPP MySQL username
$password = "";      // your XAMPP MySQL password (blank by default)
$dbname = "car_dealership";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
?>