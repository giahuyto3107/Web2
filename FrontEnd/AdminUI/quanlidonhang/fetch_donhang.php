<?php
include '../../../BackEnd/Config/config.php';
header('Content-Type: application/json');

// Chuẩn bị câu truy vấn để lấy tất cả các bản ghi từ bảng orders
$sql = "SELECT o.order_id, u.full_name AS user_name, o.order_date, o.total_amount, o.status_id, o.payment_method, o.phone, o.address
        FROM orders o
        LEFT JOIN user u ON o.user_id = u.user_id";
$result = $conn->query($sql);

if ($result) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            'order_id' => $row['order_id'],
            'user_name' => $row['user_name'],
            'order_date' => $row['order_date'],
            'total_amount' => $row['total_amount'],
            'status_id' => $row['status_id'],
            'payment_method' => $row['payment_method'],
            'phone' => $row['phone'],
            'address' => $row['address']
        ];
    }

    // Trả về JSON với trạng thái success
    echo json_encode([
        'status' => 'success',
        'data' => $orders
    ]);
} else {
    // Trả về lỗi nếu truy vấn thất bại
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch orders: ' . $conn->error
    ]);
}

$conn->close();
?>