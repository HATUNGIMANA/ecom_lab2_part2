<?php
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

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
$cat_id = $input['cat_id'] ?? 0;
$cat_name = $input['cat_name'] ?? '';
$user_id = $_SESSION['customer_id'] ?? $_SESSION['id'] ?? 0;

// Validate required fields
if (empty($cat_id) || empty($cat_name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Category ID and name are required'
    ]);
    exit;
}

try {
    // Create category controller instance
    $categoryController = new CategoryController();
    
    // Prepare arguments for update method
    $kwargs = [
        'cat_id' => intval($cat_id),
        'cat_name' => trim($cat_name),
        'user_id' => intval($user_id)
    ];
    
    // Call update method
    $result = $categoryController->update_category_ctr($kwargs);
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating the category. Please try again.'
    ]);
}
?>
