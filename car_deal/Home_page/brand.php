<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Define database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_dealership";

// Establish database connection with error handling
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$brand_id = $conn->real_escape_string($_GET['id']);

// Get brand details
$brand_sql = "SELECT * FROM car_brands WHERE brand_id = '$brand_id'";
$brand_result = $conn->query($brand_sql);
$brand = $brand_result->fetch_assoc();

// Get all cars for this brand
$cars_sql = "SELECT * FROM cars WHERE brand_id = '$brand_id'";
$cars_result = $conn->query($cars_sql);
$cars = [];
while ($row = $cars_result->fetch_assoc()) {
    $cars[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($brand['brand_name']); ?> - Elite Auto Garages</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <?php require '../components/navbar.php'; ?>

    <div class="brand-header">
        <img src="images/brands/<?php echo htmlspecialchars($brand['logo_url']); ?>" 
             alt="<?php echo htmlspecialchars($brand['brand_name']); ?>" 
             class="brand-logo-large">
        <h1><?php echo htmlspecialchars($brand['brand_name']); ?> Models</h1>
    </div>

    <section class="cars-grid">
        <?php foreach ($cars as $car): ?>
        <div class="car-card">
            <img src="images/cars/<?php echo htmlspecialchars($car['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($car['model']); ?>" 
                 class="car-image">
            <div class="car-details">
                <h3><?php echo htmlspecialchars($car['model']); ?></h3>
                <p>Year: <?php echo htmlspecialchars($car['year']); ?></p>
                <p>Price: $<?php echo number_format($car['price'], 2); ?></p>
                <a href="car.php?id=<?php echo $car['car_id']; ?>" class="view-details">View Details</a>
                <button class="addtocart" 
                        data-product="<?php echo htmlspecialchars($brand['brand_name'] . ' ' . $car['model']); ?>"
                        data-price="<?php echo $car['price']; ?>">
                    Add to Garage
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </section>
</body>
</html>
