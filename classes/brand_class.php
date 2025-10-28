<?php

require_once __DIR__ . '/../settings/db_class.php';

class Brand extends db_connection
{
    private $brand_id;
    private $brand_name;
    private $brand_cat;
    private $created_by;
    private $created_at;

    public function __construct($brand_id = null)
    {
        parent::db_connect();
        if ($brand_id) {
            $this->brand_id = $brand_id;
            $this->loadBrand();
        }
    }

    private function loadBrand($brand_id = null)
    {
        if ($brand_id) $this->brand_id = $brand_id;
        if (!$this->brand_id) return false;
        
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $this->brand_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            $this->brand_name = $result['brand_name'];
            $this->brand_cat = $result['brand_cat'];
            $this->created_by = $result['created_by'];
            $this->created_at = $result['created_at'];
            return true;
        }
        return false;
    }

    public function getBrandById($brand_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getBrandsByCategory($category_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_cat = ? ORDER BY brand_name ASC");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllBrands()
    {
        $stmt = $this->db->prepare("SELECT * FROM brands ORDER BY brand_name ASC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addBrand($brand_name, $brand_cat, $created_by)
    {
        if ($this->brandNameExistsInCategory($brand_name, $brand_cat)) return false;

        $stmt = $this->db->prepare("INSERT INTO brands (brand_name, brand_cat, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $brand_name, $brand_cat, $created_by);
        
        if ($stmt->execute()) return $this->db->insert_id;
        return false;
    }

    public function updateBrand($brand_id, $new_name, $user_id)
    {
        $brand = $this->getBrandById($brand_id);
        if (!$brand || $brand['created_by'] != $user_id) return false;

        $stmt = $this->db->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ? AND created_by = ?");
        $stmt->bind_param("sii", $new_name, $brand_id, $user_id);
        return $stmt->execute();
    }

    public function deleteBrand($brand_id, $user_id)
    {
        $brand = $this->getBrandById($brand_id);
        if (!$brand || $brand['created_by'] != $user_id) return false;

        $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ? AND created_by = ?");
        $stmt->bind_param("ii", $brand_id, $user_id);
        return $stmt->execute();
    }

    public function brandNameExistsInCategory($brand_name, $category_id, $exclude_id = null)
    {
        if ($exclude_id) {
            $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND brand_cat = ? AND brand_id != ?");
            $stmt->bind_param("sii", $brand_name, $category_id, $exclude_id);
        } else {
            $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND brand_cat = ?");
            $stmt->bind_param("si", $brand_name, $category_id);
        }
        
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function getBrandsGroupedByCategory()
    {
        $stmt = $this->db->prepare("SELECT b.*, c.cat_name 
                                   FROM brands b 
                                   LEFT JOIN categories c ON b.brand_cat = c.cat_id 
                                   ORDER BY c.cat_name ASC, b.brand_name ASC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getBrandId() { return $this->brand_id; }
    public function getBrandName() { return $this->brand_name; }
    public function getBrandCat() { return $this->brand_cat; }
    public function getCreatedBy() { return $this->created_by; }
    public function getCreatedAt() { return $this->created_at; }
}
?>

