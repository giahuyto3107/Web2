<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

// Tắt hiển thị lỗi PHP để tránh trả về HTML
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'update_status') {
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $status_id = isset($_POST['status_id']) ? intval($_POST['status_id']) : 0;

        if ($order_id <= 0 || !in_array($status_id, [4, 5, 7])) {
            throw new Exception('Invalid order ID or status ID');
        }

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        $conn->begin_transaction();

        // Nếu chuyển từ "Chờ duyệt" (3) sang "Đã duyệt" (4), cập nhật số lượng sản phẩm
        if ($status_id == 4) {
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

            // Cập nhật số lượng sản phẩm trong bảng product

            foreach ($items as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];

                // Kiểm tra số lượng hiện tại trong bảng product
                $sql = "SELECT stock_quantity FROM product WHERE product_id = ? AND status_id != 6";
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

                    if ($available_quantity < $quantity) {
                        throw new Exception("Sản phẩm ID $product_id không đủ số lượng: Yêu cầu $quantity, chỉ còn $available_quantity");
                    }

                    // Cập nhật số lượng trong bảng product
                    $new_quantity = $available_quantity - $quantity;
                    $sql = "UPDATE product SET stock_quantity = ? WHERE product_id = ?";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
                    }
                    $stmt->bind_param("ii", $new_quantity, $product_id);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    throw new Exception("Sản phẩm ID $product_id không tồn tại hoặc đã bị xóa");
                }
            }
        }

        // Cập nhật trạng thái đơn hàng trong bảng orders
        $sql = "UPDATE orders SET status_id = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        $stmt->bind_param("ii", $status_id, $order_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Đơn hàng đã được cập nhật'
            ]);
        } else {
            throw new Exception('Không thể cập nhật trạng thái đơn hàng');
        }

        $stmt->close();
    } else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>