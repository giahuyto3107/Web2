<?php
include('../../../BackEnd/Config/config.php');
session_start();
header('Content-Type: application/json');

// Simulate user ID from session (replace with your actual authentication logic)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Fetch user's role_id and permissions
$query = "
    SELECT rp.permission_id 
    FROM users u 
    JOIN role_permission rp ON u.role_id = rp.role_id 
    WHERE u.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$permissions = [];
while ($row = $result->fetch_assoc()) {
    $permissions[] = $row['permission_id'];
}

echo json_encode([
    'success' => true,
    'permissions' => $permissions
]);

$stmt->close();
$conn->close();
?>