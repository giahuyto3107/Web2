<?php
include '../../../BackEnd/Config/config.php';
header('Content-Type: application/json');

// Chuẩn bị câu truy vấn để lấy tất cả các bản ghi từ bảng category
$sql = "SELECT category_id, category_name, category_description, status_id 
FROM category 
WHERE status_id IN (1, 2);";
$result = $conn->query($sql);

if ($result) {
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'category_id' => $row['category_id'],
            'category_name' => $row['category_name'],
            'category_description' => $row['category_description'],
            'status_id' => $row['status_id']
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