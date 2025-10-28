<?php
require_once __DIR__ . '/../settings/db_class.php';

class Product extends db_connection
{
    private $product_id;
    private $product_cat;
    private $product_brand;
    private $product_title;
    private $product_price;
    private $product_desc;
    private $product_image;
    private $product_keywords;

    public function __construct($product_id = null)
    {
        parent::db_connect();
        if ($product_id) {
            $this->product_id = $product_id;
            $this->loadProduct();
        }
    }

    private function loadProduct()
    {
        if (!$this->product_id) return false;
        
        $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            $this->product_cat = $result['product_cat'];
            $this->product_brand = $result['product_brand'];
            $this->product_title = $result['product_title'];
            $this->product_price = $result['product_price'];
            $this->product_desc = $result['product_desc'];
            $this->product_image = $result['product_image'];
            $this->product_keywords = $result['product_keywords'];
            return true;
        }
        return false;
    }

    public function getProductById($product_id)
    {
        $stmt = $this->db->prepare("SELECT p.*, c.cat_name, b.brand_name 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.product_cat = c.cat_id 
                                   LEFT JOIN brands b ON p.product_brand = b.brand_id 
                                   WHERE p.product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function view_all_products($limit = 0, $offset = 0)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.product_id DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function search_products($query)
    {
        $search = "%" . $query . "%";
        $stmt = $this->db->prepare("SELECT p.*, c.cat_name, b.brand_name 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.product_cat = c.cat_id 
                                   LEFT JOIN brands b ON p.product_brand = b.brand_id 
                                   WHERE p.product_title LIKE ? OR p.product_keywords LIKE ? 
                                   ORDER BY p.product_id DESC");
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function filter_products_by_category($cat_id)
    {
        $stmt = $this->db->prepare("SELECT p.*, c.cat_name, b.brand_name 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.product_cat = c.cat_id 
                                   LEFT JOIN brands b ON p.product_brand = b.brand_id 
                                   WHERE p.product_cat = ? 
                                   ORDER BY p.product_id DESC");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function filter_products_by_brand($brand_id)
    {
        $stmt = $this->db->prepare("SELECT p.*, c.cat_name, b.brand_name 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.product_cat = c.cat_id 
                                   LEFT JOIN brands b ON p.product_brand = b.brand_id 
                                   WHERE p.product_brand = ? 
                                   ORDER BY p.product_id DESC");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function view_single_product($id)
    {
        return $this->getProductById($id);
    }

    public function composite_search($query = '', $cat_id = 0, $brand_id = 0, $max_price = 0)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($query)) {
            $sql .= " AND (p.product_title LIKE ? OR p.product_keywords LIKE ?)";
            $search = "%" . $query . "%";
            $params[] = $search;
            $params[] = $search;
            $types .= "ss";
        }
        
        if ($cat_id > 0) {
            $sql .= " AND p.product_cat = ?";
            $params[] = $cat_id;
            $types .= "i";
        }
        
        if ($brand_id > 0) {
            $sql .= " AND p.product_brand = ?";
            $params[] = $brand_id;
            $types .= "i";
        }
        
        if ($max_price > 0) {
            $sql .= " AND p.product_price <= ?";
            $params[] = $max_price;
            $types .= "d";
        }
        
        $sql .= " ORDER BY p.product_id DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addProduct($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by)
    {
        $stmt = $this->db->prepare("INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdsssi", $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by);
        
        if ($stmt->execute()) return $this->db->insert_id;
        return false;
    }

    public function getProductCount($query = '', $cat_id = 0, $brand_id = 0)
    {
        $sql = "SELECT COUNT(*) as count FROM products WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($query)) {
            $sql .= " AND (product_title LIKE ? OR product_keywords LIKE ?)";
            $search = "%" . $query . "%";
            $params[] = $search;
            $params[] = $search;
            $types .= "ss";
        }
        
        if ($cat_id > 0) {
            $sql .= " AND product_cat = ?";
            $params[] = $cat_id;
            $types .= "i";
        }
        
        if ($brand_id > 0) {
            $sql .= " AND product_brand = ?";
            $params[] = $brand_id;
            $types .= "i";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['count'] : 0;
    }
}
?>

