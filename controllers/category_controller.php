<?php

require_once __DIR__ . '/../classes/category_class.php';

/**
 * Category Controller for handling category-related operations
 */
class CategoryController
{
    /**
     * Add category controller method
     * @param array $kwargs - array containing category data
     * @return array
     */
    public function add_category_ctr($kwargs)
    {
        $cat_name = $kwargs['cat_name'] ?? '';
        $created_by = $kwargs['created_by'] ?? 0;
        
        // Validate input
        if (empty($cat_name) || empty($created_by)) {
            return [
                'success' => false,
                'message' => 'Category name and user ID are required'
            ];
        }
        
        // Validate category name
        $cat_name = trim($cat_name);
        if (strlen($cat_name) < 2 || strlen($cat_name) > 100) {
            return [
                'success' => false,
                'message' => 'Category name must be between 2 and 100 characters'
            ];
        }
        
        // Validate user ID
        if (!is_numeric($created_by) || $created_by <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID'
            ];
        }
        
        try {
            $category = new Category();
            
            // Check if category name already exists
            if ($category->categoryNameExists($cat_name)) {
                return [
                    'success' => false,
                    'message' => 'Category name already exists. Please choose a different name.'
                ];
            }
            
            $categoryId = $category->addCategory($cat_name, $created_by);
            
            if ($categoryId) {
                return [
                    'success' => true,
                    'message' => 'Category created successfully!',
                    'category_id' => $categoryId,
                    'category_name' => $cat_name
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create category. Please try again.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while creating the category. Please try again.'
            ];
        }
    }
    
    /**
     * Fetch categories controller method
     * @param array $kwargs - array containing user ID
     * @return array
     */
    public function fetch_categories_ctr($kwargs)
    {
        $user_id = $kwargs['user_id'] ?? 0;
        
        // Validate user ID
        if (!is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID'
            ];
        }
        
        try {
            $category = new Category();
            $categories = $category->getCategoriesByUser($user_id);
            
            if ($categories !== false) {
                return [
                    'success' => true,
                    'message' => 'Categories fetched successfully',
                    'categories' => $categories,
                    'count' => count($categories)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch categories'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while fetching categories'
            ];
        }
    }
    
    /**
     * Update category controller method
     * @param array $kwargs - array containing category data
     * @return array
     */
    public function update_category_ctr($kwargs)
    {
        $cat_id = $kwargs['cat_id'] ?? 0;
        $cat_name = $kwargs['cat_name'] ?? '';
        $user_id = $kwargs['user_id'] ?? 0;
        
        // Validate input
        if (empty($cat_id) || empty($cat_name) || empty($user_id)) {
            return [
                'success' => false,
                'message' => 'Category ID, name, and user ID are required'
            ];
        }
        
        // Validate category ID
        if (!is_numeric($cat_id) || $cat_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid category ID'
            ];
        }
        
        // Validate category name
        $cat_name = trim($cat_name);
        if (strlen($cat_name) < 2 || strlen($cat_name) > 100) {
            return [
                'success' => false,
                'message' => 'Category name must be between 2 and 100 characters'
            ];
        }
        
        // Validate user ID
        if (!is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID'
            ];
        }
        
        try {
            $category = new Category();
            
            // Check if new category name already exists (excluding current category)
            if ($category->categoryNameExists($cat_name, $cat_id)) {
                return [
                    'success' => false,
                    'message' => 'Category name already exists. Please choose a different name.'
                ];
            }
            
            $result = $category->updateCategory($cat_id, $cat_name, $user_id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Category updated successfully!',
                    'category_id' => $cat_id,
                    'category_name' => $cat_name
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update category. Category may not exist or you may not have permission to edit it.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while updating the category. Please try again.'
            ];
        }
    }
    
    /**
     * Delete category controller method
     * @param array $kwargs - array containing category data
     * @return array
     */
    public function delete_category_ctr($kwargs)
    {
        $cat_id = $kwargs['cat_id'] ?? 0;
        $user_id = $kwargs['user_id'] ?? 0;
        
        // Validate input
        if (empty($cat_id) || empty($user_id)) {
            return [
                'success' => false,
                'message' => 'Category ID and user ID are required'
            ];
        }
        
        // Validate category ID
        if (!is_numeric($cat_id) || $cat_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid category ID'
            ];
        }
        
        // Validate user ID
        if (!is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID'
            ];
        }
        
        try {
            $category = new Category();
            
            // Get category name before deletion for confirmation message
            $categoryData = $category->getCategoryById($cat_id);
            $categoryName = $categoryData ? $categoryData['cat_name'] : 'Unknown';
            
            $result = $category->deleteCategory($cat_id, $user_id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => "Category '{$categoryName}' deleted successfully!",
                    'category_id' => $cat_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete category. Category may not exist or you may not have permission to delete it.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while deleting the category. Please try again.'
            ];
        }
    }
    
    /**
     * Get category count for a user
     * @param array $kwargs - array containing user ID
     * @return array
     */
    public function get_category_count_ctr($kwargs)
    {
        $user_id = $kwargs['user_id'] ?? 0;
        
        // Validate user ID
        if (!is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID'
            ];
        }
        
        try {
            $category = new Category();
            $count = $category->getCategoryCountByUser($user_id);
            
            return [
                'success' => true,
                'message' => 'Category count retrieved successfully',
                'count' => $count
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while getting category count'
            ];
        }
    }
}
?>
