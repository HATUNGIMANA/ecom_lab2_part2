<?php
// Brands and Products tables setup script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>📦 Brands & Products Setup</h2>";
echo "<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
</style>";

try {
    require_once 'settings/db_cred.php';
    $connection = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    
    if (!$connection) {
        echo "<p class='error'>❌ Database connection failed</p>";
        exit;
    }
    
    echo "<p class='success'>✅ Connected!</p>";
    
    // Update brands table
    mysqli_query($connection, "ALTER TABLE `brands` ADD COLUMN IF NOT EXISTS `brand_cat` int(11) NOT NULL DEFAULT 0 AFTER `brand_name`");
    mysqli_query($connection, "ALTER TABLE `brands` ADD COLUMN IF NOT EXISTS `created_by` int(11) NOT NULL AFTER `brand_cat`");
    mysqli_query($connection, "ALTER TABLE `brands` ADD COLUMN IF NOT EXISTS `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`");
    
    // Add unique constraint on brand_name + brand_cat if not exists
    $result = mysqli_query($connection, "SHOW INDEX FROM brands WHERE Key_name = 'unique_brand_category'");
    if (mysqli_num_rows($result) == 0) {
        mysqli_query($connection, "ALTER TABLE `brands` ADD UNIQUE KEY `unique_brand_category` (`brand_name`, `brand_cat`)");
        echo "<p class='info'>✅ Added unique constraint on brand_name + brand_cat</p>";
    }
    
    // Update products table  
    mysqli_query($connection, "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `created_by` int(11) NOT NULL AFTER `product_keywords`");
    mysqli_query($connection, "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`");
    
    echo "<p class='success'>✅ Setup complete!</p>";
    mysqli_close($connection);
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>
