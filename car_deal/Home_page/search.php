<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_dealership";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

$searchTerm = $_GET['term'] ?? '';

if (strlen($searchTerm) < 2) {
    echo json_encode([]);
    exit;
}

$searchTerm = $conn->real_escape_string($searchTerm);

$sql = "SELECT c.*, cb.brand_name 
        FROM cars c 
        JOIN car_brands cb ON c.brand_id = cb.brand_id 
        WHERE cb.brand_name LIKE '%$searchTerm%' 
        OR c.model LIKE '%$searchTerm%' 
        OR c.year LIKE '%$searchTerm%' 
        OR c.fuel_type LIKE '%$searchTerm%'
        ORDER BY c.price DESC 
        LIMIT 5";

$result = $conn->query($sql);

$cars = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

echo json_encode($cars);

$conn->close();
?>