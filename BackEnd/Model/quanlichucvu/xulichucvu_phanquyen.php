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

// Start transaction for data integrity
$conn->begin_transaction();

try {
    // Delete existing permissions for the role
    $sql_delete = "DELETE FROM role_permission WHERE role_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $role_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Insert new permissions
    if (!empty($permissions)) {
        $sql_insert = "INSERT INTO role_permission (role_id, permission_id, action) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        foreach ($permissions as $permission_id => $actions) {
            $permission_id = intval($permission_id);
            foreach ($actions as $action => $value) {
                if ($value === 'on') { // Checkbox checked
                    $stmt_insert->bind_param("iis", $role_id, $permission_id, $action);
                    $stmt_insert->execute();
                }
            }
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
    // Rollback on error
    $conn->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Có lỗi khi phân quyền: ' . $e->getMessage()
    ]);
}

$conn->close();
?>