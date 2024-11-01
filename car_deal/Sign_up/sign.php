<?php
session_start();

// Temporarily enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_dealership";

// Connect to the database
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Function to safely redirect
function redirectToHome() {
    header("Location: ../Home_page/index.php");
    exit();
}

// Initialize error message variable
$error_message = "";

// Handle both login and signup
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $con->real_escape_string($_POST['email_php']);
    $password = $_POST['password_php'];
    
    // Check if this is a signup request
    if (isset($_POST['action']) && $_POST['action'] == 'signup') {
        try {
            // Check if email already exists
            $check_stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
            if (!$check_stmt) {
                throw new Exception("Prepare failed: " . $con->error);
            }
            
            $check_stmt->bind_param("s", $email);
            if (!$check_stmt->execute()) {
                throw new Exception("Execute failed: " . $check_stmt->error);
            }
            
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error_message = "account_exists";
            } else {
                // Hash the password for security
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $insert_stmt = $con->prepare("INSERT INTO users (username, email, password_hash) VALUES ('user',?,?)");
                if (!$insert_stmt) {
                    throw new Exception("Prepare failed: " . $con->error);
                }
                
                $insert_stmt->bind_param("ss", $email, $password_hash);
                
                if ($insert_stmt->execute()) {
                    // Get the new user's ID
                    $user_id = $con->insert_id;
                    
                    // Set session variables
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['username'] = $email;
                    $_SESSION['user_id'] = $user_id;
                    
                    redirectToHome();
                } else {
                    throw new Exception("Insert failed: " . $insert_stmt->error);
                }
                $insert_stmt->close();
            }
            $check_stmt->close();
            
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            error_log("Signup error: " . $e->getMessage());
        }
    } else {
        // This is a login request
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $row['password_hash'])) {
                // Login successful
                $_SESSION['loggedIn'] = true;
                $_SESSION['username'] = $email;
                $_SESSION['user_id'] = $row['user_id'];
                
                redirectToHome();
            } else {
                $error_message = "invalid_credentials";
            }
        } else {
            $error_message = "invalid_credentials";
        }
        $stmt->close();
    }
}

$con->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Elite Auto Garages Sign In</title>
    <link rel="stylesheet" href="HomeStyle.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            text-align: center;
            min-width: 300px;
        }

        .popup-content {
            margin-bottom: 15px;
        }

        .popup-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
<div class="SIGN-UP_form">
    <img src="signup_img/logo.png">
    <h1>SIGN-UP</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input name="email_php" type="email" class="InputEmail" placeholder="Enter Email" required>
        <input name="password_php" type="password" class="InputPassword" placeholder="Enter Password" required><br>
        <button type="submit" class="Sign-btn">LOG-IN</button>
        <hr>
        <p class="or">OR</p>
        <button type="submit" name="action" value="signup" class="Sign-btn">SIGN-UP</button>
    </form>
</div>

<!-- Popup and Overlay -->
<div class="overlay" id="overlay"></div>
<div class="popup" id="popup">
    <div class="popup-content" id="popup-message"></div>
    <button class="popup-button" onclick="closePopup()">OK</button>
</div>

<script>
function showPopup(message) {
    document.getElementById('popup-message').textContent = message;
    document.getElementById('popup').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
}

function closePopup() {
    document.getElementById('popup').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

<?php if ($error_message): ?>
    // Handle different error types
    <?php if ($error_message == "account_exists"): ?>
        showPopup("An account with this email already exists. Please log in instead.");
    <?php elseif ($error_message == "invalid_credentials"): ?>
        showPopup("Invalid email or password. Please try again.");
    <?php elseif ($error_message == "signup_error"): ?>
        showPopup("Error creating account. Please try again.");
    <?php endif; ?>
<?php endif; ?>
</script>

</body>
</html>