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

try {
    // Create category controller instance
    $categoryController = new CategoryController();
    
    // Get user ID from session
    $user_id = $_SESSION['customer_id'] ?? $_SESSION['id'] ?? 0;
    
    // Prepare arguments for fetch method
    $kwargs = [
        'user_id' => intval($user_id)
    ];
    
    // Call fetch method
    $result = $categoryController->fetch_categories_ctr($kwargs);
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching categories. Please try again.'
    ]);
}
?>
