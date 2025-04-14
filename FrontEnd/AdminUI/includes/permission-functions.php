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

// Include database connection at the top level
require_once __DIR__ . '/../../../BackEnd/Config/config.php';
global $conn;

if (!function_exists('debug_log')) {
    function debug_log($message, $data = null) {
        error_log(sprintf(
            "[Permission Debug] %s %s",
            $message,
            $data !== null ? json_encode($data, JSON_UNESCAPED_UNICODE) : ''
        ));
    }
}

/**
 * Get all permissions for a user
 * 
 * @param int $userId The user ID
 * @return array An associative array of permissions organized by module
 */
function getUserPermissions($userId) {
    global $conn;
    
    debug_log("Getting permissions for user ID", $userId);
    
    if (!$userId) {
        debug_log("Invalid user ID provided", $userId);
        return [];
    }

    // Get user's role IDs
    $roleQuery = "SELECT role_id FROM account WHERE id = ?";
    $roleStmt = $conn->prepare($roleQuery);
    $roleStmt->bind_param("i", $userId);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();
    
    if ($roleResult->num_rows === 0) {
        debug_log("No roles found for user", $userId);
        return [];
    }
    
    $roleIds = [];
    while ($row = $roleResult->fetch_assoc()) {
        $roleIds[] = $row['role_id'];
    }
    
    debug_log("Found role IDs for user", $roleIds);
    
    // Get permissions for all roles
    $permissions = [];
    $permissionQuery = "
        SELECT DISTINCT 
            rp.permission_id,
            rp.action,
            p.module_name
        FROM role_permission rp
        JOIN permission p ON rp.permission_id = p.permission_id
        WHERE rp.role_id IN (" . implode(',', array_fill(0, count($roleIds), '?')) . ")";
    
    $permissionStmt = $conn->prepare($permissionQuery);
    if ($permissionStmt) {
        $permissionStmt->bind_param(str_repeat("i", count($roleIds)), ...$roleIds);
        $permissionStmt->execute();
        $permissionResult = $permissionStmt->get_result();
        
        while ($row = $permissionResult->fetch_assoc()) {
            $permId = $row['permission_id'];
            if (!isset($permissions[$permId])) {
                $permissions[$permId] = [
                    'module_name' => $row['module_name'],
                    'actions' => []
                ];
            }
            $permissions[$permId]['actions'][] = $row['action'];
        }
        
        debug_log("Retrieved permissions", $permissions);
    } else {
        debug_log("Failed to prepare permission query", $conn->error);
    }
    
    return $permissions;
}

/**
 * Check if a user has a specific permission
 * 
 * @param int $userId The user ID
 * @param int $permissionId The permission ID
 * @param string $actionName The action name
 * @return bool True if the user has the permission, false otherwise
 */
function hasPermission($userId, $permissionId, $action = null) {
    global $conn;
    
    debug_log("Checking permission", [
        'userId' => $userId,
        'permissionId' => $permissionId,
        'action' => $action
    ]);

    // Get user's role ID
    $roleQuery = "SELECT role_id FROM account WHERE id = ?";
    $roleStmt = $conn->prepare($roleQuery);
    $roleStmt->bind_param("i", $userId);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();
    
    if ($roleRow = $roleResult->fetch_assoc()) {
        $roleId = $roleRow['role_id'];
        debug_log("User role found", ['roleId' => $roleId]);
        
        // Get all actions for this permission and role
        $actionQuery = "SELECT DISTINCT action_name FROM role_permission 
                       WHERE role_id = ? AND permission_id = ?";
        $actionStmt = $conn->prepare($actionQuery);
        $actionStmt->bind_param("ii", $roleId, $permissionId);
        $actionStmt->execute();
        $actionResult = $actionStmt->get_result();
        
        $allowedActions = [];
        while ($row = $actionResult->fetch_assoc()) {
            $allowedActions[] = $row['action_name'];
        }
        
        debug_log("Allowed actions", $allowedActions);
        
        // If no specific action is required, return true if user has any actions
        if ($action === null) {
            $hasPermission = !empty($allowedActions);
            debug_log("No specific action required, checking if any actions exist", 
                     ['hasPermission' => $hasPermission]);
            return $hasPermission;
        }
        
        // Check if the specific action is allowed
        $hasPermission = in_array($action, $allowedActions);
        debug_log("Checking specific action", [
            'action' => $action,
            'hasPermission' => $hasPermission
        ]);
        return $hasPermission;
    }
    
    debug_log("User role not found", ['userId' => $userId]);
    return false;
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
 * Get all actions that a user has for a specific permission
 * 
 * @param int $userId The user ID
 * @param int $permissionId The permission ID
 * @return array An array of action names
 */
function getUserModuleActions($userId, $permissionId) {
    $permissions = getUserPermissions($userId);
    
    // Check if the permission exists in the permissions
    if (!isset($permissions[$permissionId])) {
        return [];
    }
    
    return $permissions[$permissionId]['actions'];
}

/**
 * Check if a user has access to a specific permission
 * 
 * @param int $userId The user ID
 * @param int $permissionId The permission ID
 * @return bool True if the user has access to the permission, false otherwise
 */
function hasModuleAccess($userId, $permissionId) {
    global $conn;
    
    // If no user ID is provided, return false
    if (!$userId) {
        return false;
    }
    
    // Get the user's role ID
    $stmt = $conn->prepare("SELECT role_id FROM account WHERE account_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $roleId = $row['role_id'];
        
        // Check if the role has any permissions for the specified permission
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM role_permission rp
            WHERE rp.role_id = ? AND rp.permission_id = ?
        ");
        $stmt->bind_param("ii", $roleId, $permissionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['count'] > 0;
        }
    }
    
    return false;
}

/**
 * Get all actions a user can perform for a specific permission
 * 
 * @param int $userId The user ID
 * @param int $permissionId The permission ID
 * @return array An array of action names
 */
function getUserActionsForModule($userId, $permissionId) {
    global $conn;
    
    // Initialize an empty actions array
    $actions = [];
    
    // If no user ID is provided, return empty array
    if (!$userId) {
        return $actions;
    }
    
    // Get the user's role ID
    $stmt = $conn->prepare("SELECT role_id FROM account WHERE account_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $roleId = $row['role_id'];
        
        // Get all actions for this role and permission
        $stmt = $conn->prepare("
            SELECT rp.action as action_name
            FROM role_permission rp
            WHERE rp.role_id = ? AND rp.permission_id = ?
        ");
        $stmt->bind_param("ii", $roleId, $permissionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Add each action to the array
        while ($row = $result->fetch_assoc()) {
            $actions[] = $row['action_name'];
        }
    }
    
    return $actions;
}

/**
 * Check if a user has a specific action for a permission
 * 
 * @param int $userId The user ID
 * @param int $permissionId The permission ID
 * @param string $actionName The action name
 * @return bool True if the user has the action, false otherwise
 */
function hasActionPermission($userId, $permissionId, $actionName) {
    global $conn;
    
    // If no user ID is provided, return false
    if (!$userId) {
        return false;
    }
    
    // Get the user's role ID
    $stmt = $conn->prepare("SELECT role_id FROM account WHERE account_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $roleId = $row['role_id'];
        
        // Check if the role has the specific action for the permission
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM role_permission rp
            WHERE rp.role_id = ? AND rp.permission_id = ? AND rp.action = ?
        ");
        $stmt->bind_param("iis", $roleId, $permissionId, $actionName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['count'] > 0;
        }
    }
    
    return false;
}
?> 