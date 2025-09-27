<?php
session_start();
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
	</div>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">
			<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
				<div class="welcome-message">
					<h1><i class="fas fa-home me-2"></i>Welcome to Taste of Africa!</h1>
					<p class="mb-0">Hello <?php echo htmlspecialchars($_SESSION['customer_name']); ?>, you are successfully logged in.</p>
					<small class="opacity-75">Login time: <?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?></small>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="card">
							<div class="card-body text-center">
								<i class="fas fa-user fa-3x text-primary mb-3"></i>
								<h5>Profile</h5>
								<p class="text-muted">Manage your account</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card">
							<div class="card-body text-center">
								<i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
								<h5>Shopping</h5>
								<p class="text-muted">Browse our products</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card">
							<div class="card-body text-center">
								<i class="fas fa-heart fa-3x text-danger mb-3"></i>
								<h5>Favorites</h5>
								<p class="text-muted">Your saved items</p>
							</div>
						</div>
					</div>
				</div>
			<?php else: ?>
				<h1><i class="fas fa-home me-2"></i>Welcome to Taste of Africa!</h1>
				<p class="text-muted">Please use the menu in the top-right to Register or Login.</p>
				<div class="row mt-4">
					<div class="col-md-6">
						<div class="card">
							<div class="card-body text-center">
								<i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
								<h5>New Customer?</h5>
								<p class="text-muted">Create an account to get started</p>
								<a href="login/register.php" class="btn btn-primary">Register Now</a>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card">
							<div class="card-body text-center">
								<i class="fas fa-sign-in-alt fa-3x text-secondary mb-3"></i>
								<h5>Existing Customer?</h5>
								<p class="text-muted">Sign in to your account</p>
								<a href="login/login.php" class="btn btn-secondary">Login</a>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
