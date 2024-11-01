<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: sign.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_dealership";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user's saved cars
$user_id = $_SESSION['user_id'];
$saved_cars_sql = "SELECT c.*, b.brand_name 
                   FROM saved_cars sc
                   JOIN cars c ON sc.car_id = c.car_id
                   JOIN car_brands b ON c.brand_id = b.brand_id
                   WHERE sc.user_id = '$user_id'";
$saved_cars_result = $conn->query($saved_cars_sql);

// Get user's purchase history
$purchases_sql = "SELECT p.*, c.model, c.image_url, b.brand_name 
                 FROM purchases p
                 JOIN cars c ON p.car_id = c.car_id
                 JOIN car_brands b ON c.brand_id = b.brand_id
                 WHERE p.user_id = '$user_id'
                 ORDER BY p.purchase_date DESC";
$purchases_result = $conn->query($purchases_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Garage - Elite Auto Garages</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>

<body>
    <?php require '../components/navbar.php'; ?>

    <div class="garage-container">
        <h1>My Garage</h1>

        <section class="saved-cars">
            <h2>Saved Cars</h2>
            <div class="cars-grid">
                <?php while ($car = $saved_cars_result->fetch_assoc()): ?>
                    <div class="car-card">
                        <img src="images/cars/<?php echo htmlspecialchars($car['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($car['model']); ?>"
                            class="car-image">
                        <div class="car-details">
                            <h3><?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model']); ?></h3>
                            <p>Year: <?php echo htmlspecialchars($car['year']); ?></p>
                            <p>Price: $<?php echo number_format($car['price'], 2); ?></p>
                            <a href="car.php?id=<?php echo $car['car_id']; ?>" class="view-details">View Details</a>
                            <button class="remove-saved" data-car-id="<?php echo $car['car_id']; ?>">
                                Remove from Garage
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="purchase-history">
            <h2>Purchase History</h2>
            <div class="purchases-list">
                <?php while ($purchase = $purchases_result->fetch_assoc()): ?>
                    <div class="purchase-card">
                        <img src="images/cars/<?php echo htmlspecialchars($purchase['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($purchase['model']); ?>"
                            class="purchase-image">
                        <div class="purchase-details">
                            <h3><?php echo htmlspecialchars($purchase['brand_name'] . ' ' . $purchase['model']); ?></h3>
                            <p>Purchase Date: <?php echo date('F j, Y', strtotime($purchase['purchase_date'])); ?></p>
                            <p>Price: $<?php echo number_format($purchase['price'], 2); ?></p>
                            <p>Status: <?php echo htmlspecialchars($purchase['status']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>

    <script>
        // Handle removing saved cars
        document.querySelectorAll('.remove-saved').forEach(button => {
            button.addEventListener('click', function() {
                const carId = this.getAttribute('data-car-id');
                fetch('remove_saved_car.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `car_id=${carId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.car-card').remove();
                        }
                    });
            });
        });
    </script>
</body>

</html>