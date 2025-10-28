<?php
session_start();
require_once 'classes/product_class.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: all_product.php');
    exit;
}

$productModel = new Product();
$product = $productModel->view_single_product($product_id);

if (!$product) {
    header('Location: all_product.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <a href="all_product.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-1"></i>Back to Products</a>
        
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($product['product_image'] ?? 'https://via.placeholder.com/500'); ?>" 
                     class="img-fluid rounded shadow" 
                     alt="<?php echo htmlspecialchars($product['product_title']); ?>">
            </div>
            <div class="col-md-6">
                <h2><?php echo htmlspecialchars($product['product_title']); ?></h2>
                <p class="text-muted">
                    <strong>Category:</strong> <?php echo htmlspecialchars($product['cat_name'] ?? 'N/A'); ?><br>
                    <strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?>
                </p>
                <h3 class="text-primary">$<?php echo number_format($product['product_price'], 2); ?></h3>
                <p class="mt-3"><?php echo htmlspecialchars($product['product_desc'] ?? 'No description available.'); ?></p>
                <?php if (!empty($product['product_keywords'])): ?>
                    <p><strong>Keywords:</strong> <?php echo htmlspecialchars($product['product_keywords']); ?></p>
                <?php endif; ?>
                <button class="btn btn-success btn-lg w-100 mt-3" disabled>
                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

