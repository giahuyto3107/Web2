<?php
/**
 * Role Management System
 * This file handles CRUD operations for roles and role permissions
 */

// Include database connection
require_once '../connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Function to get all roles
function getAllRoles($conn) {
    $query = "SELECT * FROM role ORDER BY role_id";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return [
            'success' => false,
            'message' => 'Error fetching roles: ' . mysqli_error($conn)
        ];
    }
    
    $roles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $roles[] = $row;
    }
    
    return [
        'success' => true,
        'roles' => $roles
    ];
}

// Function to get a single role by ID
function getRoleById($conn, $roleId) {
    $query = "SELECT * FROM role WHERE role_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $roleId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        return [
            'success' => false,
            'message' => 'Error fetching role: ' . mysqli_error($conn)
        ];
    }
    
    $role = mysqli_fetch_assoc($result);
    
    if (!$role) {
        return [
            'success' => false,
            'message' => 'Role not found'
        ];
    }
    
    return [
        'success' => true,
        'role' => $role
    ];
}

// Function to create a new role
function createRole($conn, $roleName, $description) {
    // Check if role name already exists
    $checkQuery = "SELECT * FROM role WHERE role_name = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "s", $roleName);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        return [
            'success' => false,
            'message' => 'Role name already exists'
        ];
    }
    
    // Insert new role
    $query = "INSERT INTO role (role_name, description) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $roleName, $description);
    
    if (!mysqli_stmt_execute($stmt)) {
        return [
            'success' => false,
            'message' => 'Error creating role: ' . mysqli_error($conn)
        ];
    }
    
    $roleId = mysqli_insert_id($conn);
    
    return [
        'success' => true,
        'message' => 'Role created successfully',
        'role_id' => $roleId
    ];
}

// Function to update a role
function updateRole($conn, $roleId, $roleName, $description) {
    // Check if role exists
    $checkQuery = "SELECT * FROM role WHERE role_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "i", $roleId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) === 0) {
        return [
            'success' => false,
            'message' => 'Role not found'
        ];
    }
    
    // Check if new role name already exists (if changed)
    $currentRole = mysqli_fetch_assoc($checkResult);
    if ($currentRole['role_name'] !== $roleName) {
        $nameCheckQuery = "SELECT * FROM role WHERE role_name = ? AND role_id != ?";
        $nameCheckStmt = mysqli_prepare($conn, $nameCheckQuery);
        mysqli_stmt_bind_param($nameCheckStmt, "si", $roleName, $roleId);
        mysqli_stmt_execute($nameCheckStmt);
        $nameCheckResult = mysqli_stmt_get_result($nameCheckStmt);
        
        if (mysqli_num_rows($nameCheckResult) > 0) {
            return [
                'success' => false,
                'message' => 'Role name already exists'
            ];
        }
    }
    
    // Update role
    $query = "UPDATE role SET role_name = ?, description = ? WHERE role_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $roleName, $description, $roleId);
    
    if (!mysqli_stmt_execute($stmt)) {
        return [
            'success' => false,
            'message' => 'Error updating role: ' . mysqli_error($conn)
        ];
    }
    
    return [
        'success' => true,
        'message' => 'Role updated successfully'
    ];
}

// Function to delete a role
function deleteRole($conn, $roleId) {
    // Check if role exists
    $checkQuery = "SELECT * FROM role WHERE role_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "i", $roleId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) === 0) {
        return [
            'success' => false,
            'message' => 'Role not found'
        ];
    }
    
    // Check if role is assigned to any users
    $userCheckQuery = "SELECT * FROM account WHERE role_id = ?";
    $userCheckStmt = mysqli_prepare($conn, $userCheckQuery);
    mysqli_stmt_bind_param($userCheckStmt, "i", $roleId);
    mysqli_stmt_execute($userCheckStmt);
    $userCheckResult = mysqli_stmt_get_result($userCheckStmt);
    
    if (mysqli_num_rows($userCheckResult) > 0) {
        return [
            'success' => false,
            'message' => 'Cannot delete role: It is assigned to one or more users'
        ];
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Delete role permissions
        $deletePermissionsQuery = "DELETE FROM role_permission WHERE role_id = ?";
        $deletePermissionsStmt = mysqli_prepare($conn, $deletePermissionsQuery);
        mysqli_stmt_bind_param($deletePermissionsStmt, "i", $roleId);
        mysqli_stmt_execute($deletePermissionsStmt);
        
        // Delete role
        $deleteRoleQuery = "DELETE FROM role WHERE role_id = ?";
        $deleteRoleStmt = mysqli_prepare($conn, $deleteRoleQuery);
        mysqli_stmt_bind_param($deleteRoleStmt, "i", $roleId);
        mysqli_stmt_execute($deleteRoleStmt);
        
        // Commit transaction
        mysqli_commit($conn);
        
        return [
            'success' => true,
            'message' => 'Role deleted successfully'
        ];
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        return [
            'success' => false,
            'message' => 'Error deleting role: ' . $e->getMessage()
        ];
    }
}

// Function to get all permissions for a role
function getRolePermissions($conn, $roleId) {
    $query = "SELECT p.* FROM permission p
              JOIN role_permission rp ON p.permission_id = rp.permission_id
              WHERE rp.role_id = ?
              ORDER BY p.permission_id";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $roleId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        return [
            'success' => false,
            'message' => 'Error fetching role permissions: ' . mysqli_error($conn)
        ];
    }
    
    $permissions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $permissions[] = $row;
    }
    
    return [
        'success' => true,
        'permissions' => $permissions
    ];
}

// Function to assign permissions to a role
function assignPermissionsToRole($conn, $roleId, $permissionIds) {
    // Check if role exists
    $checkQuery = "SELECT * FROM role WHERE role_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "i", $roleId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) === 0) {
        return [
            'success' => false,
            'message' => 'Role not found'
        ];
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Delete existing permissions
        $deleteQuery = "DELETE FROM role_permission WHERE role_id = ?";
        $deleteStmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "i", $roleId);
        mysqli_stmt_execute($deleteStmt);
        
        // Insert new permissions
        if (!empty($permissionIds)) {
            $insertQuery = "INSERT INTO role_permission (role_id, permission_id) VALUES (?, ?)";
            $insertStmt = mysqli_prepare($conn, $insertQuery);
            
            foreach ($permissionIds as $permissionId) {
                mysqli_stmt_bind_param($insertStmt, "ii", $roleId, $permissionId);
                mysqli_stmt_execute($insertStmt);
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        return [
            'success' => true,
            'message' => 'Permissions assigned successfully'
        ];
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        return [
            'success' => false,
            'message' => 'Error assigning permissions: ' . $e->getMessage()
        ];
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get_all_roles':
                echo json_encode(getAllRoles($conn));
                break;
                
            case 'get_role':
                if (isset($_GET['role_id'])) {
                    echo json_encode(getRoleById($conn, $_GET['role_id']));
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Role ID is required'
                    ]);
                }
                break;
                
            case 'get_role_permissions':
                if (isset($_GET['role_id'])) {
                    echo json_encode(getRolePermissions($conn, $_GET['role_id']));
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Role ID is required'
                    ]);
                }
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Action is required'
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'create_role':
                if (isset($data['role_name'])) {
                    $description = isset($data['description']) ? $data['description'] : '';
                    echo json_encode(createRole($conn, $data['role_name'], $description));
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Role name is required'
                    ]);
                }
                break;
                
            case 'update_role':
                if (isset($data['role_id']) && isset($data['role_name'])) {
                    $description = isset($data['description']) ? $data['description'] : '';
                    echo json_encode(updateRole($conn, $data['role_id'], $data['role_name'], $description));
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Role ID and role name are required'
                    ]);
                }
                break;
                
            case 'delete_role':
                if (isset($data['role_id'])) {
                    echo json_encode(deleteRole($conn, $data['role_id']));
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Role ID is required'
                    ]);
                }
                break;
                
            case 'assign_permissions':
                if (isset($data['role_id']) && isset($data['permission_ids'])) {
                    echo json_encode(assignPermissionsToRole($conn, $data['role_id'], $data['permission_ids']));
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Role ID and permission IDs are required'
                    ]);
                }
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Action is required'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

// Close database connection
mysqli_close($conn);
?> 