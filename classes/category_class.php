<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * Category Class for handling category CRUD operations
 */
class Category extends db_connection
{
    private $cat_id;
    private $cat_name;
    private $created_by;
    private $created_at;

    public function __construct($cat_id = null)
    {
        parent::db_connect();
        if ($cat_id) {
            $this->cat_id = $cat_id;
            $this->loadCategory();
        }
    }

    private function loadCategory($cat_id = null)
    {
        if ($cat_id) {
            $this->cat_id = $cat_id;
        }
        if (!$this->cat_id) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $this->cat_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            $this->cat_name = $result['cat_name'];
            $this->created_by = $result['created_by'];
            $this->created_at = $result['created_at'];
            return true;
        }
        return false;
    }

    /**
     * Get category by ID
     * @param int $cat_id
     * @return array|false
     */
    public function getCategoryById($cat_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get category by name
     * @param string $cat_name
     * @return array|false
     */
    public function getCategoryByName($cat_name)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_name = ?");
        $stmt->bind_param("s", $cat_name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get all categories created by a specific user
     * @param int $created_by
     * @return array|false
     */
    public function getCategoriesByUser($created_by)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE created_by = ? ORDER BY cat_name ASC");
        $stmt->bind_param("i", $created_by);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all categories
     * @return array|false
     */
    public function getAllCategories()
    {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY cat_name ASC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create a new category
     * @param string $cat_name
     * @param int $created_by
     * @return int|false
     */
    public function addCategory($cat_name, $created_by)
    {
        // Check if category name already exists
        if ($this->getCategoryByName($cat_name)) {
            return false; // Category name already exists
        }

        $stmt = $this->db->prepare("INSERT INTO categories (cat_name, created_by) VALUES (?, ?)");
        $stmt->bind_param("si", $cat_name, $created_by);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update category name
     * @param int $cat_id
     * @param string $new_name
     * @param int $user_id
     * @return bool
     */
    public function updateCategory($cat_id, $new_name, $user_id)
    {
        // Check if user owns this category
        $category = $this->getCategoryById($cat_id);
        if (!$category || $category['created_by'] != $user_id) {
            return false; // Category doesn't exist or user doesn't own it
        }

        // Check if new name already exists (excluding current category)
        $existing = $this->getCategoryByName($new_name);
        if ($existing && $existing['cat_id'] != $cat_id) {
            return false; // Category name already exists
        }

        $stmt = $this->db->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ? AND created_by = ?");
        $stmt->bind_param("sii", $new_name, $cat_id, $user_id);
        
        return $stmt->execute();
    }

    /**
     * Delete category
     * @param int $cat_id
     * @param int $user_id
     * @return bool
     */
    public function deleteCategory($cat_id, $user_id)
    {
        // Check if user owns this category
        $category = $this->getCategoryById($cat_id);
        if (!$category || $category['created_by'] != $user_id) {
            return false; // Category doesn't exist or user doesn't own it
        }

        $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ? AND created_by = ?");
        $stmt->bind_param("ii", $cat_id, $user_id);
        
        return $stmt->execute();
    }

    /**
     * Check if category name exists (excluding specific category ID)
     * @param string $cat_name
     * @param int $exclude_id
     * @return bool
     */
    public function categoryNameExists($cat_name, $exclude_id = null)
    {
        if ($exclude_id) {
            $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND cat_id != ?");
            $stmt->bind_param("si", $cat_name, $exclude_id);
        } else {
            $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ?");
            $stmt->bind_param("s", $cat_name);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Get category count for a user
     * @param int $user_id
     * @return int
     */
    public function getCategoryCountByUser($user_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM categories WHERE created_by = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['count'] : 0;
    }

    // Getters
    public function getCatId() { return $this->cat_id; }
    public function getCatName() { return $this->cat_name; }
    public function getCreatedBy() { return $this->created_by; }
    public function getCreatedAt() { return $this->created_at; }
}
?>
