<?php
//Database credentials
// Settings/db_cred.php
// IMPORTANT: Update these values for your school server!

// For LOCAL development (XAMPP)
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    if (!defined("SERVER")) {
        define("SERVER", "localhost");
    }
    
    if (!defined("USERNAME")) {
        define("USERNAME", "root");
    }
    
    if (!defined("PASSWD")) {
        define("PASSWD", "");
    }
    
    if (!defined("DATABASE")) {
        define("DATABASE", "shoppn");
    }
}
// For SCHOOL SERVER - Update these with your actual credentials!
else {
    if (!defined("SERVER")) {
        define("SERVER", "");
    }
    
    if (!defined("USERNAME")) {
        define("USERNAME", "eric.hatungimana");  
    }
    
    if (!defined("PASSWD")) {
        define("PASSWD", "6100202629");
        
    }
    
    if (!defined("DATABASE")) {
        define("DATABASE", "ecommerce_2025A_eric_hatungimana");  
    }
}
?>
