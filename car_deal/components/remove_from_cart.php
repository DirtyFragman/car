
<?php
session_start();
// Adjust path as needed

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (!isset($_POST['garage_id'])) {
    echo json_encode(['error' => 'No garage item specified']);
    exit;
}

try {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "car_dealership";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userId = $_SESSION['user_id'];
    $garageId = $_POST['garage_id'];

    $sql = "DELETE FROM garage WHERE garage_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $garageId, $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to remove car from garage']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error removing car from garage']);
}
