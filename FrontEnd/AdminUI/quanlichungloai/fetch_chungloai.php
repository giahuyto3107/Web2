<?php
include '../../../BackEnd/Config/config.php';
header('Content-Type: application/json');

// Chuẩn bị câu truy vấn để lấy tất cả các bản ghi từ bảng category_type
$sql = "SELECT category_type_id, type_name, type_description, status_id 
        FROM category_type 
        WHERE status_id IN (1, 2);";
$result = $conn->query($sql);

if ($result) {
    $category_types = [];
    while ($row = $result->fetch_assoc()) {
        $category_types[] = [
            'category_type_id' => $row['category_type_id'],
            'type_name' => $row['type_name'],
            'type_description' => $row['type_description'],
            'status_id' => $row['status_id']
        ];
    }

    // Trả về JSON với trạng thái success
    echo json_encode([
        'status' => 'success',
        'data' => $category_types
    ]);
} else {
    // Trả về lỗi nếu truy vấn thất bại
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch category types: ' . $conn->error
    ]);
}

$conn->close();
?>