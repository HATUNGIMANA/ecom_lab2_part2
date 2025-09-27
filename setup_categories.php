<?php
// Categories table setup script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>📂 Categories Table Setup</h2>";
echo "<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; }
    pre { background: #f8f8f8; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
</style>";

try {
    require_once 'settings/db_cred.php';
    
    echo "<h3>Categories Table Configuration</h3>";
    echo "<div class='debug'>";
    echo "<p><strong>Table:</strong> categories</p>";
    echo "<p><strong>Fields:</strong> cat_id (AUTO_INCREMENT), cat_name (UNIQUE), created_by (customer_id)</p>";
    echo "<p><strong>Purpose:</strong> Store product categories created by admin users</p>";
    echo "</div>";
    
    // Connect to database
    $connection = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    
    if (!$connection) {
        echo "<p class='error'>❌ Database connection failed: " . mysqli_connect_error() . "</p>";
        echo "<p>Please run <a href='setup_database.php'>setup_database.php</a> first to create the database.</p>";
        exit;
    }
    
    echo "<p class='success'>✅ Database connection successful!</p>";
    
    // Check if categories table exists
    $result = mysqli_query($connection, "SHOW TABLES LIKE 'categories'");
    if (mysqli_num_rows($result) == 0) {
        echo "<p class='info'>ℹ️ Categories table does not exist. Creating it...</p>";
        
        $createTableSQL = "
        CREATE TABLE `categories` (
          `cat_id` int(11) NOT NULL AUTO_INCREMENT,
          `cat_name` varchar(100) NOT NULL,
          `created_by` int(11) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`cat_id`),
          UNIQUE KEY `cat_name` (`cat_name`),
          KEY `created_by` (`created_by`),
          CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ";
        
        if (mysqli_query($connection, $createTableSQL)) {
            echo "<p class='success'>✅ Categories table created successfully!</p>";
        } else {
            echo "<p class='error'>❌ Failed to create categories table: " . mysqli_error($connection) . "</p>";
        }
    } else {
        echo "<p class='info'>ℹ️ Categories table exists. Checking structure...</p>";
        
        // Check if created_by column exists
        $result = mysqli_query($connection, "SHOW COLUMNS FROM categories LIKE 'created_by'");
        if (mysqli_num_rows($result) == 0) {
            echo "<p class='info'>ℹ️ Adding created_by column to existing table...</p>";
            
            $alterSQL = "
            ALTER TABLE `categories` 
            ADD COLUMN `created_by` int(11) NOT NULL AFTER `cat_name`,
            ADD COLUMN `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`,
            ADD KEY `created_by` (`created_by`),
            ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ";
            
            if (mysqli_query($connection, $alterSQL)) {
                echo "<p class='success'>✅ Categories table updated successfully!</p>";
            } else {
                echo "<p class='error'>❌ Failed to update categories table: " . mysqli_error($connection) . "</p>";
            }
        } else {
            echo "<p class='success'>✅ Categories table structure is correct!</p>";
        }
    }
    
    // Check table structure
    echo "<h3>Categories Table Structure</h3>";
    $result = mysqli_query($connection, "DESCRIBE categories");
    if ($result) {
        echo "<div class='debug'>";
        echo "<pre>";
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
        }
        echo "</pre>";
        echo "</div>";
    }
    
    // Test insert with admin user
    echo "<h3>Testing Category Insert Operation</h3>";
    
    // Get admin user ID
    $adminSQL = "SELECT customer_id FROM customer WHERE customer_email = 'admin@ashesi.edu.gh'";
    $adminResult = mysqli_query($connection, $adminSQL);
    
    if ($adminResult && mysqli_num_rows($adminResult) > 0) {
        $admin = mysqli_fetch_assoc($adminResult);
        $adminId = $admin['customer_id'];
        
        echo "<p class='info'>ℹ️ Found admin user with ID: " . $adminId . "</p>";
        
        // Test insert
        $testCategory = 'Test Category ' . time();
        $insertSQL = "INSERT INTO categories (cat_name, created_by) VALUES (?, ?)";
        $stmt = mysqli_prepare($connection, $insertSQL);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $testCategory, $adminId);
            
            if (mysqli_stmt_execute($stmt)) {
                $insertId = mysqli_insert_id($connection);
                echo "<p class='success'>✅ Test category insert successful! Category ID: " . $insertId . "</p>";
                
                // Clean up test record
                mysqli_query($connection, "DELETE FROM categories WHERE cat_id = $insertId");
                echo "<p class='info'>ℹ️ Test category cleaned up</p>";
            } else {
                echo "<p class='error'>❌ Test category insert failed: " . mysqli_stmt_error($stmt) . "</p>";
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo "<p class='error'>❌ Failed to prepare category insert statement: " . mysqli_error($connection) . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Admin user not found. Please run <a href='setup_admin.php'>setup_admin.php</a> first.</p>";
    }
    
    mysqli_close($connection);
    
    echo "<hr>";
    echo "<h3>✅ Categories Setup Complete!</h3>";
    echo "<p>Your categories table is ready for category management. You can now:</p>";
    echo "<ul>";
    echo "<li><a href='admin/category.php'>Access Category Management</a> (Admin only)</li>";
    echo "<li><a href='admindsh.php'>Go to Admin Dashboard</a></li>";
    echo "<li><a href='index.php'>Go to main website</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Setup error: " . $e->getMessage() . "</p>";
}
?>
