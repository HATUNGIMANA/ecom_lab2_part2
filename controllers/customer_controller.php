<?php

require_once __DIR__ . '/../classes/customer_class.php';

/**
 * Customer Controller for handling customer-related operations
 */
class CustomerController
{
    /**
     * Login customer controller method
     * @param array $kwargs - array containing email and password
     * @return array
     */
    public function login_customer_ctr($kwargs)
    {
        $email = $kwargs['email'] ?? '';
        $password = $kwargs['password'] ?? '';
        
        // Validate input
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Email and password are required'
            ];
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Please enter a valid email address'
            ];
        }
        
        try {
            $customer = new Customer();
            $customerData = $customer->verifyLogin($email, $password);
            
            if ($customerData) {
                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'customer' => $customerData
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred during login. Please try again.'
            ];
        }
    }
    
    /**
     * Register customer controller method
     * @param array $kwargs - array containing customer data
     * @return array
     */
    public function register_customer_ctr($kwargs)
    {
        $name = $kwargs['name'] ?? '';
        $email = $kwargs['email'] ?? '';
        $password = $kwargs['password'] ?? '';
        $phone_number = $kwargs['phone_number'] ?? '';
        $country = $kwargs['country'] ?? '';
        $city = $kwargs['city'] ?? '';
        $role = $kwargs['role'] ?? 1; // Default role is 1 (customer)
        
        // Validate input
        if (empty($name) || empty($email) || empty($password) || empty($phone_number) || empty($country) || empty($city)) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Please enter a valid email address'
            ];
        }
        
        // Validate password strength
        if (strlen($password) < 6) {
            return [
                'success' => false,
                'message' => 'Password must be at least 6 characters long'
            ];
        }
        
        try {
            $customer = new Customer();
            
            // Check if email already exists
            $existingCustomer = $customer->getCustomerByEmail(strtolower(trim($email)));
            if ($existingCustomer) {
                return [
                    'success' => false,
                    'message' => 'Email address is already registered'
                ];
            }
            
            $customerId = $customer->createCustomer($name, strtolower(trim($email)), $password, $phone_number, $country, $city, $role);
            
            if ($customerId) {
                return [
                    'success' => true,
                    'message' => 'Registration successful! Welcome to Taste of Africa.',
                    'customer_id' => $customerId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Registration failed. Please try again.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred during registration. Please try again.'
            ];
        }
    }
}
