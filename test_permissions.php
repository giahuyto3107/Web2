<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define ADMIN_ACCESS to bypass security check
define('ADMIN_ACCESS', true);

include 'FrontEnd/AdminUI/includes/permission-functions.php';

// Test with a sample user ID
$userId = 1;
$permissions = getUserPermissions($userId);

echo "Permissions for User ID: $userId\n";
var_dump($permissions);
?> 