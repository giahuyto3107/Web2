<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

try {
    // Truy vấn tất cả nhà cung cấp từ bảng supplier
    $sql = "SELECT * FROM supplier WHERE status_id IN (1, 2);";
    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    $suppliers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = [
                'supplier_id' => $row['supplier_id'],
                'supplier_name' => $row['supplier_name'],
                'contact_phone' => $row['contact_phone'],
                'address' => $row['address'],
                'publisher' => $row['publisher'],
                'status_id' => $row['status_id']
            ];
        }
    }

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Lấy danh sách nhà cung cấp thành công',
        'data' => $suppliers
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