<?php
session_start();
include_once('../Config/config.php');

header("Content-Type: application/json; charset=UTF-8");

// Function to check if a user has a specific permission
function hasPermission($conn, $userId, $permissionId) {
    // Get the user's role_id
    $sql = "SELECT role_id FROM account WHERE account_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false; // User not found
    }
    
    $user = $result->fetch_assoc();
    $roleId = $user['role_id'];
    
    // Check if the role has the permission
    $sql = "SELECT COUNT(*) as count FROM role_permission WHERE role_id = ? AND permission_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $roleId, $permissionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

// Function to get all permissions for a user
function getUserPermissions($conn, $userId) {
    // Get the user's role_id
    $sql = "SELECT role_id FROM account WHERE account_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return []; // User not found
    }
    
    $user = $result->fetch_assoc();
    $roleId = $user['role_id'];
    
    // Get all permissions for the role
    $sql = "SELECT p.permission_id, p.permission_name, p.permission_description 
            FROM permission p 
            JOIN role_permission rp ON p.permission_id = rp.permission_id 
            WHERE rp.role_id = ? AND p.status_id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row;
    }
    
    return $permissions;
}

// Handle API requests
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'check_permission' && isset($_GET['permission_id']) && isset($_SESSION['user_id'])) {
            $permissionId = $_GET['permission_id'];
            $userId = $_SESSION['user_id'];
            
            $hasPermission = hasPermission($conn, $userId, $permissionId);
            
            echo json_encode([
                "success" => true,
                "has_permission" => $hasPermission
            ]);
        } 
        else if ($_GET['action'] === 'get_permissions' && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            
            $permissions = getUserPermissions($conn, $userId);
            
            echo json_encode([
                "success" => true,
                "permissions" => $permissions
            ]);
        }
        else {
            echo json_encode([
                "success" => false,
                "message" => "Invalid request parameters"
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Action parameter is required"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Only GET requests are allowed"
    ]);
}
?> 