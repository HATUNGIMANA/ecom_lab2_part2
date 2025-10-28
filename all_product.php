<?php
session_start();
require_once 'classes/product_class.php';
require_once 'classes/category_class.php';
require_once 'classes/brand_class.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$productModel = new Product();
$categoryModel = new Category();
$brandModel = new Brand();

// Get filter parameters
$filter_cat = isset($_GET['category']) ? intval($_GET['category']) : 0;
$filter_brand = isset($_GET['brand']) ? intval($_GET['brand']) : 0;

// Fetch products based on filters
if ($filter_cat > 0) {
    $products = $productModel->filter_products_by_category($filter_cat);
} elseif ($filter_brand > 0) {
    $products = $productModel->filter_products_by_brand($filter_brand);
} else {
    $products = $productModel->view_all_products($per_page, $offset);
}

$total_products = $productModel->getProductCount('', $filter_cat, $filter_brand);
$total_pages = ceil($total_products / $per_page);

// Fetch all categories and brands for filters
$all_categories = $categoryModel->getAllCategories();
$all_brands = $brandModel->getAllBrands();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-shopping-bag me-2"></i>All Products</h1>
        
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="all_product.php" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Filter by Category</label>
                        <select class="form-select" name="category" id="category">
                            <option value="0">All Categories</option>
                            <?php foreach ($all_categories as $cat): ?>
                                <option value="<?php echo $cat['cat_id']; ?>" <?php echo ($filter_cat == $cat['cat_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="brand" class="form-label">Filter by Brand</label>
                        <select class="form-select" name="brand" id="brand">
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
                        <div>
                            <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter me-1"></i>Apply Filters</button>
                            <a href="all_product.php" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No products found.
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
                                    <button class="btn btn-success btn-sm w-100 mt-2" disabled>
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Product pagination">
                <ul class="pagination justify-content-center">
                    <?php
                    $query_params = $_GET;
                    for ($i = 1; $i <= $total_pages; $i++):
                        $query_params['page'] = $i;
                        $url = '?' . http_build_query($query_params);
                    ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo $url; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

