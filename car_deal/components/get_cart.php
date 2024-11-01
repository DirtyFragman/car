<?php
session_start();




if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in");
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
    error_log("Processing request for user ID: " . $userId);

    $sql = "SELECT g.garage_id, c.car_id, b.brand_name, c.model, c.price 
            FROM garage g
            JOIN cars c ON g.car_id = c.car_id
            JOIN car_brands b ON c.brand_id = b.brand_id
            WHERE g.user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Add error logging here
    error_log("Cart query result: " . print_r($result, true));

    $cartItems = [];
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }

    // Log the final cart items array
    error_log("Cart items: " . print_r($cartItems, true));
    header('Content-Type: application/json');
    echo json_encode($cartItems);
} catch (Exception $e) {
    error_log("Error in get_cart.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error fetching garage items']);
}
?>