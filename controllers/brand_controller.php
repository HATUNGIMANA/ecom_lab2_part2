<?php

require_once __DIR__ . '/../classes/brand_class.php';

/**
 * Brand Controller for handling brand-related operations
 */
class BrandController
{
    /**
     * Add brand controller method
     */
    public function add_brand_ctr($kwargs)
    {
        $brand_name = $kwargs['brand_name'] ?? '';
        $brand_cat = $kwargs['brand_cat'] ?? 0;
        $created_by = $kwargs['created_by'] ?? 0;
        
        // Validate input
        if (empty($brand_name) || empty($brand_cat) || empty($created_by)) {
            return [
                'success' => false,
                'message' => 'Brand name, category, and user ID are required'
            ];
        }
        
        // Validate brand name
        $brand_name = trim($brand_name);
        if (strlen($brand_name) < 2 || strlen($brand_name) > 100) {
            return [
                'success' => false,
                'message' => 'Brand name must be between 2 and 100 characters'
            ];
        }
        
        // Validate category and user ID
        if (!is_numeric($brand_cat) || $brand_cat <= 0 || !is_numeric($created_by) || $created_by <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid category or user ID'
            ];
        }
        
        try {
            $brand = new Brand();
            
            // Check if brand name already exists in this category
            if ($brand->brandNameExistsInCategory($brand_name, $brand_cat)) {
                return [
                    'success' => false,
                    'message' => 'Brand name already exists in this category'
                ];
            }
            
            $brandId = $brand->addBrand($brand_name, $brand_cat, $created_by);
            
            if ($brandId) {
                return [
                    'success' => true,
                    'message' => 'Brand created successfully!',
                    'brand_id' => $brandId,
                    'brand_name' => $brand_name
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create brand'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while creating the brand'
            ];
        }
    }
    
    /**
     * Fetch brands controller method
     */
    public function fetch_brands_ctr($kwargs)
    {
        $user_id = $kwargs['user_id'] ?? 0;
        
        if (!is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user ID'
            ];
        }
        
        try {
            $brand = new Brand();
            $brands = $brand->getAllBrands();
            
            if ($brands !== false) {
                return [
                    'success' => true,
                    'message' => 'Brands fetched successfully',
                    'brands' => $brands,
                    'count' => count($brands)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch brands'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while fetching brands'
            ];
        }
    }
    
    /**
     * Update brand controller method
     */
    public function update_brand_ctr($kwargs)
    {
        $brand_id = $kwargs['brand_id'] ?? 0;
        $brand_name = $kwargs['brand_name'] ?? '';
        $user_id = $kwargs['user_id'] ?? 0;
        
        if (empty($brand_id) || empty($brand_name) || empty($user_id)) {
            return [
                'success' => false,
                'message' => 'Brand ID, name, and user ID are required'
            ];
        }
        
        if (!is_numeric($brand_id) || $brand_id <= 0 || !is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid brand or user ID'
            ];
        }
        
        $brand_name = trim($brand_name);
        if (strlen($brand_name) < 2 || strlen($brand_name) > 100) {
            return [
                'success' => false,
                'message' => 'Brand name must be between 2 and 100 characters'
            ];
        }
        
        try {
            $brand = new Brand();
            
            $result = $brand->updateBrand($brand_id, $brand_name, $user_id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Brand updated successfully!',
                    'brand_id' => $brand_id,
                    'brand_name' => $brand_name
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update brand'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while updating the brand'
            ];
        }
    }
    
    /**
     * Delete brand controller method
     */
    public function delete_brand_ctr($kwargs)
    {
        $brand_id = $kwargs['brand_id'] ?? 0;
        $user_id = $kwargs['user_id'] ?? 0;
        
        if (empty($brand_id) || empty($user_id)) {
            return [
                'success' => false,
                'message' => 'Brand ID and user ID are required'
            ];
        }
        
        if (!is_numeric($brand_id) || $brand_id <= 0 || !is_numeric($user_id) || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid brand or user ID'
            ];
        }
        
        try {
            $brand = new Brand();
            
            $brandData = $brand->getBrandById($brand_id);
            $brandName = $brandData ? $brandData['brand_name'] : 'Unknown';
            
            $result = $brand->deleteBrand($brand_id, $user_id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => "Brand '{$brandName}' deleted successfully!",
                    'brand_id' => $brand_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete brand'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while deleting the brand'
            ];
        }
    }
}
?>

