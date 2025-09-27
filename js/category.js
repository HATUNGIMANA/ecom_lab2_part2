$(document).ready(function() {
    // Load categories on page load
    loadCategories();

    // Add Category Form Submission
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault();
        addCategory();
    });

    // Edit Category Form Submission
    $('#editCategoryForm').submit(function(e) {
        e.preventDefault();
        updateCategory();
    });

    // Delete Category Confirmation
    $('#confirmDeleteBtn').click(function() {
        deleteCategory();
    });

    // Refresh Categories Button
    $('#refreshCategories').click(function() {
        loadCategories();
    });

    // Load categories from server
    function loadCategories() {
        showLoading('categoriesList');
        
        $.ajax({
            url: '../actions/fetch_category_action.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading('categoriesList');
                
                if (response.success) {
                    displayCategories(response.categories);
                } else {
                    showError('categoriesList', response.message);
                }
            },
            error: function(xhr, status, error) {
                hideLoading('categoriesList');
                console.error('AJAX Error:', error);
                showError('categoriesList', 'Failed to load categories. Please try again.');
            }
        });
    }

    // Display categories in the list
    function displayCategories(categories) {
        const categoriesList = $('#categoriesList');
        
        if (categories.length === 0) {
            categoriesList.html(`
                <div class="empty-state">
                    <i class="fas fa-tags"></i>
                    <h5>No categories found</h5>
                    <p>Start by adding your first category using the form on the left.</p>
                </div>
            `);
            return;
        }

        let html = '<div class="row">';
        
        categories.forEach(function(category) {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-1">${escapeHtml(category.cat_name)}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Created: ${formatDate(category.created_at)}
                                    </small>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-edit" 
                                            onclick="editCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')"
                                            title="Edit Category">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-delete" 
                                            onclick="confirmDelete(${category.cat_id}, '${escapeHtml(category.cat_name)}')"
                                            title="Delete Category">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        categoriesList.html(html);
    }

    // Add new category
    function addCategory() {
        const formData = {
            cat_name: $('#categoryName').val().trim()
        };

        if (!validateCategoryName(formData.cat_name)) {
            return;
        }

        showLoadingState('addCategoryBtn', 'addSpinner', 'addText', 'Adding...');

        $.ajax({
            url: '../actions/add_category_action.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                hideLoadingState('addCategoryBtn', 'addSpinner', 'addText', 'Add Category');
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#addCategoryForm')[0].reset();
                    loadCategories();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState('addCategoryBtn', 'addSpinner', 'addText', 'Add Category');
                console.error('AJAX Error:', error);
                
                let errorMessage = 'Failed to add category. Please try again.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Use default error message
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    }

    // Update category
    function updateCategory() {
        const formData = {
            cat_id: $('#editCategoryId').val(),
            cat_name: $('#editCategoryName').val().trim()
        };

        if (!validateCategoryName(formData.cat_name)) {
            return;
        }

        showLoadingState('updateCategoryBtn', 'updateSpinner', 'updateText', 'Updating...');

        $.ajax({
            url: '../actions/update_category_action.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                hideLoadingState('updateCategoryBtn', 'updateSpinner', 'updateText', 'Update Category');
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#editCategoryModal').modal('hide');
                    loadCategories();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState('updateCategoryBtn', 'updateSpinner', 'updateText', 'Update Category');
                console.error('AJAX Error:', error);
                
                let errorMessage = 'Failed to update category. Please try again.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Use default error message
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    }

    // Delete category
    function deleteCategory() {
        const categoryId = $('#deleteCategoryId').val();

        showLoadingState('confirmDeleteBtn', 'deleteSpinner', 'deleteText', 'Deleting...');

        $.ajax({
            url: '../actions/delete_category_action.php',
            type: 'POST',
            data: JSON.stringify({ cat_id: categoryId }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                hideLoadingState('confirmDeleteBtn', 'deleteSpinner', 'deleteText', 'Delete Category');
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#deleteCategoryModal').modal('hide');
                    loadCategories();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState('confirmDeleteBtn', 'deleteSpinner', 'deleteText', 'Delete Category');
                console.error('AJAX Error:', error);
                
                let errorMessage = 'Failed to delete category. Please try again.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Use default error message
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    }

    // Validate category name
    function validateCategoryName(name) {
        if (!name || name.length < 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Input',
                text: 'Category name must be at least 2 characters long.'
            });
            return false;
        }
        
        if (name.length > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Input',
                text: 'Category name must be less than 100 characters.'
            });
            return false;
        }
        
        return true;
    }

    // Show loading state
    function showLoadingState(buttonId, spinnerId, textId, loadingText) {
        $('#' + buttonId).prop('disabled', true);
        $('#' + spinnerId).show();
        $('#' + textId).text(loadingText);
    }

    // Hide loading state
    function hideLoadingState(buttonId, spinnerId, textId, originalText) {
        $('#' + buttonId).prop('disabled', false);
        $('#' + spinnerId).hide();
        $('#' + textId).text(originalText);
    }

    // Show loading in container
    function showLoading(containerId) {
        $('#' + containerId).html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading...</p>
            </div>
        `);
    }

    // Hide loading and show error
    function showError(containerId, message) {
        $('#' + containerId).html(`
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${escapeHtml(message)}
            </div>
        `);
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
});

// Global functions for onclick handlers
function editCategory(categoryId, categoryName) {
    $('#editCategoryId').val(categoryId);
    $('#editCategoryName').val(categoryName);
    $('#editCategoryModal').modal('show');
}

function confirmDelete(categoryId, categoryName) {
    $('#deleteCategoryId').val(categoryId);
    $('#deleteCategoryName').text(categoryName);
    $('#deleteCategoryModal').modal('show');
}
