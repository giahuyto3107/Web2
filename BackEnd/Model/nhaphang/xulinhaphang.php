<?php
header("Content-Type: application/json");
include ('../../Config/config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $supplier_id = $_POST['supplier_id'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $order_date = $_POST['order_date'] ?? '';
    $total_amount = $_POST['total_amount'] ?? '0';
    $total_price = $_POST['total_price'] ?? '0';
    $profit_percent = $_POST['profit_percent'] ?? '0';
    $status_id = $_POST['status_id'] ?? '1';
    $purchase_items = json_decode($_POST['purchase_items'], true) ?? [];

    if (empty($supplier_id) || empty($purchase_items)) {
        echo json_encode(["success" => false, "message" => "Thiếu thông tin đơn hàng!"]);
        exit;
    }

    // Insert order into 'purchase_order' table
    $stmt = $conn->prepare("INSERT INTO purchase_order (supplier_id, user_id, order_date, total_amount, total_price, status_id) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die(json_encode(["success" => false, "message" => "Lỗi SQL: " . $conn->error]));
    }

    $stmt->bind_param("iisddi", $supplier_id, $user_id, $order_date, $total_amount, $total_price, $status_id);
    if ($stmt->execute()) {
        $purchase_order_id = $stmt->insert_id;

        // Insert order items
        $stmtDetail = $conn->prepare("INSERT INTO purchase_order_items (purchase_order_id, product_id, quantity, price, profit) VALUES (?, ?, ?, ?, ?)");
        if (!$stmtDetail) {
            die(json_encode(["success" => false, "message" => "Lỗi SQL chi tiết: " . $conn->error]));
        }

        foreach ($purchase_items as $item) {
            // Validate product_id
            $checkProduct = $conn->prepare("SELECT 1 FROM product WHERE product_id = ?");
            $checkProduct->bind_param("i", $item['product_id']);
            $checkProduct->execute();
            $checkProduct->store_result();

            if ($checkProduct->num_rows == 0) {
                die(json_encode(["success" => false, "message" => "Lỗi: Sản phẩm với ID " . $item['product_id'] . " không tồn tại."]));
            }
            $checkProduct->close();

            $profit = ($item['price'] * $profit_percent) / 100; // Calculate profit per item
            $stmtDetail->bind_param("iiidd", $purchase_order_id, $item['product_id'], $item['quantity'], $item['price'], $profit);
            if (!$stmtDetail->execute()) {
                die(json_encode(["success" => false, "message" => "Lỗi khi chèn chi tiết đơn hàng: " . $stmtDetail->error]));
            }
        }

        echo json_encode(["success" => true, "message" => "Đơn hàng đã được lưu"]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi lưu đơn hàng: " . $stmt->error]);
    }

    $stmt->close();
    if (isset($stmtDetail) && $stmtDetail) {
        $stmtDetail->close();
    }
    $conn->close();
}
?>
