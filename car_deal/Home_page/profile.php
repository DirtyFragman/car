<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
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

// Fetch user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, email, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch cart items
$cart_query = "SELECT c.car_id, b.brand_name, c.model, c.price, c.year, c.image_url 
               FROM garage ca 
               JOIN cars c ON ca.car_id = c.car_id 
               JOIN car_brands b ON c.brand_id = b.brand_id 
               WHERE ca.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();
$cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);

// Handle password change
$password_error = "";
$password_success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    $verify_query = "SELECT password_hash FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    if (password_verify($current_password, $user_data['password_hash'])) {
        // Validate new password
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 8) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("si", $new_password_hash, $user_id);
                
                if ($stmt->execute()) {
                    $password_success = "Password changed successfully!";
                } else {
                    $password_error = "Error updating password. Please try again.";
                }
            } else {
                $password_error = "New password must be at least 8 characters long.";
            }
        } else {
            $password_error = "New passwords do not match.";
        }
    } else {
        $password_error = "Current password is incorrect.";
    }
}

// Handle cart item removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_cart_item'])) {
    $car_id_to_remove = $_POST['car_id'];
    $remove_query = "DELETE FROM cart WHERE user_id = ? AND car_id = ?";
    $stmt = $conn->prepare($remove_query);
    $stmt->bind_param("ii", $user_id, $car_id_to_remove);
    $stmt->execute();
    
    // Refresh cart items
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    $cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Elite Auto Garages</title>
    
    <!-- Dependencies -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="styles.css" rel="stylesheet">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #c8a45d;
        }

        .profile-section {
            margin-bottom: 2rem;
            padding: 1rem;
            background: #c8a45d;
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #d4ae08;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background: #3d3d3d;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
        }

        .success {
            color: green;
            margin-bottom: 1rem;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: white;
            border-bottom: 1px solid #eee;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 1rem;
        }

        .cart-item-details {
            flex-grow: 1;
            color:#3d3d3d;
        }

        .remove-cart-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Include the navbar -->
    <?php include '../components/navbar.php'; ?>

    <div class="profile-container">
        <div class="profile-header">
            <h1>User Profile</h1>
        </div>

        <div class="profile-section">
            <h2>Account Information</h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Account Created:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
        </div>

        <div class="profile-section">
            <h2>Change Password</h2>
            <?php 
            if (!empty($password_error)) {
                echo "<div class='error'>$password_error</div>";
            }
            if (!empty($password_success)) {
                echo "<div class='success'>$password_success</div>";
            }
            ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn">Change Password</button>
            </form>
        </div>

        <div class="profile-section">
            <h2>My Garage</h2>
            <?php if (empty($cart_items)): ?>
                <p>Your garage is empty.</p>
            <?php else: ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="images/cars/<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['brand_name'] . ' ' . $item['model']); ?>">
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['brand_name'] . ' ' . $item['model']); ?></h3>
                            <p>Year: <?php echo htmlspecialchars($item['year']); ?></p>
                            <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="car_id" value="<?php echo $item['car_id']; ?>">
                            <button type="submit" name="remove_cart_item" class="remove-cart-btn">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>