# Database Setup Instructions for School Server

## Problem
The school server needs different database credentials than localhost.

## Solution

### Step 1: Update Database Credentials

Edit the file `settings/db_cred.php` on your **school server** with the correct credentials:

```php
<?php
//School server database configuration

if (!defined("SERVER")) {
    // Change this to your school server's database host
    define("SERVER", "localhost");  // Or your school's DB host
}

if (!defined("USERNAME")) {
    // Change this to your school server database username
    define("USERNAME", "eric_hatungimana");  // Your school server username
}

if (!defined("PASSWD")) {
    // Change this to your school server database password
    define("PASSWD", "YOUR_SCHOOL_PASSWORD");  // Your school password
}

if (!defined("DATABASE")) {
    // Change this to your school server database name
    define("DATABASE", "eric_hatungimana_db");  // Your database name
}
?>
```

### Step 2: Find Your School Database Credentials

You need to find out:
1. **Database username** - usually same as your hosting username
2. **Database password** - Check your hosting control panel (cPanel)
3. **Database name** - Usually in format `username_db` or check cPanel

### Step 3: Verify Table Structure

Make sure your `customer` table has these columns:
- customer_id (AUTO_INCREMENT)
- customer_name
- customer_email (UNIQUE)
- customer_pass
- customer_contact
- customer_country
- customer_city
- user_role (INT, default 2)

### Step 4: Add Missing Column (if needed)

If the `user_role` column is missing, run this SQL in phpMyAdmin:

```sql
ALTER TABLE customer ADD COLUMN user_role INT(11) NOT NULL DEFAULT 2;
```

### Common School Server Database Names

Typical patterns for school servers:
- Username: Your login username
- Password: Your hosting password (from cPanel)
- Database name: Usually `username_db` or `username_maindb`

### How to Check in cPanel

1. Login to cPanel
2. Find "MySQL Databases"
3. Look at "Current Databases" to see your database name
4. Look at "MySQL Users" to see your username

### Testing

After updating `settings/db_cred.php` on the school server, run:

```
https://your-school-server.com/test_registration_simple.php
```

This will tell you if the connection works.

