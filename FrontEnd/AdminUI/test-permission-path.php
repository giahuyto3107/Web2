<?php
// Define ADMIN_ACCESS to allow access to this file
define('ADMIN_ACCESS', true);

// Start session
session_start();

// Include the permission functions
require_once __DIR__ . '/includes/permission-functions.php';

// Get the user ID from the session
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Get the user's permissions
$permissions = getUserPermissions($userId);

// Output the permissions
echo "<h1>Permission Test</h1>";
echo "<h2>User ID: " . $userId . "</h2>";
echo "<h2>Permissions:</h2>";
echo "<pre>";
print_r($permissions);
echo "</pre>";

// Test the hasActionPermission function
echo "<h2>Permission Tests:</h2>";
$permissionTests = [
    [1, "Xem", "Can view products"],
    [1, "Thêm", "Can add products"],
    [1, "Sửa", "Can edit products"],
    [1, "Xóa", "Can delete products"]
];

echo "<table border='1'>";
echo "<tr><th>Permission ID</th><th>Action</th><th>Result</th></tr>";
foreach ($permissionTests as $test) {
    $permissionId = $test[0];
    $action = $test[1];
    $description = $test[2];
    
    $hasPermission = hasActionPermission($userId, $permissionId, $action);
    $result = $hasPermission ? "Yes" : "No";
    
    echo "<tr>";
    echo "<td>" . $permissionId . "</td>";
    echo "<td>" . $action . " (" . $description . ")</td>";
    echo "<td>" . $result . "</td>";
    echo "</tr>";
}
echo "</table>";

// Output the path information
echo "<h2>Path Information:</h2>";
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$permissionPath = $basePath . '/includes/fetch_role_permission.php';

echo "<p>Base URL: " . $baseUrl . "</p>";
echo "<p>Base Path: " . $basePath . "</p>";
echo "<p>Permission Path: " . $permissionPath . "</p>";
echo "<p>Full URL: " . $baseUrl . $permissionPath . "</p>";
?> 