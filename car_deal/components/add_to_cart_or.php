// 5. add_to_cart.php
<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_dealership";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$car_id = $_POST['car_id'];
$user_id = $_SESSION['user_id'];

// Check if car is already in garage
$check_sql = "SELECT * FROM garage WHERE user_id = ? AND car_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $car_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['error' => 'Car is already in your garage']);
    exit;
}

// Add car to garage
$sql = "INSERT INTO garage (user_id, car_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $car_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to add car to garage']);
}
?>