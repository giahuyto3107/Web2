<?php
// Define ADMIN_ACCESS to allow access to this file
define('ADMIN_ACCESS', true);

// Start session
session_start();

// Get the base URL
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
$permissionPath = $basePath . '/includes/fetch_role_permission.php';

// Return the path as JSON
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'path' => $permissionPath,
    'fullUrl' => $baseUrl . $permissionPath
]); 