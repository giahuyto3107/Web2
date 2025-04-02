<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

$role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : 0;
$permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

if ($role_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid role ID'
    ]);
    exit;
}

// Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
$conn->begin_transaction();

try {
    // Xóa tất cả quyền hiện tại của chức vụ
    $sql_delete = "DELETE FROM role_permission WHERE role_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $role_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Thêm các quyền mới được chọn
    if (!empty($permissions)) {
        $sql_insert = "INSERT INTO role_permission (role_id, permission_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        foreach ($permissions as $permission_id) {
            $permission_id = intval($permission_id);
            $stmt_insert->bind_param("ii", $role_id, $permission_id);
            $stmt_insert->execute();
        }
        $stmt_insert->close();
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Phân quyền thành công'
    ]);
} catch (Exception $e) {
    // Rollback transaction nếu có lỗi
    $conn->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Có lỗi khi phân quyền: ' . $e->getMessage()
    ]);
}

$conn->close();
?>