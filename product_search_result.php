<?php
session_start();
require_once 'classes/product_class.php';
require_once 'classes/category_class.php';
require_once 'classes/brand_class.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$filter_cat = isset($_GET['category']) ? intval($_GET['category']) : 0;
$filter_brand = isset($_GET['brand']) ? intval($_GET['brand']) : 0;

$productModel = new Product();
$categoryModel = new Category();
$brandModel = new Brand();

// Perform search
$products = $productModel->composite_search($query, $filter_cat, $filter_brand, 0);

$all_categories = $categoryModel->getAllCategories();
$all_brands = $brandModel->getAllBrands();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-search me-2"></i>Search Results</h1>
        
        <?php if (!empty($query)): ?>
            <p>Showing results for "<strong><?php echo htmlspecialchars($query); ?></strong>"</p>
        <?php endif; ?>

        <!-- Refine Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="product_search_result.php" class="row g-3">
                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category">
                            <option value="0">All Categories</option>
                            <?php foreach ($all_categories as $cat): ?>
                                <option value="<?php echo $cat['cat_id']; ?>" <?php echo ($filter_cat == $cat['cat_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <select class="form-select" name="brand">
                            <option value="0">All Brands</option>
                            <?php foreach ($all_brands as $brand): ?>
                                <option value="<?php echo $brand['brand_id']; ?>" <?php echo ($filter_brand == $brand['brand_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Refine Results</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>No products found matching your search criteria.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo htmlspecialchars($product['product_image'] ?? 'https://via.placeholder.com/300'); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?php echo htmlspecialchars($product['product_title']); ?></h6>
                                <p class="text-muted small mb-2">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($product['cat_name'] ?? 'N/A'); ?><br>
                                    <strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?>
                                </p>
                                <p class="text-primary fw-bold fs-5">$<?php echo number_format($product['product_price'], 2); ?></p>
                                <div class="mt-auto">
                                    <a href="single_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

