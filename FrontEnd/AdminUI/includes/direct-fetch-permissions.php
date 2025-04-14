<?php
// Define ADMIN_ACCESS to allow access to this file
define('ADMIN_ACCESS', true);

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

// Include the database connection
require_once __DIR__ . '/../../../BackEnd/Config/config.php';

// Include the permission functions
require_once __DIR__ . '/permission-functions.php';

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Get the user's permissions
$permissions = getUserPermissions($userId);

// Return the permissions as JSON
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'permissions' => $permissions
]); 