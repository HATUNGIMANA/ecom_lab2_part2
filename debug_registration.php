<?php
// Debug registration functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Registration Debug Tool</h2>";
echo "<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; }
    pre { background: #f8f8f8; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
    .setup-link { background: #e7f3ff; padding: 15px; border: 1px solid #007cba; border-radius: 5px; margin: 10px 0; }
</style>";

echo "<div class='setup-link'>";
echo "<h3>🚀 Quick Setup</h3>";
echo "<p>If you're getting database errors, run the setup tool first:</p>";
echo "<p><a href='setup_database.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Database Setup</a></p>";
echo "</div>";

// Test 1: Check if files exist
echo "<h3>1. File Existence Check</h3>";
$files_to_check = [
    'classes/customer_class.php',
    'controllers/customer_controller.php', 
    'actions/register_customer_action.php',
    'settings/db_class.php',
    'settings/db_cred.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ $file exists</p>";
    } else {
        echo "<p class='error'>❌ $file missing</p>";
    }
}

// Test 2: Check database connection
echo "<h3>2. Database Connection Test</h3>";
try {
    require_once 'settings/db_cred.php';
    require_once 'settings/db_class.php';
    
    // First check if database exists
    $connection = mysqli_connect(SERVER, USERNAME, PASSWD);
    if (!$connection) {
        echo "<p class='error'>❌ MySQL connection failed: " . mysqli_connect_error() . "</p>";
        echo "<p class='info'>Please run <a href='setup_database.php'>setup_database.php</a> first to create the database.</p>";
    } else {
        echo "<p class='success'>✅ MySQL connection successful</p>";
        
        // Check if database exists
        $result = mysqli_query($connection, "SHOW DATABASES LIKE '" . DATABASE . "'");
        if (mysqli_num_rows($result) == 0) {
            echo "<p class='error'>❌ Database '" . DATABASE . "' does not exist</p>";
            echo "<p class='info'>Please run <a href='setup_database.php'>setup_database.php</a> first to create the database.</p>";
        } else {
            echo "<p class='success'>✅ Database '" . DATABASE . "' exists</p>";
            
            // Now test the db_connection class
            $db = new db_connection();
            if ($db->db_connect()) {
                echo "<p class='success'>✅ Database connection successful</p>";
                echo "<p class='info'>Database: " . DATABASE . "</p>";
                echo "<p class='info'>Server: " . SERVER . "</p>";
                echo "<p class='info'>Username: " . USERNAME . "</p>";
            } else {
                echo "<p class='error'>❌ Database connection failed</p>";
            }
        }
        mysqli_close($connection);
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test 3: Check customer table structure
echo "<h3>3. Customer Table Structure</h3>";
try {
    // First check if database exists
    $connection = mysqli_connect(SERVER, USERNAME, PASSWD);
    if (!$connection) {
        echo "<p class='error'>❌ Cannot check table - MySQL connection failed</p>";
    } else {
        $result = mysqli_query($connection, "SHOW DATABASES LIKE '" . DATABASE . "'");
        if (mysqli_num_rows($result) == 0) {
            echo "<p class='error'>❌ Cannot check table - Database '" . DATABASE . "' does not exist</p>";
            echo "<p class='info'>Please run <a href='setup_database.php'>setup_database.php</a> first.</p>";
        } else {
            // Database exists, now check table
            mysqli_close($connection);
            $db = new db_connection();
            if ($db->db_connect()) {
                $result = $db->db_query("DESCRIBE customer");
                if ($result) {
                    echo "<p class='success'>✅ Customer table exists</p>";
                    echo "<div class='debug'>";
                    echo "<h4>Table Structure:</h4>";
                    echo "<pre>";
                    while ($row = mysqli_fetch_assoc($db->results)) {
                        print_r($row);
                    }
                    echo "</pre>";
                    echo "</div>";
                } else {
                    echo "<p class='error'>❌ Customer table not found or query failed</p>";
                    echo "<p class='info'>Please run <a href='setup_database.php'>setup_database.php</a> to create the table.</p>";
                }
            } else {
                echo "<p class='error'>❌ Cannot connect to database</p>";
            }
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Table check error: " . $e->getMessage() . "</p>";
}

// Test 4: Test Customer Class
echo "<h3>4. Customer Class Test</h3>";
try {
    require_once 'classes/customer_class.php';
    $customer = new Customer();
    echo "<p class='success'>✅ Customer class loaded successfully</p>";
    
    // Test getCustomerByEmail method
    $testEmail = 'nonexistent@test.com';
    $result = $customer->getCustomerByEmail($testEmail);
    if ($result === false || $result === null) {
        echo "<p class='success'>✅ getCustomerByEmail method works (returned false for non-existent email)</p>";
    } else {
        echo "<p class='info'>ℹ️ getCustomerByEmail returned: " . print_r($result, true) . "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Customer class error: " . $e->getMessage() . "</p>";
}

// Test 5: Test Controller
echo "<h3>5. Customer Controller Test</h3>";
try {
    require_once 'controllers/customer_controller.php';
    $controller = new CustomerController();
    echo "<p class='success'>✅ Customer controller loaded successfully</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Controller error: " . $e->getMessage() . "</p>";
}

// Test 6: Test Registration Action
echo "<h3>6. Registration Action Test</h3>";
try {
    require_once 'actions/register_customer_action.php';
    echo "<p class='success'>✅ Registration action file loaded successfully</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Registration action error: " . $e->getMessage() . "</p>";
}

// Test 7: Simulate Registration
echo "<h3>7. Simulate Registration Process</h3>";
try {
    $testData = [
        'name' => 'Debug Test User',
        'email' => 'debug@test.com',
        'password' => 'password123',
        'phone_number' => '1234567890',
        'country' => 'United States',
        'city' => 'Test City',
        'role' => 1
    ];
    
    echo "<div class='debug'>";
    echo "<h4>Test Data:</h4>";
    echo "<pre>" . print_r($testData, true) . "</pre>";
    echo "</div>";
    
    $controller = new CustomerController();
    $result = $controller->register_customer_ctr($testData);
    
    echo "<div class='debug'>";
    echo "<h4>Registration Result:</h4>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    echo "</div>";
    
    if ($result['success']) {
        echo "<p class='success'>✅ Registration simulation successful!</p>";
    } else {
        echo "<p class='error'>❌ Registration simulation failed: " . $result['message'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Registration simulation error: " . $e->getMessage() . "</p>";
    echo "<div class='debug'>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

// Test 8: Check existing customers
echo "<h3>8. Existing Customers Check</h3>";
try {
    $db = new db_connection();
    if ($db->db_connect()) {
        $result = $db->db_query("SELECT COUNT(*) as count FROM customer");
        if ($result) {
            $row = mysqli_fetch_assoc($db->results);
            echo "<p class='info'>ℹ️ Total customers in database: " . $row['count'] . "</p>";
            
            // Show recent customers
            $result = $db->db_query("SELECT customer_id, customer_name, customer_email, customer_country, customer_city, user_role FROM customer ORDER BY customer_id DESC LIMIT 5");
            if ($result) {
                echo "<div class='debug'>";
                echo "<h4>Recent Customers:</h4>";
                echo "<pre>";
                while ($row = mysqli_fetch_assoc($db->results)) {
                    print_r($row);
                }
                echo "</pre>";
                echo "</div>";
            }
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Customer check error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🔧 Next Steps</h3>";
echo "<p>1. Check the errors above and fix any issues</p>";
echo "<p>2. If database connection fails, check your XAMPP/WAMP settings</p>";
echo "<p>3. If table doesn't exist, run the SQL file in phpMyAdmin</p>";
echo "<p>4. Try registering again after fixing issues</p>";
echo "<p><a href='login/register.php'>Go to Registration Form</a></p>";
echo "<p><a href='index.php'>Go to Home</a></p>";
?>
