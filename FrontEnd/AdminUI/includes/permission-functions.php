<?php
/**
 * Permission Functions
 * This file contains functions for retrieving and checking user permissions
 */

// Prevent direct access to this file
if (!defined('ADMIN_ACCESS')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access forbidden.');
}

/**
 * Get all permissions for a user
 * 
 * @param int $userId The user ID
 * @return array An associative array of permissions organized by module
 */
function getUserPermissions($userId) {
    // Initialize an empty permissions array
    $permissions = [];
    
    // If user is not logged in, return empty permissions
    if ($userId <= 0) {
        return $permissions;
    }
    
    // Connect to the database
    require_once __DIR__ . '/../../BackEnd/Config/config.php';
    
    // Get the user's role ID
    $stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $roleId = $row['role_id'];
        
        // Get all permissions for this role
        $stmt = $conn->prepare("
            SELECT m.name as module_name, a.name as action_name
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            JOIN modules m ON p.module_id = m.id
            JOIN actions a ON p.action_id = a.id
            WHERE rp.role_id = ?
        ");
        $stmt->bind_param("i", $roleId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Organize permissions by module
        while ($row = $result->fetch_assoc()) {
            $moduleName = $row['module_name'];
            $actionName = $row['action_name'];
            
            // Initialize module array if it doesn't exist
            if (!isset($permissions[$moduleName])) {
                $permissions[$moduleName] = [];
            }
            
            // Add action to module
            $permissions[$moduleName][] = $actionName;
        }
    }
    
    // Close the database connection
    $stmt->close();
    $conn->close();
    
    return $permissions;
}

/**
 * Check if a user has a specific permission
 * 
 * @param int $userId The user ID
 * @param string $moduleName The module name
 * @param string $actionName The action name
 * @return bool True if the user has the permission, false otherwise
 */
function hasPermission($userId, $moduleName, $actionName) {
    $permissions = getUserPermissions($userId);
    
    // Check if the module exists in the permissions
    if (!isset($permissions[$moduleName])) {
        return false;
    }
    
    // Check if the action exists in the module
    return in_array($actionName, $permissions[$moduleName]);
}

/**
 * Get all modules that a user has access to
 * 
 * @param int $userId The user ID
 * @return array An array of module names
 */
function getUserModules($userId) {
    $permissions = getUserPermissions($userId);
    return array_keys($permissions);
}

/**
 * Get all actions that a user has for a specific module
 * 
 * @param int $userId The user ID
 * @param string $moduleName The module name
 * @return array An array of action names
 */
function getUserModuleActions($userId, $moduleName) {
    $permissions = getUserPermissions($userId);
    
    // Check if the module exists in the permissions
    if (!isset($permissions[$moduleName])) {
        return [];
    }
    
    return $permissions[$moduleName];
}

/**
 * Check if a user has access to a specific module
 * 
 * @param int $userId The user ID
 * @param string $moduleName The module name
 * @return bool True if the user has access to the module, false otherwise
 */
function hasModuleAccess($userId, $moduleName) {
    global $conn;
    
    // If no user ID is provided, return false
    if (!$userId) {
        return false;
    }
    
    // Get the user's role ID
    $stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $roleId = $row['role_id'];
        
        // Check if the role has any permissions for the specified module
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            JOIN modules m ON p.module_id = m.id
            WHERE rp.role_id = ? AND m.name = ?
        ");
        $stmt->bind_param("is", $roleId, $moduleName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['count'] > 0;
        }
    }
    
    return false;
}

/**
 * Get all actions a user can perform for a specific module
 * 
 * @param int $userId The user ID
 * @param string $moduleName The module name
 * @return array An array of action names
 */
function getUserActionsForModule($userId, $moduleName) {
    global $conn;
    
    // Initialize an empty actions array
    $actions = [];
    
    // If no user ID is provided, return empty array
    if (!$userId) {
        return $actions;
    }
    
    // Get the user's role ID
    $stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $roleId = $row['role_id'];
        
        // Get all actions for this role and module
        $stmt = $conn->prepare("
            SELECT a.name as action_name
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            JOIN modules m ON p.module_id = m.id
            JOIN actions a ON p.action_id = a.id
            WHERE rp.role_id = ? AND m.name = ?
        ");
        $stmt->bind_param("is", $roleId, $moduleName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Add each action to the array
        while ($row = $result->fetch_assoc()) {
            $actions[] = $row['action_name'];
        }
    }
    
    return $actions;
}
?> 