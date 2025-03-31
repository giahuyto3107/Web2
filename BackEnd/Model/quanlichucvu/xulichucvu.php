<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

$role_id = $_POST['id'] ?? null;
$role_name = trim($_POST['role_name'] ?? '');
$role_description = trim($_POST['role_description'] ?? '');
$status_id = (int)($_POST['status_id'] ?? 1);

if ($role_id) {
    // Kiểm tra sự tồn tại của chức vụ
    $check_id_sql = "SELECT role_name FROM role WHERE id = ?";
    $stmt = $conn->prepare($check_id_sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Chức vụ không tồn tại!']);
        exit;
    }

    // Xử lý xóa (đánh dấu status_id = 6)
    if (isset($_POST['status_id']) && $_POST['status_id'] == 6) {
        $sql = "UPDATE role SET status_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $status_id, $role_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Chức vụ đã được đánh dấu xóa!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi đánh dấu xóa: ' . $conn->error]);
        }
        $stmt->close();
    } else {
        // Xử lý cập nhật chức vụ
        if (empty($role_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên chức vụ không được để trống!']);
            exit;
        }

        // Kiểm tra trùng lặp role_name (trừ bản ghi hiện tại)
        $current_name = $result->fetch_assoc()['role_name'];
        if ($role_name !== $current_name) {
            $check_sql = "SELECT role_name FROM role WHERE role_name = ? AND id != ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("si", $role_name, $role_id);
            $stmt->execute();
            $check_result = $stmt->get_result();

            if ($check_result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Tên chức vụ đã tồn tại!']);
                exit;
            }
        }

        // Cập nhật thông tin chức vụ
        $sql = "UPDATE role SET role_name = ?, role_description = ?, status_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $role_name, $role_description, $status_id, $role_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Chức vụ đã được cập nhật thành công!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi cập nhật chức vụ: ' . $conn->error]);
        }
        $stmt->close();
    }
} else {
    // Xử lý thêm mới chức vụ
    if (empty($role_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Tên chức vụ không được để trống!']);
        exit;
    }

    // Kiểm tra trùng lặp role_name
    $check_sql = "SELECT role_name FROM role WHERE role_name = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $role_name);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Tên chức vụ đã tồn tại!']);
        exit;
    }

    // Thêm mới chức vụ
    $sql = "INSERT INTO role (role_name, role_description, status_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $role_name, $role_description, $status_id);

    if ($stmt->execute()) {
        $new_role_id = $conn->insert_id;

        // Xử lý quyền (nếu có)
        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            $insert_permission_sql = "INSERT INTO role_permission (role_id, permission_id) VALUES (?, ?)";
            $stmt_permission = $conn->prepare($insert_permission_sql);

            foreach ($_POST['permissions'] as $permission_id) {
                $permission_id = (int)$permission_id;
                $stmt_permission->bind_param("ii", $new_role_id, $permission_id);
                $stmt_permission->execute();
            }
            $stmt_permission->close();
        }

        echo json_encode(['status' => 'success', 'message' => 'Chức vụ đã được thêm thành công!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi thêm chức vụ: ' . $conn->error]);
    }
    $stmt->close();
}

$conn->close();
?>