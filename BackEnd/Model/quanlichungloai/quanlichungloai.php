<?php
include '../../../BackEnd/Config/config.php';
header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'add') {
    // Thêm chủng loại
    $type_name = isset($_POST['type_name']) ? trim($_POST['type_name']) : '';
    $type_description = isset($_POST['type_description']) ? trim($_POST['type_description']) : '';
    $status_id = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 0;

    $errors = [];

    // Validate type_name
    if (empty($type_name)) {
        $errors['name'] = 'Tên chủng loại không được để trống';
    } elseif (!preg_match('/^[a-zA-Z\s-]+$/', $type_name)) {
        $errors['name'] = 'Tên chủng loại chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
    } elseif (strlen($type_name) > 100) {
        $errors['name'] = 'Tên chủng loại không được vượt quá 100 ký tự';
    }

    // Validate type_description
    if (strlen($type_description) > 400) {
        $errors['desc'] = 'Mô tả không được vượt quá 400 ký tự';
    }

    // Validate status_id
    if ($status_id !== 1 && $status_id !== 2) {
        $errors['status'] = 'Trạng thái không hợp lệ';
    }

    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'errors' => $errors
        ]);
        exit;
    }

    // Kiểm tra xem type_name đã tồn tại chưa
    $sql_check = "SELECT COUNT(*) FROM category_type WHERE type_name = ? AND status_id IN (1, 2)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $type_name);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        echo json_encode([
            'status' => 'error',
            'errors' => ['name' => 'Tên chủng loại đã tồn tại']
        ]);
        exit;
    }

    // Thêm chủng loại vào cơ sở dữ liệu
    $sql = "INSERT INTO category_type (type_name, type_description, status_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $type_name, $type_description, $status_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Chủng loại đã được thêm thành công'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Thêm chủng loại thất bại: ' . $stmt->error
        ]);
    }

    $stmt->close();
} elseif ($action === 'edit') {
    // Sửa chủng loại
    $category_type_id = isset($_POST['category_type_id']) ? (int)$_POST['category_type_id'] : 0;
    $type_name = isset($_POST['type_name']) ? trim($_POST['type_name']) : '';
    $type_description = isset($_POST['type_description']) ? trim($_POST['type_description']) : '';
    $status_id = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 0;

    $errors = [];

    // Validate category_type_id
    if ($category_type_id <= 0) {
        $errors['category_type_id'] = 'ID chủng loại không hợp lệ';
    }

    // Validate type_name
    if (empty($type_name)) {
        $errors['name'] = 'Tên chủng loại không được để trống';
    } elseif (!preg_match('/^[a-zA-Z\s-]+$/', $type_name)) {
        $errors['name'] = 'Tên chủng loại chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
    } elseif (strlen($type_name) > 100) {
        $errors['name'] = 'Tên chủng loại không được vượt quá 100 ký tự';
    }

    // Validate type_description
    if (strlen($type_description) > 400) {
        $errors['desc'] = 'Mô tả không được vượt quá 400 ký tự';
    }

    // Validate status_id
    if ($status_id !== 1 && $status_id !== 2) {
        $errors['status'] = 'Trạng thái không hợp lệ';
    }

    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'errors' => $errors
        ]);
        exit;
    }

    // Kiểm tra xem type_name đã tồn tại chưa (trừ bản ghi hiện tại)
    $sql_check = "SELECT COUNT(*) FROM category_type WHERE type_name = ? AND category_type_id != ? AND status_id IN (1, 2)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $type_name, $category_type_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        echo json_encode([
            'status' => 'error',
            'errors' => ['name' => 'Tên chủng loại đã tồn tại']
        ]);
        exit;
    }

    // Cập nhật chủng loại
    $sql = "UPDATE category_type SET type_name = ?, type_description = ?, status_id = ? WHERE category_type_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $type_name, $type_description, $status_id, $category_type_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Chủng loại đã được cập nhật thành công'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Cập nhật chủng loại thất bại: ' . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    // Xóa mềm chủng loại (cập nhật status_id thành 6)
    $category_type_id = isset($_POST['category_type_id']) ? (int)$_POST['category_type_id'] : 0;
    $status_id = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 0;

    if ($category_type_id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID chủng loại không hợp lệ'
        ]);
        exit;
    }

    if ($status_id !== 6) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Trạng thái không hợp lệ cho hành động xóa'
        ]);
        exit;
    }

    // Cập nhật status_id thành 6
    $sql = "UPDATE category_type SET status_id = ? WHERE category_type_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $status_id, $category_type_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Chủng loại đã được đánh dấu xóa thành công'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Xóa chủng loại thất bại: ' . $stmt->error
        ]);
    }

    $stmt->close();
}

$conn->close();
?>