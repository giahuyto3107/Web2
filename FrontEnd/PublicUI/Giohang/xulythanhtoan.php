<?php
session_start();
include('../../../BackEnd/Config/config.php');

error_log("User address: " . $_SESSION['user_address']);
error_log("Total amount: " . $_SESSION['selectedTotal']);

$user_id = $_SESSION['user_id'];
$total_amount = $_SESSION['selectedTotal'];
$status_id = 3; 
$payment_method = "Online";
$phone = $_SESSION['user_phone'];
$address = $_SESSION['user_address'];
$products = $_SESSION['selectedProducts'];

if (empty($products) || empty($total_amount) || empty($address) || empty($phone)) {
    die("Dữ liệu thanh toán không hợp lệ. Vui lòng kiểm tra lại.");
}

$conn->begin_transaction();

try {
    // Chèn đơn hàng vào bảng `orders`
    $sql = "INSERT INTO orders (user_id, order_date, total_amount, status_id, payment_method, phone, address) 
            VALUES (?, NOW(), ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Sửa kiểu dữ liệu của bind_param
    $stmt->bind_param("iissss", $user_id, $total_amount, $status_id, $payment_method, $phone, $address);
    $stmt->execute();
    
    $order_id = $conn->insert_id;

    // Chuẩn bị câu lệnh để chèn order_items và cập nhật số lượng sản phẩm
    $sql_order_items = "INSERT INTO order_items (order_id, product_id, quantity, price, review) VALUES (?, ?, ?, ?, ?)";
    $stmt_order_items = $conn->prepare($sql_order_items);

    $sql_update_quantity = "UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
    $stmt_update_quantity = $conn->prepare($sql_update_quantity);
    
    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $quantity = $product['quantity'];
        
        // Tìm giá sản phẩm từ session
        $price = 0; // Giá mặc định nếu không tìm thấy
        foreach ($_SESSION['selectedProducts'] as $selectedProduct) {
            if ($selectedProduct['product_id'] == $product_id) {
                $price = $selectedProduct['price']; // Lấy giá đúng
                break;
            }
        }

        // Thêm vào order_items với review mặc định là 0
        $review = 0; // Thiết lập giá trị review mặc định là 0
        $stmt_order_items->bind_param("iiidi", $order_id, $product_id, $quantity, $price, $review);
        $stmt_order_items->execute();

        // Giảm số lượng sản phẩm trong kho
        $stmt_update_quantity->bind_param("ii", $quantity, $product_id);
        $stmt_update_quantity->execute();

        // Kiểm tra nếu số lượng âm (tùy chọn)
        if ($conn->affected_rows == 0) {
            throw new Exception("Không thể cập nhật số lượng sản phẩm cho product_id: $product_id");
        }
    }
    
    // Xóa sản phẩm trong giỏ hàng
    $sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    }

    // Xóa session sau khi đặt hàng thành công
    unset($_SESSION['selectedProducts']);
    unset($_SESSION['selectedTotal']);
    unset($_SESSION['user_phone']);
    unset($_SESSION['user_address']);

    $conn->commit();
    header("Location: http://localhost/Web2/FrontEnd/PublicUI/Giohang/giohang.php");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    echo "Lỗi khi lưu đơn hàng: " . $e->getMessage();
}

$conn->close();
?>