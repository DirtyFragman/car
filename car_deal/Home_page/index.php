<?php
//...
session_start();

// Function to get cart items with proper JSON handling
function getCartItems($conn)
{
    // Set JSON header first
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'User not logged in'
        ]);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $items = [];

    try {
        $sql = "SELECT c.*, cb.brand_name 
              FROM cart_items ci 
              JOIN cars c ON ci.car_id = c.car_id 
              JOIN car_brands cb ON c.brand_id = cb.brand_id 
              WHERE ci.user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'id' => $row['car_id'],
                'brand' => $row['brand_name'],
                'model' => $row['model'],
                'price' => floatval($row['price']), // Convert to float
                'image' => 'images/cars/' . $row['image_url']
            ];
        }

        echo json_encode([
            'success' => true,
            'items' => $items
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}



// Add this to handle AJAX requests
if (isset($_GET['action']) && $_GET['action'] === 'get_cart') {
    getCartItems($conn);
    exit;
}







if (isset($_SESSION['user_id'])) {
    error_log('User ID in session: ' . $_SESSION['user_id']);
} else {
    error_log('No user ID in session');
}
//...
error_reporting(0);


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_dealership";




$conn = new mysqli($servername, $username, $password, $dbname);


// Function to get all brand images from the database
function getBrandImages($conn)
{
    $brandImages = [];
    $sql = "SELECT * FROM car_brands";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $brandImages[] = [
                'id' => $row['brand_id'],
                'name' => $row['brand_name'],
                'image' => 'images/brands/' . $row['logo_url']
            ];
        }
    }
    return $brandImages;
}




// Function to get all cars from the database
function getCarImages($conn)
{
    $carImages = [];
    $sql = "SELECT c.*, b.brand_name FROM cars c JOIN car_brands b ON c.brand_id = b.brand_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $carImages[] = [
                'id' => $row['car_id'],
                'brand' => $row['brand_name'],
                'model' => $row['model'],
                'price' => $row['price'],
                'year' => $row['year'],
                'image' => 'images/cars/' . $row['image_url'],
                'mileage' => $row['mileage'],
                'fuel_type' => $row['fuel_type'],
                'transmission' => $row['transmission']
            ];
        }
    }
    return $carImages;
}




// Handle search functionality
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT c.*, b.brand_name FROM cars c
            JOIN car_brands b ON c.brand_id = b.brand_id
            WHERE b.brand_name LIKE '%$search%'
            OR c.model LIKE '%$search%'";
    $searchResults = $conn->query($sql);
    $results = [];
    while ($row = $searchResults->fetch_assoc()) {
        $results[] = $row;
    }
    echo json_encode($results);
    exit;
}




$brandImages = getBrandImages($conn);
$carImages = getCarImages($conn);
?>




<!DOCTYPE html>
<html lang="en">

<head>


    <script src="jquery-3.7.1.min.js"></script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Auto Garages</title>

    <!-- Dependencies -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom Styles -->
    <link href="styles.css" rel="stylesheet">
    <style>
        /* Main page specific styles */
        .hero {
            background-image: url('images/assets/background.jpg');
            background-size: cover;
            background-position: center;
            height: 1000px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            margin-bottom: 2rem;
        }




        .hero h1 {
            font-size: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }




        .brand-logos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            padding: 2rem;
            background: #f5f5f5;
        }




        .brand-logo {
            width: 100%;
            max-width: 150px;
            height: auto;
            transition: transform 0.2s;
        }




        .brand-logo:hover {
            transform: scale(1.1);
        }




        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }




        .car-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }




        .car-card:hover {
            transform: translateY(-5px);
        }




        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }




        .car-details {
            padding: 1rem;
        }




        .car-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }




        .car-specs {
            font-size: 0.9rem;
            color: #666;
        }




        .car-specs p {
            margin: 0.25rem 0;
        }




        .addtocart {
            width: 100%;
            padding: 0.5rem;
            background: #d4ae08;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 1rem;
        }




        .addtocart:hover {
            background: #3d3d3d;
        }
    </style>
</head>

<body>
    <!-- Include the navbar -->
    <?php require '../components/navbar.php'; ?>




    <!-- Hero Section -->
    <section class="hero">
        <h1>Drive the Extraordinary, Own the Elite!</h1>
    </section>




    <!-- Brand Logos -->
    <section class="brand-logos">
        <?php foreach ($brandImages as $brand): ?>
            <a href="brand.php?id=<?php echo urlencode($brand['id']); ?>">
                <img src="<?php echo htmlspecialchars($brand['image']); ?>"
                    alt="<?php echo htmlspecialchars($brand['name']); ?>"
                    class="brand-logo">
            </a>
        <?php endforeach; ?>
    </section>




    <!-- Featured Cars -->
    <section class="cars-grid">
        <?php foreach ($carImages as $car): ?>
            <div class="car-card">
                <img src="<?php echo htmlspecialchars($car['image']); ?>"
                    alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"
                    class="car-image">
                <div class="car-details">
                    <div class="car-title">
                        <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>
                    </div>
                    <div class="car-specs">
                        <p>Year: <?php echo htmlspecialchars($car['year']); ?></p>
                        <p>Price: $<?php echo number_format($car['price'], 2); ?></p>
                        <p>Mileage: <?php echo number_format($car['mileage']); ?> miles</p>
                        <p>Fuel: <?php echo htmlspecialchars($car['fuel_type']); ?></p>
                        <p>Transmission: <?php echo htmlspecialchars($car['transmission']); ?></p>
                    </div>
           
                    <button class="addtocart"
                        onclick="addToCart(<?php echo $car['id']; ?>)"
                        data-brand="<?php echo htmlspecialchars($car['brand']); ?>"
                        data-model="<?php echo htmlspecialchars($car['model']); ?>">
                        Add to Garage
                    </button>


                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <script>
        function displayCartItems(items) {
            const cartContainer = $('#cart-items');
            cartContainer.empty();

            if (!items || items.length === 0) {
                cartContainer.html('<p>Your garage is empty</p>');
                $('#cart-total').html('Total: $0');
                return;
            }

            let total = 0;

            items.forEach(item => {
                total += parseFloat(item.price);
                cartContainer.append(`
            <div class="cart-item">
                <img src="${item.image}" alt="${item.brand} ${item.model}" style="width: 50px; height: auto;">
                <div class="cart-item-details">
                    <span>${item.brand} ${item.model}</span>
                    <span>$${parseFloat(item.price).toLocaleString()}</span>
                </div>
                <button onclick="removeFromCart(${item.id})" class="remove-from-cart">Remove</button>
            </div>
        `);
            });

            $('#cart-total').html(`Total: $${total.toLocaleString()}`);
        }

        function addToCart(carId) {
            const button = $(event.target);
            const brand = button.data('brand');
            const model = button.data('model');

            $.ajax({
                url: '../components/add_to_cart.php',
                type: 'POST',
                dataType: 'json', // Explicitly specify JSON dataType
                data: {
                    car_id: carId
                },
                success: function(response) {
                    if (response.success) {
                        alert(`${brand} ${model} added to your garage!`);
                        loadCartItems(); // Refresh cart contents
                    } else {
                        alert(response.error || 'Error adding car to garage');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    console.log('Response:', xhr.responseText);
                    alert('Error adding car to garage');
                }
            });
        }
    </script>







</body>

</html>