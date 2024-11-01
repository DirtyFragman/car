<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// For debugging database connection
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
// car.php - Displays detailed information about a specific car
session_start();
error_reporting(0);

$conn = new mysqli($servername, $username, $password, $dbname);

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$car_id = $conn->real_escape_string($_GET['id']);

// Get car details with brand information
$sql = "SELECT c.*, b.brand_name, b.logo_url as brand_logo 
        FROM cars c 
        JOIN car_brands b ON c.brand_id = b.brand_id 
        WHERE c.car_id = '$car_id'";
$result = $conn->query($sql);
$car = $result->fetch_assoc();

// Get car features
$features_sql = "SELECT f.feature_name 
                 FROM car_features cf 
                 JOIN features f ON cf.feature_id = f.feature_id 
                 WHERE cf.car_id = '$car_id'";
$features_result = $conn->query($features_sql);
$features = [];
while ($row = $features_result->fetch_assoc()) {
    $features[] = $row['feature_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model']); ?> - Elite Auto Garages</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <?php require '../components/navbar.php'; ?>

    <div class="car-detail-container">
        <div class="car-gallery">
            <img src="images/cars/<?php echo htmlspecialchars($car['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($car['model']); ?>" 
                 class="car-main-image">
        </div>
        
        <div class="car-info">
            <div class="car-header">
                <img src="images/brands/<?php echo htmlspecialchars($car['brand_logo']); ?>" 
                     alt="<?php echo htmlspecialchars($car['brand_name']); ?>" 
                     class="brand-logo-small">
                <h1><?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model']); ?></h1>
            </div>
            
            <div class="car-specs">
                <p class="price">Price: $<?php echo number_format($car['price'], 2); ?></p>
                <p>Year: <?php echo htmlspecialchars($car['year']); ?></p>
                <p>Engine: <?php echo htmlspecialchars($car['engine']); ?></p>
                <p>Transmission: <?php echo htmlspecialchars($car['transmission']); ?></p>
                <p>Color: <?php echo htmlspecialchars($car['color']); ?></p>
            </div>
            
            <div class="car-features">
                <h2>Features</h2>
                <ul>
                    <?php foreach ($features as $feature): ?>
                        <li><?php echo htmlspecialchars($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="car-description">
                <h2>Description</h2>
                <p><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
            </div>
            
            <button class="addtocart" 
                    data-product="<?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model']); ?>"
                    data-price="<?php echo $car['price']; ?>">
                Add to Garage
            </button>
        </div>
    </div>
</body>
</html>