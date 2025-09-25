# E-commerce Lab 2 - Part 2: Customer Authentication System

## 🎯 Project Overview

This project implements a complete customer login and registration system for an e-commerce platform called "Taste of Africa". It includes full authentication functionality with session management, form validation, and database integration.

## ✨ Features Implemented

### 🔐 Authentication System
- **Customer Registration** - Complete registration form with validation
- **Customer Login** - Secure login with password verification
- **Session Management** - Proper session handling with logout functionality
- **Password Security** - Password hashing using PHP's password_hash()

### 🎨 User Interface
- **Responsive Design** - Bootstrap-based UI with custom styling
- **Form Validation** - Client-side and server-side validation
- **Real-time Feedback** - AJAX form submission with loading states
- **Error Handling** - Comprehensive error messages and notifications

### 🗄️ Database Integration
- **MySQL Database** - Complete database schema with customer table
- **Prepared Statements** - SQL injection protection
- **Database Setup Tools** - Automated database and table creation

## 📁 Project Structure

```
├── actions/
│   ├── login_customer_action.php      # Login form handler
│   ├── register_customer_action.php  # Registration form handler
│   └── register_user_action.php      # Original registration action
├── classes/
│   ├── customer_class.php            # Customer model with login functionality
│   └── user_class.php               # Original user class
├── controllers/
│   ├── customer_controller.php      # Customer controller with login method
│   └── user_controller.php          # Original user controller
├── db/
│   └── dbforlab.sql                 # Database schema
├── js/
│   ├── login.js                     # Login form validation and AJAX
│   └── register.js                  # Registration form validation and AJAX
├── login/
│   ├── login.php                    # Enhanced login form
│   ├── register.php                 # Registration form
│   └── logout.php                   # Logout functionality
├── settings/
│   ├── db_class.php                 # Database connection class
│   ├── db_cred.php                  # Database credentials
│   └── core.php                     # Core settings
├── index.php                        # Landing page with session handling
├── setup_database.php              # Database setup tool
├── debug_registration.php          # Registration debugging tool
└── README.md                       # This file
```

## 🚀 Getting Started

### Prerequisites
- XAMPP/WAMP server
- PHP 7.4 or higher
- MySQL database
- Web browser

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/HATUNGIMANA/ecom_lab2_part2.git
   ```

2. **Set up the database**
   - Start XAMPP/WAMP server
   - Navigate to `setup_database.php` in your browser
   - This will automatically create the database and tables

3. **Configure database credentials**
   - Edit `settings/db_cred.php` if needed
   - Default settings work with XAMPP

4. **Test the system**
   - Go to `index.php` to see the landing page
   - Try registering a new user at `login/register.php`
   - Test login functionality at `login/login.php`

## 🧪 Testing

### Sample User Accounts

**Customer Account:**
- Name: John Smith
- Email: john.smith@example.com
- Password: Password123
- Country: United States
- City: New York
- Contact: +1-555-123-4567

**Restaurant Owner Account:**
- Name: Maria Garcia
- Email: maria.garcia@restaurant.com
- Password: Restaurant2024!
- Country: Spain
- City: Madrid
- Contact: +34-91-123-4567

### Debugging Tools

- **`setup_database.php`** - Database setup and verification
- **`debug_registration.php`** - Comprehensive debugging tool

## 🔧 Technical Details

### Database Schema
- **Table:** `customer`
- **Fields:** customer_id, customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, customer_image, user_role
- **Security:** Password hashing, prepared statements, input validation

### Session Variables
- `customer_id` - User's unique ID
- `customer_name` - User's display name
- `customer_email` - User's email address
- `customer_contact` - User's phone number
- `user_role` - User's role/permissions
- `logged_in` - Boolean login status
- `login_time` - Timestamp of login

### Security Features
- Password hashing with `password_hash()`
- SQL injection protection with prepared statements
- Input validation and sanitization
- Session security
- CSRF protection considerations

## 📝 API Endpoints

### Registration
- **URL:** `actions/register_customer_action.php`
- **Method:** POST
- **Content-Type:** application/json
- **Parameters:** name, email, password, phone_number, country, city, role

### Login
- **URL:** `actions/login_customer_action.php`
- **Method:** POST
- **Content-Type:** application/json
- **Parameters:** email, password

## 🐛 Troubleshooting

### Common Issues
1. **Database Connection Failed**
   - Run `setup_database.php` to create database
   - Check XAMPP/WAMP is running
   - Verify database credentials

2. **Registration Fails**
   - Run `debug_registration.php` for detailed error info
   - Check browser console for JavaScript errors
   - Verify all required fields are filled

3. **Login Issues**
   - Ensure user exists in database
   - Check password is correct
   - Verify session is working

## 👨‍💻 Author

**HATUNGIMANA**
- Email: erichatungimana82@gmail.com
- GitHub: [@HATUNGIMANA](https://github.com/HATUNGIMANA)

## 📄 License

This project is part of an e-commerce lab assignment and is for educational purposes.

## 🔄 Version History

- **v1.0** - Initial implementation with basic authentication
- **v2.0** - Complete customer login/registration system with debugging tools

---

**Note:** This project implements Part 2 of the e-commerce lab focusing on customer authentication functionality.

