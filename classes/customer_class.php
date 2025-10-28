<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * Customer Class for handling customer authentication and data
 */
class Customer extends db_connection
{
    private $customer_id;
    private $customer_name;
    private $customer_email;
    private $customer_pass;
    private $customer_contact;
    private $user_role;
    private $date_created;

    public function __construct($customer_id = null)
    {
        parent::db_connect();
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadCustomer();
        }
    }

    private function loadCustomer($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
        }
        if (!$this->customer_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->customer_name = $result['customer_name'];
            $this->customer_email = $result['customer_email'];
            $this->customer_pass = $result['customer_pass'];
            $this->customer_contact = $result['customer_contact'];
            $this->user_role = $result['user_role'];
            $this->date_created = isset($result['date_created']) ? $result['date_created'] : null;
        }
    }

    /**
     * Get customer by email address
     * @param string $email
     * @return array|false
     */
    public function getCustomerByEmail($email)
    {
        $normalized = strtolower(trim($email));
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $normalized);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Verify customer login credentials
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function verifyLogin($email, $password)
    {
        $customer = $this->getCustomerByEmail($email);
        
        if ($customer && password_verify($password, $customer['customer_pass'])) {
            // Remove password from returned data for security
            unset($customer['customer_pass']);
            return $customer;
        }
        
        return false;
    }

    /**
     * Create a new customer
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $phone_number
     * @param string $country
     * @param string $city
     * @param int $role
     * @return int|false
     */
    public function createCustomer($name, $email, $password, $phone_number, $country, $city, $role)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $normalized = strtolower(trim($email));
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $normalized, $hashed_password, $phone_number, $country, $city, $role);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Getters
    public function getCustomerId() { return $this->customer_id; }
    public function getCustomerName() { return $this->customer_name; }
    public function getCustomerEmail() { return $this->customer_email; }
    public function getCustomerContact() { return $this->customer_contact; }
    public function getUserRole() { return $this->user_role; }
    public function getDateCreated() { return $this->date_created; }
}
