<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

$role_id = isset($_GET['role_id']) ? intval($_GET['role_id']) : 0;

if ($role_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid role ID'
    ]);
    exit;
}

// Lấy danh sách quyền đã gán cho chức vụ
$sql = "SELECT permission_id FROM role_permission WHERE role_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $role_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'status' => 'success',
    'data' => $data
]);

$stmt->close();
$conn->close();
?>