/**
 * Login Form Validation and AJAX Submission
 */

$(document).ready(function() {
    // Form validation and submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous error messages
        clearErrorMessages();
        
        // Get form data
        const email = $('#email').val().trim();
        const password = $('#password').val();
        
        // Validate form
        if (!validateLoginForm(email, password)) {
            return false;
        }
        
        // Show loading state
        showLoadingState();
        
        // Prepare data for AJAX request
        const formData = {
            email: email,
            password: password
        };
        
        // Make AJAX request
        $.ajax({
            url: '../actions/login_customer_action.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                hideLoadingState();
                
                if (response.success) {
                    // Show success message
                    showSuccessMessage(response.message);
                    
                    // Redirect to index page after 2 seconds
                    setTimeout(function() {
                        window.location.href = '../index.php';
                    }, 2000);
                } else {
                    // Show error message
                    showErrorMessage(response.message);
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState();
                console.error('AJAX Error:', error);
                showErrorMessage('An error occurred. Please try again.');
            }
        });
    });
    
    // Real-time validation
    $('#email').on('blur', function() {
        const email = $(this).val().trim();
        if (email && !isValidEmail(email)) {
            showFieldError('email', 'Please enter a valid email address');
        } else {
            clearFieldError('email');
        }
    });
    
    $('#password').on('blur', function() {
        const password = $(this).val();
        if (password && password.length < 6) {
            showFieldError('password', 'Password must be at least 6 characters long');
        } else {
            clearFieldError('password');
        }
    });
});

/**
 * Validate login form
 * @param {string} email
 * @param {string} password
 * @returns {boolean}
 */
function validateLoginForm(email, password) {
    let isValid = true;
    
    // Validate email
    if (!email) {
        showFieldError('email', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showFieldError('email', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password
    if (!password) {
        showFieldError('password', 'Password is required');
        isValid = false;
    } else if (password.length < 6) {
        showFieldError('password', 'Password must be at least 6 characters long');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Validate email format using regex
 * @param {string} email
 * @returns {boolean}
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Show field-specific error message
 * @param {string} fieldName
 * @param {string} message
 */
function showFieldError(fieldName, message) {
    const field = $('#' + fieldName);
    field.addClass('is-invalid');
    
    // Remove existing error message
    field.siblings('.invalid-feedback').remove();
    
    // Add new error message
    field.after('<div class="invalid-feedback">' + message + '</div>');
}

/**
 * Clear field-specific error message
 * @param {string} fieldName
 */
function clearFieldError(fieldName) {
    const field = $('#' + fieldName);
    field.removeClass('is-invalid');
    field.siblings('.invalid-feedback').remove();
}

/**
 * Clear all error messages
 */
function clearErrorMessages() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.alert').remove();
}

/**
 * Show success message
 * @param {string} message
 */
function showSuccessMessage(message) {
    const alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
        '<i class="fas fa-check-circle me-2"></i>' + message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';
    
    $('#login-form').before(alertHtml);
}

/**
 * Show error message
 * @param {string} message
 */
function showErrorMessage(message) {
    const alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        '<i class="fas fa-exclamation-circle me-2"></i>' + message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';
    
    $('#login-form').before(alertHtml);
}

/**
 * Show loading state
 */
function showLoadingState() {
    const submitBtn = $('#login-form button[type="submit"]');
    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Logging in...');
}

/**
 * Hide loading state
 */
function hideLoadingState() {
    const submitBtn = $('#login-form button[type="submit"]');
    submitBtn.prop('disabled', false);
    submitBtn.html('Login');
}
