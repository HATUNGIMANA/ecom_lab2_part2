<?php
/**
 * Detailed debug script to capture registration errors
 * Run: https://your-school-server.com/debug_registration.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Registration Debug</title>";
echo "<style>body{font-family:Arial;padding:20px;} pre{background:#f0f0f0;padding:10px;border:1px solid #ddd;overflow-x:auto;} .error{color:red;} .success{color:green;}</style></head><body>";
echo "<h1>Registration Debug</h1>";

// Step 1: Test includes
echo "<h2>Step 1: Testing File Includes</h2>";
try {
    echo "<p>Loading core.php...</p>";
    require_once 'settings/core.php';
    echo "<p class='success'>✓ core.php loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    exit;
}

try {
    echo "<p>Loading customer_class.php...</p>";
    require_once 'classes/customer_class.php';
    echo "<p class='success'>✓ customer_class.php loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    exit;
}

try {
    echo "<p>Loading customer_controller.php...</p>";
    require_once 'controllers/customer_controller.php';
    echo "<p class='success'>✓ customer_controller.php loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    exit;
}

// Step 2: Test database connection
echo "<h2>Step 2: Testing Database Connection</h2>";
try {
    $customer = new Customer();
    echo "<p class='success'>✓ Customer object created</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error creating Customer: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

// Step 3: Test actual registration
echo "<h2>Step 3: Testing Registration</h2>";

$test_data = [
    'name' => 'Debug Test User',
    'email' => 'debug_' . time() . '@test.com',
    'password' => 'testpass123',
    'phone_number' => '1234567890',
    'country' => 'Test Country',
    'city' => 'Test City',
    'role' => 2
];

echo "<p>Test data:</p>";
echo "<pre>" . print_r($test_data, true) . "</pre>";

try {
    $controller = new CustomerController();
    echo "<p>Controller created</p>";
    
    echo "<p>Calling register_customer_ctr...</p>";
    $result = $controller->register_customer_ctr($test_data);
    
    echo "<p>Result received:</p>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    if ($result['success']) {
        echo "<p class='success'>✓ Registration SUCCESS!</p>";
    } else {
        echo "<p class='error'>✗ Registration FAILED!</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Exception: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Step 4: Direct database test
echo "<h2>Step 4: Direct Database Test</h2>";

require_once 'settings/db_cred.php';
$connection = @mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

if ($connection) {
    echo "<p class='success'>✓ Direct database connection successful</p>";
    
    // Try direct insert
    echo "<p>Testing direct INSERT...</p>";
    
    $test_email = 'direct_' . time() . '@test.com';
    $name = 'Direct Test';
    $hashed = password_hash('test123', PASSWORD_DEFAULT);
    $phone = '1234567890';
    $country = 'Test';
    $city = 'Test';
    $role = 2;
    
    $stmt = mysqli_prepare($connection, 
        "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssi", $name, $test_email, $hashed, $phone, $country, $city, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            $insert_id = mysqli_insert_id($connection);
            echo "<p class='success'>✓ Direct INSERT successful! ID: $insert_id</p>";
            
            // Clean up
            mysqli_query($connection, "DELETE FROM customer WHERE customer_id = $insert_id");
            echo "<p>Test record deleted</p>";
        } else {
            echo "<p class='error'>✗ Direct INSERT failed: " . mysqli_stmt_error($stmt) . "</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p class='error'>✗ Failed to prepare statement: " . mysqli_error($connection) . "</p>";
    }
    
    mysqli_close($connection);
} else {
    echo "<p class='error'>✗ Direct database connection failed: " . mysqli_connect_error() . "</p>";
}

echo "</body></html>";
?>
