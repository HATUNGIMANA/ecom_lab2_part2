<?php
/**
 * Database credentials template for school server
 * Copy this file to db_cred.php and update with your school server credentials
 */

// School server database configuration
// Update these values to match your school server's database settings

if (!defined("SERVER")) {
    // Change 'localhost' if your school server uses a different host
    define("SERVER", "localhost");
}

if (!defined("USERNAME")) {
    // Update with your school server database username
    define("USERNAME", "your_username");
}

if (!defined("PASSWD")) {
    // Update with your school server database password
    define("PASSWD", "your_password");
}

if (!defined("DATABASE")) {
    // Update with your school server database name
    define("DATABASE", "your_database_name");
}
?>

