<?php
require_once '../settings/core.php';
require_once '../classes/brand_class.php';
require_once '../classes/category_class.php';

if (!is_logged_in()) {
    header('Location: ../login/login.php');
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header('Location: ../index.php');
    exit;
}

$admin_name = $_SESSION['customer_name'] ?? 'Administrator';

// Preload categories for dropdown
$categoryModel = new Category();
$all_categories = $categoryModel->getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .brand-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1rem;
            border-left: 5px solid #007bff;
        }
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
        .category-group {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-store me-3"></i>Brand Management</h1>
                    <p class="mb-0">Manage product brands for Taste of Africa</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end align-items-center gap-3">
                        <span><i class="fas fa-user-shield me-2"></i>Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
                        <a href="../admindsh.php" class="btn btn-sm btn-light"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a href="../index.php" class="btn btn-sm btn-light"><i class="fas fa-home me-2"></i>Website</a>
                        <a href="../login/logout.php" class="btn btn-sm btn-outline-light"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <!-- Add Brand Form -->
            <div class="col-md-4 mb-4">
                <div class="brand-card p-4">
                    <h4 class="mb-4"><i class="fas fa-plus-circle me-2"></i>Add New Brand</h4>
                    <form id="addBrandForm">
                        <div class="mb-3">
                            <label for="brandName" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brandName" name="brand_name" 
                                   placeholder="Enter brand name" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="brandCategory" class="form-label">Category</label>
                            <select class="form-select" id="brandCategory" name="brand_cat" required>
                                <option value="">Select Category</option>
                                <?php foreach ($all_categories as $cat): ?>
                                    <option value="<?php echo $cat['cat_id']; ?>">
                                        <?php echo htmlspecialchars($cat['cat_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-custom w-100" id="addBrandBtn">
                            <span id="addSpinner" class="spinner-border spinner-border-sm me-2" style="display:none;"></span>
                            <span id="addText">Add Brand</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Brands List -->
            <div class="col-md-8">
                <div class="brand-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4><i class="fas fa-list me-2"></i>Brands by Category</h4>
                        <button class="btn btn-outline-primary btn-sm" id="refreshBrands">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>
                    </div>
                    
                    <div id="brandsList">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading brands...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editBrandForm">
                    <div class="modal-body">
                        <input type="hidden" id="editBrandId" name="brand_id">
                        <div class="mb-3">
                            <label for="editBrandName" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="editBrandName" name="brand_name" maxlength="100" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="updateBrandBtn">
                            <span id="updateSpinner" class="spinner-border spinner-border-sm me-2" style="display:none;"></span>
                            <span id="updateText">Update Brand</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Delete Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the brand <strong id="deleteBrandName"></strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                    <input type="hidden" id="deleteBrandId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <span id="deleteSpinner" class="spinner-border spinner-border-sm me-2" style="display:none;"></span>
                        <span id="deleteText">Delete Brand</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/brand.js"></script>
</body>
</html>

