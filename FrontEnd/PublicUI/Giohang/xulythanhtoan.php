<?php
session_start();
include('../../../BackEnd/Config/config.php');

error_log("User address: " . $_SESSION['user_address']);
error_log("Total amount: " . $_SESSION['selectedTotal']);

$user_id = 1; 
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
    $sql = "INSERT INTO orders (user_id, order_date, total_amount, status_id, payment_method, phone, address) 
            VALUES (?, NOW(), ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("idssss", $user_id, $total_amount, $status_id, $payment_method, $phone, $address);
    $stmt->execute();
    
    $order_id = $conn->insert_id;

    $sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $quantity = $product['quantity'];
        $stmt->bind_param("iii", $order_id, $product_id, $quantity);
        $stmt->execute();
    }

    $sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    foreach ($products as $product) {
        $product_id = $product['product_id'];
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    }

    unset($_SESSION['selectedProducts']);
    unset($_SESSION['selectedTotal']);
    unset($_SESSION['selected_products']);
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