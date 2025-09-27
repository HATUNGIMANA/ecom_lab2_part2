<?php
require_once 'settings/core.php';
require_once 'settings/db_class.php';

// Admin credentials
define('ADMIN_EMAIL', 'admin@ashesi.edu.gh');
define('ADMIN_PASSWORD', 'Adm!n123++Ecom');

/**
 * Admin authentication function
 */
function authenticateAdmin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_email']) && isset($_POST['admin_password'])) {
        $email = trim($_POST['admin_email']);
        $password = $_POST['admin_password'];
        
        if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
            // Set admin session
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_login_time'] = time();
            return true;
        }
    }
    
    // Check if already logged in as admin
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        return true;
    }
    
    return false;
}

/**
 * Get website statistics
 */
function getWebsiteStats() {
    try {
        $db = new db_connection();
        if (!$db->db_connect()) {
            return ['error' => 'Database connection failed'];
        }
        
        // Get total users
        $totalUsers = $db->db_fetch_one("SELECT COUNT(*) as total FROM customer");
        $totalCount = $totalUsers ? $totalUsers['total'] : 0;
        
        // Get customers (role = 2)
        $customers = $db->db_fetch_one("SELECT COUNT(*) as count FROM customer WHERE user_role = 2");
        $customerCount = $customers ? $customers['count'] : 0;
        
        // Get restaurant owners (role = 3)
        $owners = $db->db_fetch_one("SELECT COUNT(*) as count FROM customer WHERE user_role = 3");
        $ownerCount = $owners ? $owners['count'] : 0;
        
        // Get recent registrations (last 10 users by ID)
        $recentUsers = $db->db_fetch_all("
            SELECT customer_id FROM customer 
            ORDER BY customer_id DESC 
            LIMIT 10
        ");
        $recentCount = count($recentUsers);
        
        // Get country distribution
        $countryStats = $db->db_fetch_all("
            SELECT customer_country, COUNT(*) as count 
            FROM customer 
            GROUP BY customer_country 
            ORDER BY count DESC 
            LIMIT 10
        ");
        
        return [
            'total_users' => $totalCount,
            'customers' => $customerCount,
            'restaurant_owners' => $ownerCount,
            'recent_registrations' => $recentCount,
            'country_stats' => $countryStats ?: []
        ];
    } catch (Exception $e) {
        return ['error' => 'Failed to fetch statistics: ' . $e->getMessage()];
    }
}

// Check authentication
$isAuthenticated = authenticateAdmin();

if (!$isAuthenticated) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Taste of Africa</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
            }
            .login-container {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(10px);
            }
            .admin-icon {
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="login-container p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-shield-alt fa-3x admin-icon mb-3"></i>
                            <h2 class="admin-icon">Admin Dashboard</h2>
                            <p class="text-muted">Taste of Africa - Administrative Access</p>
                        </div>
                        
                        <?php if (isset($_POST['admin_email'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Invalid credentials. Please try again.
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Admin Email
                                </label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                            </div>
                            <div class="mb-4">
                                <label for="admin_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Back to Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}

// Get statistics
$stats = getWebsiteStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            border-left: 5px solid;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.users { border-left-color: #007bff; }
        .stat-card.customers { border-left-color: #28a745; }
        .stat-card.owners { border-left-color: #ffc107; }
        .stat-card.recent { border-left-color: #17a2b8; }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-tachometer-alt me-3"></i>Admin Dashboard</h1>
                    <p class="mb-0">Taste of Africa - Website Statistics & Analytics</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end align-items-center gap-3">
                        <small>
                            <i class="fas fa-user-shield me-2"></i>
                            Logged in as: <?php echo htmlspecialchars($_SESSION['admin_email']); ?>
                        </small>
                        <a href="?logout=1" class="btn btn-sm logout-btn">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                        <a href="index.php" class="btn btn-sm btn-light">
                            <i class="fas fa-home me-2"></i>Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <?php if (isset($stats['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($stats['error']); ?>
            </div>
        <?php else: ?>
            <!-- Statistics Cards -->
            <div class="row mb-5">
                <div class="col-md-3 mb-4">
                    <div class="stat-card users p-4 text-center">
                        <i class="fas fa-users stat-icon text-primary mb-3"></i>
                        <h3 class="stat-number text-primary"><?php echo number_format($stats['total_users']); ?></h3>
                        <h6 class="text-muted">Total Users</h6>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card customers p-4 text-center">
                        <i class="fas fa-shopping-cart stat-icon text-success mb-3"></i>
                        <h3 class="stat-number text-success"><?php echo number_format($stats['customers']); ?></h3>
                        <h6 class="text-muted">Customers</h6>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card owners p-4 text-center">
                        <i class="fas fa-store stat-icon text-warning mb-3"></i>
                        <h3 class="stat-number text-warning"><?php echo number_format($stats['restaurant_owners']); ?></h3>
                        <h6 class="text-muted">Restaurant Owners</h6>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card recent p-4 text-center">
                        <i class="fas fa-user-plus stat-icon text-info mb-3"></i>
                        <h3 class="stat-number text-info"><?php echo number_format($stats['recent_registrations']); ?></h3>
                        <h6 class="text-muted">Recent Signups</h6>
                    </div>
                </div>
            </div>

            <!-- Country Distribution -->
            <?php if (!empty($stats['country_stats'])): ?>
            <div class="row">
                <div class="col-12">
                    <div class="table-container p-4">
                        <h4 class="mb-4">
                            <i class="fas fa-globe me-2"></i>User Distribution by Country
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-flag me-2"></i>Country</th>
                                        <th><i class="fas fa-users me-2"></i>Users</th>
                                        <th><i class="fas fa-chart-bar me-2"></i>Percentage</th>
                                        <th><i class="fas fa-chart-line me-2"></i>Visual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['country_stats'] as $country): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($country['customer_country']); ?></strong></td>
                                        <td><?php echo number_format($country['count']); ?></td>
                                        <td><?php echo round(($country['count'] / $stats['total_users']) * 100, 1); ?>%</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?php echo round(($country['count'] / $stats['total_users']) * 100, 1); ?>%"
                                                     aria-valuenow="<?php echo $country['count']; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="<?php echo $stats['total_users']; ?>">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Additional Information -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="table-container p-4">
                        <h4 class="mb-4">
                            <i class="fas fa-info-circle me-2"></i>System Information
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-server me-2"></i>Database Status</span>
                                        <span class="badge bg-success">Connected</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-clock me-2"></i>Last Updated</span>
                                        <span><?php echo date('Y-m-d H:i:s'); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-shield-alt me-2"></i>Admin Session</span>
                                        <span class="badge bg-primary">Active</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-user-tie me-2"></i>Admin Email</span>
                                        <span><?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-sign-in-alt me-2"></i>Login Time</span>
                                        <span><?php echo date('H:i:s', $_SESSION['admin_login_time']); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-database me-2"></i>Database</span>
                                        <span><?php echo DATABASE; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>

<?php
// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_destroy();
    header('Location: admindsh.php');
    exit;
}
?>
