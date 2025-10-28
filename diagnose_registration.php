<?php
/**
 * Diagnostic script to help identify registration issues on the server
 * Run this on your school server to see what's causing the problem
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Registration Diagnosis</title>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";
echo "</head><body>";
echo "<h1>Registration Problem Diagnosis</h1>";

// Check 1: Database Connection
echo "<h2>1. Database Connection</h2>";
require_once 'settings/db_cred.php';
$connection = @mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
if ($connection) {
    echo "<p class='success'>✓ Database connection successful</p>";
} else {
    echo "<p class='error'>✗ Database connection failed: " . mysqli_connect_error() . "</p>";
    echo "</body></html>";
    exit;
}

// Check 2: Customer Table Structure
echo "<h2>2. Customer Table Structure</h2>";
$result = mysqli_query($connection, "DESCRIBE customer");
if ($result) {
    echo "<p class='success'>✓ Customer table exists</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>✗ Customer table does not exist: " . mysqli_error($connection) . "</p>";
}

// Check 3: Required Columns
echo "<h2>3. Required Columns Check</h2>";
$required_columns = ['customer_name', 'customer_email', 'customer_pass', 'customer_contact', 'customer_country', 'customer_city', 'user_role'];
$existing_columns = [];
if ($result) {
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        $existing_columns[] = $row['Field'];
    }
}

foreach ($required_columns as $col) {
    if (in_array($col, $existing_columns)) {
        echo "<p class='success'>✓ Column '$col' exists</p>";
    } else {
        echo "<p class='error'>✗ Column '$col' is MISSING</p>";
    }
}

// Check 4: Test Insert
echo "<h2>4. Test Registration</h2>";
try {
    require_once 'classes/customer_class.php';
    $customer = new Customer();
    
    $test_email = 'test_' . time() . '@example.com';
    $test_name = 'Test User';
    $test_pass = 'testpass123';
    $test_phone = '1234567890';
    $test_country = 'Test Country';
    $test_city = 'Test City';
    
    $customerId = $customer->createCustomer($test_name, $test_email, $test_pass, $test_phone, $test_country, $test_city, 2);
    
    if ($customerId) {
        echo "<p class='success'>✓ Test registration successful! Customer ID: $customerId</p>";
        
        // Clean up test record
        mysqli_query($connection, "DELETE FROM customer WHERE customer_id = $customerId");
        echo "<p class='info'>ℹ Test record cleaned up</p>";
    } else {
        echo "<p class='error'>✗ Test registration failed</p>";
        echo "<p class='info'>Error details should appear above</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error during test: " . $e->getMessage() . "</p>";
    echo "<p class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}

// Check 5: Check File Paths
echo "<h2>5. File Path Check</h2>";
$files_to_check = [
    'settings/core.php',
    'settings/db_class.php',
    'settings/db_cred.php',
    'classes/customer_class.php',
    'controllers/customer_controller.php',
    'actions/register_customer_action.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ File '$file' exists</p>";
    } else {
        echo "<p class='error'>✗ File '$file' is MISSING</p>";
    }
}

echo "</body></html>";
?>

