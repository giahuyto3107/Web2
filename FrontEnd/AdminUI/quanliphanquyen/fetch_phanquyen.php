<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

try {
    // Truy vấn tất cả quyền từ bảng permission
    $sql = "SELECT p.permission_id,
                    p.permission_name,
                    p.permission_description,
                    p.status_id
                from permission p
                join status st on st.id = p.status_id
                ORDER BY p.permission_id ASC";
    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    $permissions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $permissions[] = [
                'permission_id' => $row['permission_id'],
                'permission_name' => $row['permission_name'],
                'permission_description' => $row['permission_description'],
                'status_id' => $row['status_id']
            ];
        }
    }

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Lấy danh sách quyền thành công',
        'data' => $permissions
    ]);
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