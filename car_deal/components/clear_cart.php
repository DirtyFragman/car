
<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
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

    $sql = "DELETE FROM garage WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to clear garage']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error clearing garage']);
}
