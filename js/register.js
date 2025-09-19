$(document).ready(function() {
    $('#registerForm').submit(function(e) {
        e.preventDefault();

        // Show loading state
        showLoadingState();

        // Get form data
        const name = $('#full_name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        const country = $('#country').val();
        const city = $('#city').val().trim();
        const phoneNumber = $('#contact_number').val().trim();
        const role = $('input[name="user_role"]:checked').val();

        // Clear previous error messages
        clearErrorMessages();

        // Validate form
        if (!validateRegistrationForm(name, email, password, confirmPassword, country, city, phoneNumber)) {
            hideLoadingState();
            return false;
        }

        // Prepare data for AJAX request
        const formData = {
            name: name,
            email: email,
            password: password,
            phone_number: phoneNumber,
            country: country,
            city: city,
            role: role
        };

        // Make AJAX request
        $.ajax({
            url: '../actions/register_customer_action.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                hideLoadingState();
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: response.message,
                        confirmButtonText: 'Go to Login'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState();
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                
                let errorMessage = 'An error occurred during registration. Please try again.';
                
                // Try to parse error response
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // If response is not JSON, show the raw response
                    if (xhr.responseText) {
                        errorMessage = 'Server Error: ' + xhr.responseText.substring(0, 200);
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Error',
                    text: errorMessage,
                    footer: '<small>Check browser console for more details</small>'
                });
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

    $('#confirm_password').on('blur', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        if (confirmPassword && password !== confirmPassword) {
            showFieldError('confirm_password', 'Passwords do not match');
        } else {
            clearFieldError('confirm_password');
        }
    });
});

/**
 * Validate registration form
 */
function validateRegistrationForm(name, email, password, confirmPassword, country, city, phoneNumber) {
    let isValid = true;

    // Validate name
    if (!name) {
        showFieldError('full_name', 'Full name is required');
        isValid = false;
    }

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

    // Validate confirm password
    if (!confirmPassword) {
        showFieldError('confirm_password', 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showFieldError('confirm_password', 'Passwords do not match');
        isValid = false;
    }

    // Validate country
    if (!country) {
        showFieldError('country', 'Please select a country');
        isValid = false;
    }

    // Validate city
    if (!city) {
        showFieldError('city', 'City is required');
        isValid = false;
    }

    // Validate phone number
    if (!phoneNumber) {
        showFieldError('contact_number', 'Contact number is required');
        isValid = false;
    } else if (!isValidPhoneNumber(phoneNumber)) {
        showFieldError('contact_number', 'Please enter a valid phone number');
        isValid = false;
    }

    return isValid;
}

/**
 * Validate email format using regex
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number format
 */
function isValidPhoneNumber(phone) {
    const phoneRegex = /^[0-9+\-\s]{6,20}$/;
    return phoneRegex.test(phone);
}

/**
 * Show field-specific error message
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
}

/**
 * Show loading state
 */
function showLoadingState() {
    const submitBtn = $('#registerBtn');
    const spinner = $('#btnSpinner');
    const btnText = $('#btnText');
    
    submitBtn.prop('disabled', true);
    spinner.show();
    btnText.text('Registering...');
}

/**
 * Hide loading state
 */
function hideLoadingState() {
    const submitBtn = $('#registerBtn');
    const spinner = $('#btnSpinner');
    const btnText = $('#btnText');
    
    submitBtn.prop('disabled', false);
    spinner.hide();
    btnText.text('Register');
}