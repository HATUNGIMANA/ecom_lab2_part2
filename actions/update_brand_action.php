<?php
require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

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
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $brandController = new BrandController();
    $user_id = $_SESSION['customer_id'] ?? $_SESSION['id'] ?? 0;
    
    $kwargs = [
        'brand_id' => intval($input['brand_id'] ?? 0),
        'brand_name' => trim($input['brand_name'] ?? ''),
        'user_id' => intval($user_id)
    ];
    
    $result = $brandController->update_brand_ctr($kwargs);
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating brand'
    ]);
}
?>
