<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

try {
    // Truy vấn tất cả chức vụ từ bảng role với điều kiện status_id là 1 hoặc 2
    $sql = "SELECT id, role_name, role_description, status_id 
            FROM role 
            WHERE status_id IN (1, 2)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        throw new Exception("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    $roles = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $roles[] = [
                'id' => $row['id'],
                'role_name' => $row['role_name'],
                'role_description' => $row['role_description'],
                'status_id' => $row['status_id']
            ];
        }
    }

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Lấy danh sách chức vụ thành công',
        'data' => $roles
    ]);

    $stmt->close();
} catch (Exception $e) {
    // Trả về lỗi nếu có
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'data' => []
    ]);
} finally {
    // Đóng kết nối
    $conn->close();
}
?>