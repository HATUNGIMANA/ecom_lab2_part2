<?php
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../controllers/customer_controller.php';

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

// Allow registration regardless of current session state

// Get JSON input or form data
$input = json_decode(file_get_contents('php://input'), true);

// If no JSON input, try to get form data
if (!$input) {
    $input = $_POST;
}

// Extract form data
$name = $input['name'] ?? '';
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';
$phone_number = $input['phone_number'] ?? '';
$country = $input['country'] ?? '';
$city = $input['city'] ?? '';
$role = $input['role'] ?? 1; // Default role is 1 (customer)

// Validate required fields
if (empty($name) || empty($email) || empty($password) || empty($phone_number) || empty($country) || empty($city)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
    ]);
    exit;
}

try {
    // Create customer controller instance
    $customerController = new CustomerController();
    
    // Prepare arguments for register method
    $kwargs = [
        'name' => trim($name),
        'email' => strtolower(trim($email)),
        'password' => $password,
        'phone_number' => trim($phone_number),
        'country' => trim($country),
        'city' => trim($city),
        'role' => intval($role)
    ];
    
    // Log the data being sent for debugging
    error_log("Registration attempt with data: " . json_encode($kwargs));
    
    // Call register method
    $result = $customerController->register_customer_ctr($kwargs);
    
    // Log the result for debugging
    error_log("Registration result: " . json_encode($result));
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    // Log the full error for debugging
    error_log("Registration error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during registration. Please try again.',
        'debug_info' => 'Check server logs for details'
    ]);
}
