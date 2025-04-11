<?php
include '../../../BackEnd/Config/config.php';
header('Content-Type: application/json');

// Chuẩn bị câu truy vấn để lấy tất cả các bản ghi từ bảng category, JOIN với category_type để lấy type_name
$sql = "SELECT c.category_id, c.category_name, c.category_description, c.status_id, c.category_type_id, ct.type_name 
        FROM category c 
        LEFT JOIN category_type ct ON c.category_type_id = ct.category_type_id 
        WHERE c.status_id IN (1, 2);";
$result = $conn->query($sql);

if ($result) {
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'category_id' => $row['category_id'],
            'category_name' => $row['category_name'],
            'category_description' => $row['category_description'],
            'status_id' => $row['status_id'],
            'category_type_id' => $row['category_type_id'],
            'type_name' => $row['type_name'] // Tên của chủng loại
        ];
    }

    // Trả về JSON với trạng thái success
    echo json_encode([
        'status' => 'success',
        'data' => $categories
    ]);
} else {
    // Trả về lỗi nếu truy vấn thất bại
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch categories: ' . $conn->error
    ]);
}

$conn->close();
?>