<?php
/**
 * Fetch Role Permissions
 * This file returns the permissions for the current user's role
 */

session_start();
include_once('../../../BackEnd/Config/config.php');

header("Content-Type: application/json; charset=UTF-8");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// Get the user's role_id
$sql = "SELECT role_id FROM account WHERE account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "User not found"
    ]);
    exit;
}

$user = $result->fetch_assoc();
$roleId = $user['role_id'];

// Get all permissions for the role with their actions
$sql = "SELECT p.permission_id, rp.action 
        FROM permission p 
        JOIN role_permission rp ON p.permission_id = rp.permission_id 
        WHERE rp.role_id = ? AND p.status_id = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $roleId);
$stmt->execute();
$result = $stmt->get_result();

$permissions = [];
$permissionActions = [];

while ($row = $result->fetch_assoc()) {
    $permId = (int)$row['permission_id'];
    if (!in_array($permId, $permissions)) {
        $permissions[] = $permId;
    }
    if (!isset($permissionActions[$permId])) {
        $permissionActions[$permId] = [];
    }
    $permissionActions[$permId][] = $row['action'];
}

echo json_encode([
    "success" => true,
    "permissions" => $permissions,
    "permissionActions" => $permissionActions
]);
?>