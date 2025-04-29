<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

// Lấy dữ liệu từ POST
$category_id = $_POST['category_id'] ?? null;
$category_name = trim($_POST['category_name'] ?? '');
$category_description = trim($_POST['category_description'] ?? '');
$status_id = (int)($_POST['status_id'] ?? 1);
$category_type_id = (int)($_POST['category_type_id'] ?? 0);

// Xử lý dựa trên dữ liệu nhận được
if ($category_id) {
    // Kiểm tra xem category_id có tồn tại không
    $check_id_sql = "SELECT category_name FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($check_id_sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Thể loại không tồn tại!']);
        exit;
    }

    if (isset($_POST['status_id']) && $_POST['status_id'] == 6) {
        // Trường hợp xóa (cập nhật status_id thành 6 và xóa các bản ghi liên quan trong product_category)
        
        // Bắt đầu giao dịch
        $conn->begin_transaction();

        try {
            // Xóa các bản ghi trong bảng product_category liên quan đến category_id
            $delete_product_category_sql = "DELETE FROM product_category WHERE category_id = ?";
            $stmt = $conn->prepare($delete_product_category_sql);
            $stmt->bind_param("i", $category_id);
            $stmt->execute();

            // Cập nhật status_id trong bảng category
            $update_category_sql = "UPDATE category SET status_id = ? WHERE category_id = ?";
            $stmt = $conn->prepare($update_category_sql);
            $stmt->bind_param("ii", $status_id, $category_id);
            $stmt->execute();

            // Commit giao dịch nếu không có lỗi
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Thể loại đã được đánh dấu xóa và các liên kết sản phẩm đã được xóa!']);
        } catch (Exception $e) {
            // Rollback giao dịch nếu có lỗi
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi xóa thể loại: ' . $e->getMessage()]);
        }

        $stmt->close();
    } else {
        // Trường hợp sửa (cập nhật thông tin)
        // Kiểm tra tên thể loại không được để trống
        if (empty($category_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên thể loại không được để trống!']);
            exit;
        }

        // Kiểm tra category_type_id
        if ($category_type_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng chọn chủng loại!']);
            exit;
        }

        // Kiểm tra category_type_id có tồn tại không
        $check_type_sql = "SELECT category_type_id FROM category_type WHERE category_type_id = ? AND status_id IN (1, 2)";
        $stmt = $conn->prepare($check_type_sql);
        $stmt->bind_param("i", $category_type_id);
        $stmt->execute();
        $type_result = $stmt->get_result();
        if ($type_result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Chủng loại không tồn tại hoặc không hoạt động!']);
            exit;
        }

        // Chuyển status_id thành số nguyên và kiểm tra giá trị hợp lệ
        if ($status_id != 1 && $status_id != 2) {
            echo json_encode(['status' => 'error', 'message' => 'Trạng thái phải là 1 (Active) hoặc 2 (Inactive)!']);
            exit;
        }

        // Kiểm tra trùng lặp tên, ngoại trừ chính nó
        $current_name = $result->fetch_assoc()['category_name'];
        if ($category_name !== $current_name) {
            $check_sql = "SELECT category_name FROM category WHERE category_name = ? AND category_id != ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("si", $category_name, $category_id);
            $stmt->execute();
            $check_result = $stmt->get_result();

            if ($check_result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Tên thể loại đã tồn tại! Vui lòng chọn tên khác.']);
                exit;
            }
        }

        // Cập nhật thông tin thể loại
        $sql = "UPDATE category SET category_name = ?, category_description = ?, status_id = ?, category_type_id = ? WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $category_name, $category_description, $status_id, $category_type_id, $category_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Thể loại đã được cập nhật thành công!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi cập nhật thể loại: ' . $conn->error]);
        }
        $stmt->close();
    }
} else {
    // Trường hợp thêm mới (INSERT)
    // Kiểm tra tên thể loại không được để trống
    if (empty($category_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Tên thể loại không được để trống!']);
        exit;
    }

    // Kiểm tra category_type_id
    if ($category_type_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng chọn chủng loại!']);
        exit;
    }

    // Kiểm tra category_type_id có tồn tại không
    $check_type_sql = "SELECT category_type_id FROM category_type WHERE category_type_id = ? AND status_id IN (1, 2)";
    $stmt = $conn->prepare($check_type_sql);
    $stmt->bind_param("i", $category_type_id);
    $stmt->execute();
    $type_result = $stmt->get_result();
    if ($type_result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Chủng loại không tồn tại hoặc không hoạt động!']);
        exit;
    }

    // Chuyển status_id thành số nguyên và kiểm tra giá trị hợp lệ
    if ($status_id != 1 && $status_id != 2) {
        echo json_encode(['status' => 'error', 'message' => 'Trạng thái phải là 1 (Active) hoặc 2 (Inactive)!']);
        exit;
    }

    // Kiểm tra trùng lặp tên

    $check_sql = "SELECT category_name FROM category WHERE category_name = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Tên thể loại đã tồn tại! Vui lòng chọn tên khác.']);
        exit;
    }

    // Thêm thể loại vào bảng category
    $sql = "INSERT INTO category (category_name, category_description, status_id, category_type_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $category_name, $category_description, $status_id, $category_type_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Thể loại đã được thêm thành công!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi thêm thể loại: ' . $conn->error]);
    }
    $stmt->close();
}

$conn->close();
?>