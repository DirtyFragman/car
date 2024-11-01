<?php
// save_to_cart.php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_dealership";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $car_id = $_POST['car_id'];
    
    // Check if car already exists in user's garage
    $check_sql = "SELECT * FROM garage WHERE user_id = ? AND car_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $car_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Car already in garage']);
    } else {
        // Add new car to garage
        $insert_sql = "INSERT INTO garage (user_id, car_id, added_at) VALUES (?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $car_id);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Car added to garage']);
        } else {
            echo json_encode(['error' => 'Failed to add car to garage']);
        }
    }
}
$conn->close();
?>



