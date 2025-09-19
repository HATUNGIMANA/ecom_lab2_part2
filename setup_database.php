<?php
// Database setup script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🗄️ Database Setup</h2>";
echo "<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; }
    pre { background: #f8f8f8; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
</style>";

try {
    require_once 'settings/db_cred.php';
    
    echo "<h3>Database Configuration</h3>";
    echo "<div class='debug'>";
    echo "<p><strong>Server:</strong> " . SERVER . "</p>";
    echo "<p><strong>Username:</strong> " . USERNAME . "</p>";
    echo "<p><strong>Password:</strong> " . (PASSWD ? '***' : 'Empty') . "</p>";
    echo "<p><strong>Database:</strong> " . DATABASE . "</p>";
    echo "</div>";
    
    // First, connect without specifying database to create it if needed
    $connection = mysqli_connect(SERVER, USERNAME, PASSWD);
    
    if (!$connection) {
        echo "<p class='error'>❌ MySQL connection failed: " . mysqli_connect_error() . "</p>";
        echo "<h3>Possible Solutions:</h3>";
        echo "<ul>";
        echo "<li>Make sure XAMPP/WAMP is running</li>";
        echo "<li>Check if MySQL service is started</li>";
        echo "<li>Verify database credentials in settings/db_cred.php</li>";
        echo "</ul>";
        exit;
    }
    
    echo "<p class='success'>✅ MySQL connection successful!</p>";
    
    // Check if database exists, create if it doesn't
    $result = mysqli_query($connection, "SHOW DATABASES LIKE '" . DATABASE . "'");
    if (mysqli_num_rows($result) == 0) {
        echo "<p class='info'>ℹ️ Database '" . DATABASE . "' does not exist. Creating it...</p>";
        
        if (mysqli_query($connection, "CREATE DATABASE " . DATABASE)) {
            echo "<p class='success'>✅ Database '" . DATABASE . "' created successfully!</p>";
        } else {
            echo "<p class='error'>❌ Failed to create database: " . mysqli_error($connection) . "</p>";
            exit;
        }
    } else {
        echo "<p class='success'>✅ Database '" . DATABASE . "' exists!</p>";
    }
    
    // Now connect to the specific database
    mysqli_close($connection);
    $connection = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    
    if (!$connection) {
        echo "<p class='error'>❌ Database connection failed: " . mysqli_connect_error() . "</p>";
        exit;
    }
    
    echo "<p class='success'>✅ Database connection successful!</p>";
    
    // Check if customer table exists
    $result = mysqli_query($connection, "SHOW TABLES LIKE 'customer'");
    if (mysqli_num_rows($result) == 0) {
        echo "<p class='error'>❌ Customer table does not exist!</p>";
        echo "<h3>Creating customer table...</h3>";
        
        $createTableSQL = "
        CREATE TABLE `customer` (
          `customer_id` int(11) NOT NULL AUTO_INCREMENT,
          `customer_name` varchar(100) NOT NULL,
          `customer_email` varchar(50) NOT NULL,
          `customer_pass` varchar(150) NOT NULL,
          `customer_country` varchar(30) NOT NULL,
          `customer_city` varchar(30) NOT NULL,
          `customer_contact` varchar(15) NOT NULL,
          `customer_image` varchar(100) DEFAULT NULL,
          `user_role` int(11) NOT NULL,
          PRIMARY KEY (`customer_id`),
          UNIQUE KEY `customer_email` (`customer_email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ";
        
        if (mysqli_query($connection, $createTableSQL)) {
            echo "<p class='success'>✅ Customer table created successfully!</p>";
        } else {
            echo "<p class='error'>❌ Failed to create customer table: " . mysqli_error($connection) . "</p>";
        }
    } else {
        echo "<p class='success'>✅ Customer table exists!</p>";
    }
    
    // Check table structure
    echo "<h3>Customer Table Structure</h3>";
    $result = mysqli_query($connection, "DESCRIBE customer");
    if ($result) {
        echo "<div class='debug'>";
        echo "<pre>";
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
        }
        echo "</pre>";
        echo "</div>";
    }
    
    // Test insert
    echo "<h3>Testing Insert Operation</h3>";
    $testEmail = 'test_' . time() . '@example.com';
    $testPassword = password_hash('testpassword', PASSWORD_DEFAULT);
    
    $insertSQL = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insertSQL);
    
    if ($stmt) {
        $name = 'Test User';
        $email = $testEmail;
        $password = $testPassword;
        $contact = '1234567890';
        $country = 'Test Country';
        $city = 'Test City';
        $role = 1;
        
        mysqli_stmt_bind_param($stmt, "ssssssi", $name, $email, $password, $contact, $country, $city, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            $insertId = mysqli_insert_id($connection);
            echo "<p class='success'>✅ Test insert successful! Customer ID: " . $insertId . "</p>";
            
            // Clean up test record
            mysqli_query($connection, "DELETE FROM customer WHERE customer_id = $insertId");
            echo "<p class='info'>ℹ️ Test record cleaned up</p>";
        } else {
            echo "<p class='error'>❌ Test insert failed: " . mysqli_stmt_error($stmt) . "</p>";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "<p class='error'>❌ Failed to prepare statement: " . mysqli_error($connection) . "</p>";
    }
    
    mysqli_close($connection);
    
    echo "<hr>";
    echo "<h3>✅ Database Setup Complete!</h3>";
    echo "<p>Your database is ready for registration. You can now:</p>";
    echo "<ul>";
    echo "<li><a href='login/register.php'>Try registering a new user</a></li>";
    echo "<li><a href='debug_registration.php'>Run registration debug tool</a></li>";
    echo "<li><a href='index.php'>Go to home page</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Setup error: " . $e->getMessage() . "</p>";
}
?>
