<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

// Tắt hiển thị lỗi PHP để tránh trả về HTML
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

try {
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    if ($order_id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid order ID'
        ]);
        exit;
    }

    // Lấy danh sách sản phẩm trong đơn hàng từ bảng order_items
    $sql = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $stmt->close();

    // Kiểm tra số lượng sản phẩm trong bảng product
    $insufficient_items = [];
    foreach ($items as $item) {
        $product_id = $item['product_id'];
        $required_quantity = $item['quantity'];

        $sql = "SELECT stock_quantity FROM product WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $available_quantity = $product['stock_quantity'];

            if ($available_quantity < $required_quantity) {
                $insufficient_items[] = "Sản phẩm ID $product_id: Yêu cầu $required_quantity, chỉ còn $available_quantity";
            }
        } else {
            $insufficient_items[] = "Sản phẩm ID $product_id không tồn tại hoặc đã bị xóa";
        }

        $stmt->close();
    }

    if (!empty($insufficient_items)) {
        echo json_encode([
            'status' => 'error',
            'message' => "Không đủ số lượng sản phẩm:\n" . implode("\n", $insufficient_items)
        ]);
        $conn->close();
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Đủ số lượng sản phẩm để duyệt đơn'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>