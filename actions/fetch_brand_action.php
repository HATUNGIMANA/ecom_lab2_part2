<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST method is allowed'
    ]);
    exit;
}

if (!is_logged_in()) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to perform this action'
    ]);
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Only administrators can manage brands'
    ]);
    exit;
}

try {
    $brandController = new BrandController();
    $user_id = $_SESSION['customer_id'] ?? $_SESSION['id'] ?? 0;
    
    $kwargs = ['user_id' => intval($user_id)];
    
    $result = $brandController->fetch_brands_ctr($kwargs);
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching brands'
    ]);
}
?>
