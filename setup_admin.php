<?php
// Admin user setup script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔐 Admin User Setup</h2>";
echo "<style>
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; }
    pre { background: #f8f8f8; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
</style>";

try {
    require_once 'settings/db_cred.php';
    
    echo "<h3>Admin User Configuration</h3>";
    echo "<div class='debug'>";
    echo "<p><strong>Admin Email:</strong> admin@ashesi.edu.gh</p>";
    echo "<p><strong>Admin Password:</strong> Adm!n123++Ecom</p>";
    echo "<p><strong>Admin Role:</strong> 1 (Administrator)</p>";
    echo "</div>";
    
    // Connect to database
    $connection = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    
    if (!$connection) {
        echo "<p class='error'>❌ Database connection failed: " . mysqli_connect_error() . "</p>";
        echo "<p>Please run <a href='setup_database.php'>setup_database.php</a> first to create the database.</p>";
        exit;
    }
    
    echo "<p class='success'>✅ Database connection successful!</p>";
    
    // Check if admin user already exists
    $adminEmail = 'admin@ashesi.edu.gh';
    $checkSQL = "SELECT customer_id, customer_name FROM customer WHERE customer_email = ?";
    $stmt = mysqli_prepare($connection, $checkSQL);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $adminEmail);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            echo "<p class='info'>ℹ️ Admin user already exists!</p>";
            echo "<div class='debug'>";
            echo "<p><strong>Admin ID:</strong> " . $admin['customer_id'] . "</p>";
            echo "<p><strong>Admin Name:</strong> " . htmlspecialchars($admin['customer_name']) . "</p>";
            echo "<p><strong>Admin Email:</strong> " . htmlspecialchars($adminEmail) . "</p>";
            echo "</div>";
            
            // Update admin password to ensure it's correct
            $newPassword = 'Adm!n123++Ecom';
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $updateSQL = "UPDATE customer SET customer_pass = ?, user_role = 1 WHERE customer_email = ?";
            $updateStmt = mysqli_prepare($connection, $updateSQL);
            
            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, "ss", $hashedPassword, $adminEmail);
                if (mysqli_stmt_execute($updateStmt)) {
                    echo "<p class='success'>✅ Admin password updated successfully!</p>";
                } else {
                    echo "<p class='error'>❌ Failed to update admin password: " . mysqli_stmt_error($updateStmt) . "</p>";
                }
                mysqli_stmt_close($updateStmt);
            }
        } else {
            echo "<p class='info'>ℹ️ Admin user does not exist. Creating new admin user...</p>";
            
            // Create admin user
            $adminName = 'System Administrator';
            $adminPassword = 'Adm!n123++Ecom';
            $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
            $adminContact = '+233-XXX-XXX-XXX';
            $adminCountry = 'Ghana';
            $adminCity = 'Accra';
            $adminRole = 1; // Admin role
            
            $insertSQL = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = mysqli_prepare($connection, $insertSQL);
            
            if ($insertStmt) {
                mysqli_stmt_bind_param($insertStmt, "ssssssi", $adminName, $adminEmail, $hashedPassword, $adminContact, $adminCountry, $adminCity, $adminRole);
                
                if (mysqli_stmt_execute($insertStmt)) {
                    $adminId = mysqli_insert_id($connection);
                    echo "<p class='success'>✅ Admin user created successfully!</p>";
                    echo "<div class='debug'>";
                    echo "<p><strong>Admin ID:</strong> " . $adminId . "</p>";
                    echo "<p><strong>Admin Name:</strong> " . htmlspecialchars($adminName) . "</p>";
                    echo "<p><strong>Admin Email:</strong> " . htmlspecialchars($adminEmail) . "</p>";
                    echo "<p><strong>Admin Role:</strong> " . $adminRole . " (Administrator)</p>";
                    echo "</div>";
                } else {
                    echo "<p class='error'>❌ Failed to create admin user: " . mysqli_stmt_error($insertStmt) . "</p>";
                }
                mysqli_stmt_close($insertStmt);
            } else {
                echo "<p class='error'>❌ Failed to prepare insert statement: " . mysqli_error($connection) . "</p>";
            }
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "<p class='error'>❌ Failed to prepare check statement: " . mysqli_error($connection) . "</p>";
    }
    
    // Verify admin user can login
    echo "<h3>Testing Admin Login</h3>";
    $testEmail = 'admin@ashesi.edu.gh';
    $testPassword = 'Adm!n123++Ecom';
    
    $verifySQL = "SELECT customer_id, customer_name, customer_pass, user_role FROM customer WHERE customer_email = ?";
    $verifyStmt = mysqli_prepare($connection, $verifySQL);
    
    if ($verifyStmt) {
        mysqli_stmt_bind_param($verifyStmt, "s", $testEmail);
        mysqli_stmt_execute($verifyStmt);
        $result = mysqli_stmt_get_result($verifyStmt);
        
        if ($adminData = mysqli_fetch_assoc($result)) {
            if (password_verify($testPassword, $adminData['customer_pass'])) {
                echo "<p class='success'>✅ Admin login verification successful!</p>";
                echo "<div class='debug'>";
                echo "<p><strong>Login Test:</strong> PASSED</p>";
                echo "<p><strong>Admin ID:</strong> " . $adminData['customer_id'] . "</p>";
                echo "<p><strong>Admin Name:</strong> " . htmlspecialchars($adminData['customer_name']) . "</p>";
                echo "<p><strong>Admin Role:</strong> " . $adminData['user_role'] . " (Administrator)</p>";
                echo "</div>";
            } else {
                echo "<p class='error'>❌ Password verification failed!</p>";
            }
        } else {
            echo "<p class='error'>❌ Admin user not found!</p>";
        }
        mysqli_stmt_close($verifyStmt);
    }
    
    mysqli_close($connection);
    
    echo "<hr>";
    echo "<h3>✅ Admin Setup Complete!</h3>";
    echo "<p>Your admin user is ready. You can now:</p>";
    echo "<ul>";
    echo "<li><a href='admindsh.php' target='_blank'>Access Admin Dashboard</a></li>";
    echo "<li><a href='index.php'>Go to main website</a></li>";
    echo "<li><a href='login/login.php'>Login as regular user</a></li>";
    echo "</ul>";
    
    echo "<div class='debug'>";
    echo "<h4>Admin Access Information:</h4>";
    echo "<p><strong>Dashboard URL:</strong> <a href='admindsh.php' target='_blank'>admindsh.php</a></p>";
    echo "<p><strong>Email:</strong> admin@ashesi.edu.gh</p>";
    echo "<p><strong>Password:</strong> Adm!n123++Ecom</p>";
    echo "<p><strong>Note:</strong> This admin access is separate from regular customer accounts.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Setup error: " . $e->getMessage() . "</p>";
}
?>
