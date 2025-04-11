<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

try {
    $sql = "SELECT category_id, category_name FROM category WHERE status_id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'data' => $categories
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi khi lấy danh sách thể loại: ' . $e->getMessage()
    ]);
}

$conn->close();
?>