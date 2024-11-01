<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

function runDiagnostics() {
    $results = [
        'jquery_check' => false,
        'session_status' => false,
        'database_connection' => false,
        'file_paths' => false,
        'car_data' => false,
        'detailed_errors' => []
    ];
    
    // 1. Check jQuery
    echo "<h3>1. Checking jQuery:</h3>";
    ?>
    <script>
        if (typeof jQuery != 'undefined') {
            console.log('jQuery Version:', jQuery.fn.jquery);
            document.write('jQuery is loaded (Version: ' + jQuery.fn.jquery + ')');
        } else {
            document.write('<span style="color: red;">jQuery is NOT loaded!</span>');
        }
    </script>
    <?php
    
    // 2. Check Session Status
    echo "<h3>2. Checking Session Status:</h3>";
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "Session is active<br>";
        echo "Session ID: " . session_id() . "<br>";
        echo "Session Data:<pre>";
        print_r($_SESSION);
        echo "</pre>";
        $results['session_status'] = true;
    } else {
        echo '<span style="color: red;">Session is NOT active!</span><br>';
        $results['detailed_errors'][] = 'Session not active';
    }
    
    // 3. Check Database Connection
    echo "<h3>3. Checking Database Connection:</h3>";
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "car_dealership";
    
    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        echo "Database connection successful<br>";
        
        // Test query
        $result = $conn->query("SHOW TABLES");
        echo "Available tables:<br>";
        while ($row = $result->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
        $results['database_connection'] = true;
    } catch (Exception $e) {
        echo '<span style="color: red;">Database Error: ' . $e->getMessage() . '</span><br>';
        $results['detailed_errors'][] = 'Database: ' . $e->getMessage();
    }
    
    // 4. Check File Paths
    echo "<h3>4. Checking Critical File Paths:</h3>";
    $critical_files = [
        'add_to_cart.php',
        'addtocart.js',
        'index.php'
    ];
    
    foreach ($critical_files as $file) {
        if (file_exists($file)) {
            echo "✓ Found: $file<br>";
        } else {
            echo "✗ Missing: $file<br>";
            $results['detailed_errors'][] = "Missing file: $file";
        }
    }
    
    // 5. Check Car Data
    echo "<h3>5. Checking Car Data:</h3>";
    if (isset($conn) && $conn->connect_error === null) {
        try {
            $result = $conn->query("SELECT * FROM cars LIMIT 1");
            if ($result && $result->num_rows > 0) {
                $car = $result->fetch_assoc();
                echo "Sample car data:<pre>";
                print_r($car);
                echo "</pre>";
                $results['car_data'] = true;
            } else {
                echo '<span style="color: red;">No cars found in database!</span><br>';
                $results['detailed_errors'][] = 'No car data available';
            }
        } catch (Exception $e) {
            echo '<span style="color: red;">Error checking car data: ' . $e->getMessage() . '</span><br>';
            $results['detailed_errors'][] = 'Car data: ' . $e->getMessage();
        }
    }
    
    // Summary
    echo "<h3>Diagnostic Summary:</h3>";
    foreach ($results as $key => $value) {
        if ($key !== 'detailed_errors') {
            $status = $value ? '✓' : '✗';
            $color = $value ? 'green' : 'red';
            echo "<div style='color: $color'>$status $key</div>";
        }
    }
    
    if (!empty($results['detailed_errors'])) {
        echo "<h4>Errors Found:</h4>";
        foreach ($results['detailed_errors'] as $error) {
            echo "<div style='color: red'>• $error</div>";
        }
    }
    
    return $results;
}

// Add some basic styling
echo '<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h3 { margin-top: 20px; border-bottom: 1px solid #ccc; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 4px; }
</style>';

// Run the diagnostics
$diagnostic_results = runDiagnostics();
?>