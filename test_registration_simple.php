<?php
/**
 * Simple registration test to capture exact error
 * Visit: https://your-school-server.com/test_registration_simple.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Registration Test</h1>";
echo "<pre>";

try {
    echo "1. Loading core.php...\n";
    require_once __DIR__ . '/settings/core.php';
    echo "   ✓ OK\n\n";
    
    echo "2. Loading customer_class.php...\n";
    require_once __DIR__ . '/classes/customer_class.php';
    echo "   ✓ OK\n\n";
    
    echo "3. Loading customer_controller.php...\n";
    require_once __DIR__ . '/controllers/customer_controller.php';
    echo "   ✓ OK\n\n";
    
    echo "4. Creating Customer instance...\n";
    $customer = new Customer();
    echo "   ✓ OK\n\n";
    
    echo "5. Creating CustomerController instance...\n";
    $controller = new CustomerController();
    echo "   ✓ OK\n\n";
    
    echo "6. Testing database connection...\n";
    require_once __DIR__ . '/settings/db_cred.php';
    $connection = @mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    if ($connection) {
        echo "   ✓ Connected to: " . DATABASE . "\n\n";
        
        // Check table structure
        $result = mysqli_query($connection, "DESCRIBE customer");
        echo "7. Customer table structure:\n";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "   - {$row['Field']} ({$row['Type']})\n";
        }
        echo "\n";
        mysqli_close($connection);
    } else {
        echo "   ✗ FAILED: " . mysqli_connect_error() . "\n\n";
    }
    
    echo "8. Testing registration...\n";
    $test_data = [
        'name' => 'Test User',
        'email' => 'test_' . time() . '@example.com',
        'password' => 'testpass123',
        'phone_number' => '1234567890',
        'country' => 'Test',
        'city' => 'Test',
        'role' => 2
    ];
    
    $result = $controller->register_customer_ctr($test_data);
    
    echo "9. Registration result:\n";
    print_r($result);
    
    if ($result['success']) {
        echo "\n\n✓ SUCCESS! Registration works!\n";
    } else {
        echo "\n\n✗ FAILED: " . $result['message'] . "\n";
    }
    
} catch (Error $e) {
    echo "\n\n✗ ERROR:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString();
} catch (Exception $e) {
    echo "\n\n✗ EXCEPTION:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "</pre>";
?>

