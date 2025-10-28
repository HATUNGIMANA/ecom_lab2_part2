<?php
require_once __DIR__ . '/../settings/core.php';

require_once __DIR__ . '/../controllers/customer_controller.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST method is allowed'
    ]);
    exit;
}

// Get JSON input or form data
$input = json_decode(file_get_contents('php://input'), true);

// If no JSON input, try to get form data
if (!$input) {
    $input = $_POST;
}

// Extract email and password
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

// Validate required fields
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required'
    ]);
    exit;
}

try {
    // Create customer controller instance
    $customerController = new CustomerController();
    
    // Prepare arguments for login method
    $kwargs = [
        'email' => trim($email),
        'password' => $password
    ];
    
    // Call login method
    $result = $customerController->login_customer_ctr($kwargs);
    
    // If login is successful, set session variables
    if ($result['success']) {
        $customer = $result['customer'];
        
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Set session variables (standardize identifiers)
        $_SESSION['id'] = $customer['customer_id'];
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['customer_name'] = $customer['customer_name'];
        $_SESSION['customer_email'] = $customer['customer_email'];
        $_SESSION['customer_contact'] = $customer['customer_contact'];
        $_SESSION['user_role'] = $customer['user_role'];
        $_SESSION['logged_in'] = true;
        $now = time();
        $_SESSION['created_at'] = $_SESSION['created_at'] ?? $now;
        $_SESSION['last_activity'] = $now;
        $_SESSION['login_time'] = $now;
        
        // Add success message
        $result['message'] = 'Login successful! Welcome back, ' . $customer['customer_name'];
    }
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during login. Please try again.'
    ]);
}
