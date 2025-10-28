<?php
session_start();
require_once __DIR__ . '/classes/category_class.php';

// Preload categories for display after login (kept for compatibility)
$all_categories = [];
try {
	$categoryModel = new Category();
	$all_categories = $categoryModel->getAllCategories() ?: [];
} catch (Exception $e) {
	$all_categories = [];
}

// Define the primary food categories to display (added Lunch, Local Meals, Fruits)
$featuredCategories = [
    [
        'slug' => 'dinner',
        'title' => 'Dinner',
        'description' => 'Hearty meals and main courses perfect for evenings.',
        'icon' => 'fa-utensils',
    ],
    [
        'slug' => 'breakfast',
        'title' => 'Breakfast',
        'description' => 'Start your day right with wholesome breakfast options.',
        'icon' => 'fa-bacon',
    ],
    [
        'slug' => 'desserts',
        'title' => 'Desserts',
        'description' => 'Sweet treats and desserts to finish your meal delightfully.',
        'icon' => 'fa-ice-cream',
    ],
    [
        'slug' => 'lunch',
        'title' => 'Lunch',
        'description' => 'Light and fulfilling midday meals to keep you going.',
        'icon' => 'fa-burger',
    ],
    [
        'slug' => 'local-meals',
        'title' => 'Local Meals',
        'description' => 'Authentic local dishes showcasing regional flavors.',
        'icon' => 'fa-bowl-food',
    ],
    [
        'slug' => 'fruits',
        'title' => 'Fruits',
        'description' => 'Fresh fruits and fruit-based snacks to refresh your palate.',
        'icon' => 'fa-apple-whole',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home - Taste of Africa</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<style>
		.menu-tray {
			position: fixed;
			top: 16px;
			right: 16px;
			background: rgba(255,255,255,0.95);
			border: 1px solid #e6e6e6;
			border-radius: 8px;
			padding: 6px 10px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.06);
			z-index: 1000;
		}
		.menu-tray a { margin-left: 8px; }
		.btn-custom {
			background-color: #D19C97;
			border-color: #D19C97;
			color: #fff;
		}
		.btn-custom:hover {
			background-color: #b77a7a;
			border-color: #b77a7a;
			color: #fff;
		}
		.welcome-message {
			background: linear-gradient(135deg, #D19C97, #b77a7a);
			color: white;
			padding: 2rem;
			border-radius: 15px;
			margin-bottom: 2rem;
		}
		.category-card .fa-3x {
			color: #D19C97;
		}
		.category-card .card-body {
			min-height: 160px;
		}
	</style>
</head>
<body>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
			<span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?>!</span>
			<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
				<a href="admindsh.php" class="btn btn-sm btn-warning">
					<i class="fas fa-tachometer-alt me-1"></i>Admin Dashboard
				</a>
				<a href="admin/category.php" class="btn btn-sm btn-info">
					<i class="fas fa-tags me-1"></i>Category
				</a>
				<a href="admin/brand.php" class="btn btn-sm btn-success">
					<i class="fas fa-store me-1"></i>Brand
				</a>
				<a href="admin/product.php" class="btn btn-sm btn-primary">
					<i class="fas fa-plus-circle me-1"></i>Add Product
				</a>
			<?php endif; ?>
			<a href="login/logout.php" class="btn btn-sm btn-custom">
				<i class="fas fa-sign-out-alt me-1"></i>Logout
			</a>
	<?php else: ?>
		<a href="login/register.php" class="btn btn-sm btn-outline-primary">
			<i class="fas fa-user-plus me-1"></i>Register
		</a>
		<a href="login/login.php" class="btn btn-sm btn-outline-secondary">
			<i class="fas fa-sign-in-alt me-1"></i>Login
		</a>
	<?php endif; ?>
	
	<!-- Search Box -->
	<div class="mt-3">
		<form action="product_search_result.php" method="GET" class="d-flex">
			<input type="text" name="query" class="form-control form-control-sm me-2" 
			       placeholder="Search products..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
			<button type="submit" class="btn btn-sm btn-outline-primary">
				<i class="fas fa-search"></i>
			</button>
		</form>
	</div>
	
	<!-- All Products Link -->
	<a href="all_product.php" class="btn btn-sm btn-info mt-2">
		<i class="fas fa-shopping-bag me-1"></i>All Products
	</a>
</div>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">
			<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
				<div class="welcome-message">
					<h1><i class="fas fa-home me-2"></i>Welcome to Taste of Africa!</h1>
					<p class="mb-0">Hello <?php echo htmlspecialchars($_SESSION['customer_name']); ?>, you are successfully logged in.</p>
					<?php if (isset($_SESSION['login_time'])): ?>
						<small class="opacity-75">Login time: <?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?></small>
					<?php endif; ?>
				</div>

				<!-- Food Categories Section -->
				<div class="mt-5 text-start">
					<h2 class="mb-3"><i class="fas fa-utensils me-2"></i>Food Categories</h2>
					<p class="text-muted">Explore by category, find what you crave.</p>

					<div class="row g-4">
						<?php foreach ($featuredCategories as $cat): ?>
							<div class="col-12 col-sm-6 col-md-4">
								<div class="card category-card h-100 border-0 shadow-sm">
									<div class="card-body d-flex flex-column justify-content-between text-center">
										<div>
											<i class="fas <?php echo $cat['icon']; ?> fa-3x mb-3"></i>
											<h5 class="card-title mb-2"><?php echo htmlspecialchars($cat['title']); ?></h5>
											<p class="card-text text-muted"><?php echo htmlspecialchars($cat['description']); ?></p>
										</div>
										<div class="mt-3">
											<!-- Button disabled for logged-in users -->
											<button class="btn btn-custom btn-sm" disabled>View items</button>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

			<?php else: ?>
				<h1><i class="fas fa-home me-2"></i>Welcome to Taste of Africa!</h1>
				<p class="text-muted">Please use the menu in the top-right to Register or Login.</p>

				<div class="mt-4">
					<h3 class="mb-3">Explore Categories</h3>
					<div class="row g-4">
						<?php foreach ($featuredCategories as $cat): ?>
							<div class="col-12 col-sm-6 col-md-4">
								<div class="card h-100 border-0 shadow-sm">
									<div class="card-body text-center">
										<i class="fas <?php echo $cat['icon']; ?> fa-2x mb-2 text-secondary"></i>
										<h6 class="mb-1"><?php echo htmlspecialchars($cat['title']); ?></h6>
										<p class="text-muted small mb-3"><?php echo htmlspecialchars($cat['description']); ?></p>
										<a href="login/login.php" class="btn btn-outline-primary btn-sm">View items</a>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
