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

    $totalPrice = 0;


    foreach ($cart_items as $obj) {
        $totalPrice += (float)$obj->price;
    }
} catch (Exception $e) {
    error_log("Error in get_cart.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error fetching garage items']);
}
?>

<!-- navbar.php -->
<nav class="navbar">
    <a href="index.php">
        <img src="images/assets/site_logo.png" alt="Elite Auto Garages" class="logo">
    </a>
    <div class="search-bar-container">
        <input type="text" id="searchInput" placeholder="Search vehicles..." class="search-bar">
        <div class="search-results" id="searchResults"></div>
    </div>
    <div class="nav-icons">
        <button class="cart_button" id="open-cart">Open Garage</button>
        <div class="dropdown">
            <i class="fas fa-bars" id="menu-icon"></i>
            <div class="dropdown-content">
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="../Sign_up/sign.php">Login/Signup</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Slide Cart/Garage Panel -->
<div id="slide-cart">
    <div class="cart_style" id="cart-content">
        <h1>My Garage</h1>
        
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
        <p>Total: <span id="cart-total">
            <?php
            $totalPrice
            ?>
        </span></p>
        <button class="cart_button" id="clear-cart">Clear Garage</button>
        <button class="cart_button" id="close-cart">Close</button>
    </div>
    <button onclick="location.href='payment.php'" class="purchase_cart">Purchase</button>
</div>

<style>
    .navbar {
        background-color: #333;
        padding: 1rem;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        height: 40px;
    }

    .search-bar-container {
        position: relative;
        flex-grow: 1;
        margin: 0 2rem;
    }

    .search-bar {
        width: 100%;
        padding: 8px;
        border-radius: 4px;
        border: none;
    }

    .search-results {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        z-index: 1000;
    }

    .search-results div {
        padding: 10px;
        cursor: pointer;
        color: #333;
    }

    .search-results div:hover {
        background-color: #f5f5f5;
    }

    .nav-icons {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .cart_button {
        padding: 8px 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .cart_button:hover {
        background-color: #45a049;
    }

    #slide-cart {
        position: fixed;
        right: -600px;
        top: 0;
        width: 600px;
        height: 100%;
        background: #404040;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
        transition: 0.3s;
        z-index: 1000;
        padding: 20px;
    }

    .purchase_cart {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 20px;
    }

    .purchase_cart:hover {
        background-color: #0056b3;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');

        searchInput.addEventListener('input', function() {
            if (this.value.length > 2) {
                fetch(`index.php?search=${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        data.forEach(car => {
                            const div = document.createElement('div');
                            div.textContent = `${car.brand_name} ${car.model} (${car.year})`;
                            div.onclick = () => window.location.href = `car.php?id=${car.car_id}`;
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                    });
            } else {
                searchResults.style.display = 'none';
            }
        });

        // Cart/Garage functionality
        const openCartButton = document.getElementById('open-cart');
        const closeCartButton = document.getElementById('close-cart');
        const clearCartButton = document.getElementById('clear-cart');
        const slideCart = document.getElementById('slide-cart');
        const cartItemsElement = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');

        function loadCartItems() {
            $.ajax({
                url: '../components/get_cart.php',
                type: 'GET',
                success: function(response) {
                    try {
                        console.log("Responce", response)

                        const cartItems = JSON.decode(response);

                        const cartTotal = cartItems.reduce((total, item) => total + parseFloat(item.price), 0);
                        console.log("Totel", cartTotal)

                        updateCart(cartItems, cartTotal);
                    } catch (e) {
                        console.error('Error parsing cart data:', e);
                        console.log('Raw response:', response); // Add this to see the raw response
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.log('Response Text:', xhr.responseText);
                    alert('Error loading garage items');
                }
            });
        }

        function updateCart(cartItems, cartTotal) {
            cartItemsElement.innerHTML = '';
            cartTotalElement.textContent = '$' + cartTotal.toFixed(2);

            cartItems.forEach(item => {
                const listItem = document.createElement('li');
                listItem.textContent = `${item.brand_name} ${item.model} - $${parseFloat(item.price).toFixed(2)}`;

                const removeButton = document.createElement('button');
                removeButton.textContent = 'Remove';
                removeButton.classList.add('cart_button');
                removeButton.addEventListener('click', () => {
                    removeFromCart(item.garage_id);
                });

                listItem.appendChild(removeButton);
                cartItemsElement.appendChild(listItem);
            });
        }

        // Update other AJAX calls as well
        function removeFromCart(garageId) {
            $.ajax({
                url: 'remove_from_cart.php',
                type: 'POST',
                data: {
                    garage_id: garageId
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.error) {
                            alert(result.error);
                        } else {
                            loadCartItems();
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        console.log('Raw response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.log('Response Text:', xhr.responseText);
                }
            });
        }

        openCartButton.addEventListener('click', () => {
            slideCart.style.right = '0';
            loadCartItems();
        });

        closeCartButton.addEventListener('click', () => {
            slideCart.style.right = '-600px';
        });

        // Update clear cart AJAX call
        clearCartButton.addEventListener('click', () => {
            if (confirm('Are you sure you want to clear your garage?')) {
                $.ajax({
                    url: 'clear_cart.php',
                    type: 'POST',
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.error) {
                                alert(result.error);
                            } else {
                                loadCartItems();
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            console.log('Raw response:', response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.log('Response Text:', xhr.responseText);
                    }
                });
            }
        });

        // Load cart items when page loads
        // loadCartItems();
    });
</script>