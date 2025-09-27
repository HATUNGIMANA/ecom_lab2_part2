<?php
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Check if user is logged in and is admin
if (!is_logged_in()) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to perform this action'
    ]);
    exit;
}

// Check if user is admin (role = 1)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Only administrators can manage categories'
    ]);
    exit;
}

// Get JSON input or form data
$input = json_decode(file_get_contents('php://input'), true);

// If no JSON input, try to get form data
if (!$input) {
    $input = $_POST;
}

// Extract form data
$cat_name = $input['cat_name'] ?? '';
$created_by = $_SESSION['customer_id'] ?? $_SESSION['id'] ?? 0;

// Validate required fields
if (empty($cat_name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Category name is required'
    ]);
    exit;
}

try {
    // Create category controller instance
    $categoryController = new CategoryController();
    
    // Prepare arguments for add method
    $kwargs = [
        'cat_name' => trim($cat_name),
        'created_by' => intval($created_by)
    ];
    
    // Log the data being sent for debugging
    error_log("Category creation attempt with data: " . json_encode($kwargs));
    
    // Call add method
    $result = $categoryController->add_category_ctr($kwargs);
    
    // Log the result for debugging
    error_log("Category creation result: " . json_encode($result));
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    // Log the full error for debugging
    error_log("Category creation error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during category creation. Please try again.',
        'debug_info' => 'Check server logs for details'
    ]);
}
?>
