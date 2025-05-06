<?php
session_start();
header('Content-Type: application/json'); // Trả về JSON

include '../../../BackEnd/Config/config.php';
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để mua hàng']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Kiểm tra số lượng có hợp lệ không
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Số lượng không hợp lệ']);
    exit;
}

// Kiểm tra sản phẩm có tồn tại và còn hàng không
$check_stock_sql = "SELECT stock_quantity FROM product WHERE product_id = $product_id";
$check_result = mysqli_query($conn, $check_stock_sql);
$stock = mysqli_fetch_assoc($check_result);

if (!$stock) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    exit;
}

if ($stock['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Số lượng tồn kho không đủ']);
    exit;
}

// Trừ số lượng tồn kho
// $update_stock_sql = "UPDATE product SET stock_quantity = stock_quantity - $quantity WHERE product_id = $product_id";
// mysqli_query($conn, $update_stock_sql);

// Kiểm tra sản phẩm đã có trong giỏ chưa
$check_cart_sql = "SELECT * FROM cart_items WHERE user_id = $user_id AND product_id = $product_id";
$cart_result = mysqli_query($conn, $check_cart_sql);

if (mysqli_num_rows($cart_result) > 0) {
    // Cộng dồn số lượng
    $update_cart_sql = "UPDATE cart_items SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id";
    mysqli_query($conn, $update_cart_sql);
} else {
    // Thêm mới
    $insert_cart_sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
    mysqli_query($conn, $insert_cart_sql);
}

// (Tùy chọn) Đếm lại số sản phẩm trong giỏ để hiển thị lên icon
$count_sql = "SELECT SUM(quantity) AS total_items FROM cart_items WHERE user_id = $user_id";
$count_result = mysqli_query($conn, $count_sql);
$count_data = mysqli_fetch_assoc($count_result);
$total_items = $count_data['total_items'] ?? 0;

mysqli_close($conn);

echo json_encode([
    'success' => true,
    'message' => 'Đã thêm vào giỏ hàng',
    'cart_count' => $total_items
]);
exit;
?>
