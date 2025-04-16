<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

// Get the user's role ID
$userId = $_SESSION['user_id'];
$sql = "SELECT role_id FROM account WHERE account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found'
    ]);
    exit;
}

$user = $result->fetch_assoc();
$roleId = $user['role_id'];

// Return the role ID
echo json_encode([
    'status' => 'success',
    'role_id' => $roleId
]);

$stmt->close();
$conn->close();
?> 