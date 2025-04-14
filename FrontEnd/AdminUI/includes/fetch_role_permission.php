<?php
// Start session
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

// Include database connection
require_once __DIR__ . '/../../../BackEnd/Config/config.php';

// Get the user's role ID
$userId = $_SESSION['user_id'];
$sql = "SELECT role_id FROM account WHERE account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
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

// Return the permissions as JSON
echo json_encode([
    'success' => true,
    'permissions' => $permissions,
    'permissionActions' => $permissionActions
]);
?> 