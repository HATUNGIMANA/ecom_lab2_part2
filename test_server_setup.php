<?php
/**
 * Comprehensive test script to identify server configuration issues
 * Run this on your school server: https://your-school-server.com/test_server_setup.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Server Setup Test</title>";
echo "<style>
    body{font-family:Arial;padding:20px;background:#f5f5f5;}
    .container{max-width:900px;margin:0 auto;background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
    h1{color:#333;border-bottom:3px solid #D19C97;padding-bottom:10px;}
    h2{color:#D19C97;margin-top:30px;}
    .test{background:#f9f9f9;padding:15px;margin:10px 0;border-left:4px solid #ddd;}
    .success{background:#d4edda;border-left-color:#28a745;color:#155724;}
    .error{background:#f8d7da;border-left-color:#dc3545;color:#721c24;}
    .warning{background:#fff3cd;border-left-color:#ffc107;color:#856404;}
    .info{background:#d1ecf1;border-left-color:#17a2b8;color:#0c5460;}
    .code{background:#000;color:#0f0;padding:10px;font-family:monospace;overflow-x:auto;}
</style></head><body>";
echo "<div class='container'>";
echo "<h1>Server Configuration Test</h1>";

// Test 1: PHP Version
echo "<h2>1. PHP Version</h2>";
echo "<div class='test " . (version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'error') . "'>";
echo "PHP Version: " . PHP_VERSION . " (" . (version_compare(PHP_VERSION, '7.4.0', '>=') ? "✓ OK" : "✗ Too old, requires 7.4+") . ")";
echo "</div>";

// Test 2: Required PHP Extensions
echo "<h2>2. PHP Extensions</h2>";
$required_extensions = ['mysqli', 'json', 'session', 'mbstring'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<div class='test " . ($loaded ? 'success' : 'error') . "'>";
    echo ($loaded ? "✓" : "✗") . " " . $ext;
    echo "</div>";
}

// Test 3: File Permissions
echo "<h2>3. File System</h2>";
$paths_to_check = [
    'settings' => is_readable('settings') ? 'Readable' : 'Not Readable',
    'classes' => is_readable('classes') ? 'Readable' : 'Not Readable',
    'controllers' => is_readable('controllers') ? 'Readable' : 'Not Readable',
    'actions' => is_readable('actions') ? 'Readable' : 'Not Readable',
];

foreach ($paths_to_check as $path => $status) {
    $is_ok = strpos($status, 'Readable') !== false;
    echo "<div class='test " . ($is_ok ? 'success' : 'error') . "'>";
    echo "Directory '$path': " . $status;
    echo "</div>";
}

// Test 4: Configuration Files
echo "<h2>4. Configuration Files</h2>";
$config_files = [
    'settings/db_cred.php' => 'Database Credentials',
    'settings/db_class.php' => 'Database Class',
    'settings/core.php' => 'Core Functions',
];

foreach ($config_files as $file => $name) {
    $exists = file_exists($file);
    echo "<div class='test " . ($exists ? 'success' : 'error') . "'>";
    echo ($exists ? "✓" : "✗") . " $name ($file)";
    echo "</div>";
}

// Test 5: Database Connection
echo "<h2>5. Database Connection</h2>";
try {
    require_once 'settings/db_cred.php';
    
    $connection = @mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    
    if ($connection) {
        echo "<div class='test success'>✓ Database connection successful</div>";
        echo "<div class='test info'>Connected to: " . DATABASE . " on " . SERVER . "</div>";
        
        // Check if customer table exists
        $result = mysqli_query($connection, "SHOW TABLES LIKE 'customer'");
        if (mysqli_num_rows($result) > 0) {
            echo "<div class='test success'>✓ Customer table exists</div>";
            
            // Check table structure
            $result = mysqli_query($connection, "DESCRIBE customer");
            if ($result) {
                echo "<div class='test info'>Customer table columns:</div>";
                echo "<div class='code'>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo $row['Field'] . " (" . $row['Type'] . ")\n";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='test error'>✗ Customer table does NOT exist - You need to import your database</div>";
        }
        
        mysqli_close($connection);
    } else {
        echo "<div class='test error'>✗ Database connection failed</div>";
        echo "<div class='test warning'>Error: " . mysqli_connect_error() . "</div>";
        echo "<div class='test info'>Check settings/db_cred.php with correct credentials</div>";
    }
} catch (Exception $e) {
    echo "<div class='test error'>✗ Exception: " . $e->getMessage() . "</div>";
}

// Test 6: Session Configuration
echo "<h2>6. Session Configuration</h2>";
$session_status = session_status();
$session_msg = [
    PHP_SESSION_DISABLED => "Sessions are disabled on this server",
    PHP_SESSION_NONE => "Sessions are available but none started",
    PHP_SESSION_ACTIVE => "Session is active"
];

echo "<div class='test " . ($session_status === PHP_SESSION_ACTIVE ? 'success' : 'warning') . "'>";
echo "Session Status: " . $session_msg[$session_status];
echo "</div>";

echo "<div class='test info'>";
echo "session_save_path(): " . session_save_path() . "<br>";
echo "is_writable(session_save_path()): " . (is_writable(session_save_path()) ? "Yes" : "No");
echo "</div>";

// Test 7: Test Registration with Actual Code
echo "<h2>7. Test Registration Process</h2>";
try {
    require_once 'classes/customer_class.php';
    require_once 'controllers/customer_controller.php';
    
    $controller = new CustomerController();
    
    $test_data = [
        'name' => 'Test User ' . time(),
        'email' => 'test_' . time() . '@example.com',
        'password' => 'testpass123',
        'phone_number' => '1234567890',
        'country' => 'Test',
        'city' => 'Test',
        'role' => 2
    ];
    
    $result = $controller->register_customer_ctr($test_data);
    
    if ($result['success']) {
        echo "<div class='test success'>✓ Registration test PASSED</div>";
        echo "<div class='test info'>Message: " . $result['message'] . "</div>";
        
        // Clean up
        $customer = new Customer();
        $customer->deleteCustomer($result['customer_id'], 2);
    } else {
        echo "<div class='test error'>✗ Registration test FAILED</div>";
        echo "<div class='test error'>Error: " . $result['message'] . "</div>";
    }
} catch (Exception $e) {
    echo "<div class='test error'>✗ Exception during registration test</div>";
    echo "<div class='code'>" . $e->getMessage() . "\nFile: " . $e->getFile() . "\nLine: " . $e->getLine() . "</div>";
}

// Test 8: Server Information
echo "<h2>8. Server Information</h2>";
echo "<div class='test info'>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "PHP SAPI: " . php_sapi_name() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Path: " . __FILE__ . "<br>";
echo "Current Working Directory: " . getcwd() . "<br>";
echo "Include Path: " . get_include_path() . "<br>";
echo "</div>";

echo "</div></body></html>";
?>

