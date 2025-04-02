<?php
include '../../../BackEnd/Config/config.php';
header('Content-Type: application/json');

// Lấy order_id từ query string
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Mã đơn hàng không hợp lệ'
    ]);
    exit;
}

// Chuẩn bị câu truy vấn để lấy thông tin đơn hàng
$sql = "SELECT o.order_id, u.full_name AS user_name, o.order_date, o.total_amount, o.status_id, o.payment_method, o.phone, o.address
        FROM orders o
        LEFT JOIN user u ON o.user_id = u.user_id
        WHERE o.order_id = $order_id";
$result = $conn->query($sql);

if ($result) {
    $order_info = $result->fetch_assoc();

    if (!$order_info) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Không tìm thấy đơn hàng'
        ]);
        $conn->close();
        exit;
    }

    // Chuẩn bị câu truy vấn để lấy danh sách sản phẩm trong đơn hàng
    $sql_items = "SELECT oi.order_item_id, oi.product_id, p.product_name, oi.quantity, oi.price
                  FROM order_items oi
                  LEFT JOIN product p ON oi.product_id = p.product_id
                  WHERE oi.order_id = $order_id";
    $result_items = $conn->query($sql_items);

    if ($result_items) {
        $items = [];
        $total_value = 0;

        while ($row = $result_items->fetch_assoc()) {
            $items[] = [
                'order_item_id' => $row['order_item_id'],
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'price' => $row['price']
            ];
            $total_value += $row['quantity'] * $row['price'];
        }

        // Trả về JSON với trạng thái success
        echo json_encode([
            'status' => 'success',
            'data' => [
                'order_info' => [
                    'order_id' => $order_info['order_id'],
                    'user_name' => $order_info['user_name'],
                    'order_date' => $order_info['order_date'],
                    'total_amount' => $order_info['total_amount'],
                    'status_id' => $order_info['status_id'],
                    'payment_method' => $order_info['payment_method'],
                    'phone' => $order_info['phone'],
                    'address' => $order_info['address']
                ],
                'items' => $items,
                'total_value' => $total_value
            ]
        ]);
    } else {
        // Trả về lỗi nếu truy vấn danh sách sản phẩm thất bại
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch order items: ' . $conn->error
        ]);
    }
} else {
    // Trả về lỗi nếu truy vấn thông tin đơn hàng thất bại
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch order: ' . $conn->error
    ]);
}

$conn->close();
?>