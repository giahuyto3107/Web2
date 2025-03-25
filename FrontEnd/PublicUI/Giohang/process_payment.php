<?php
session_start();
include('../../../BackEnd/Config/config.php');

$user_id = $_POST['user_id'];
$total_amount = $_POST['total_amount'];
$status_id = $_POST['status_id'];
$payment_method = $_POST['payment_method'];
$phone = $_POST['phone']; // Lấy số điện thoại từ POST
$address = $_POST['address'];
$products = json_decode($_POST['products'], true); 
$price = $_SESSION['selectedProducts'];

$sql = "INSERT INTO orders (user_id, order_date, total_amount, status_id, payment_method, phone, address)
        VALUES (?, NOW(), ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

// Cập nhật kiểu dữ liệu cho bind_param
mysqli_stmt_bind_param($stmt, "idssss", $user_id, $total_amount, $status_id, $payment_method, $phone, $address);

if (mysqli_stmt_execute($stmt)) {
    $order_id = mysqli_insert_id($conn); 

    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $quantity = $product['quantity'];
        
        // Tìm giá sản phẩm tương ứng trong session
        foreach ($_SESSION['selectedProducts'] as $selectedProduct) {
            if ($selectedProduct['product_id'] == $product_id) {
                $price = $selectedProduct['price'];
                break;
            }
        }
    
        // Thêm chi tiết đơn hàng vào bảng order_items
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiid", $order_id, $product_id, $quantity, $price);
        mysqli_stmt_execute($stmt);

        // Giảm số lượng sản phẩm trong bảng products
        $sql = "UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $product_id);
        mysqli_stmt_execute($stmt);
    }
    
    // Xóa sản phẩm khỏi giỏ hàng
    foreach ($products as $product) {
        $product_id = $product['product_id'];

        $sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
    }

    echo json_encode(["success" => true, "message" => "Đơn hàng đã được thêm thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi thêm đơn hàng: " . mysqli_error($conn)]);
}

mysqli_close($conn);
?>