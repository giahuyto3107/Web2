<?php
session_start();
include ('../../../BackEnd/Config/config.php'); // Kết nối database

$user_id = $_SESSION['user_id'];
// $user_id = 1;
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : "";

// Kiểm tra dữ liệu hợp lệ
if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($review_text)) {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
    exit;
}

// Kiểm tra xem user đã đánh giá sản phẩm này chưa
$query_check = "SELECT * FROM review WHERE user_id = ? AND product_id = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $product_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$existing_review = mysqli_fetch_assoc($result_check);

if ($existing_review) {
    // Cập nhật review
    $query_update = "UPDATE review SET rating = ?, review_text = ?, status_id = 1 WHERE user_id = ? AND product_id = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "isii", $rating, $review_text, $user_id, $product_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $query_update_order_item = "UPDATE order_items SET review = 1 WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = ?) AND product_id = ? AND order_item_id IN (SELECT order_item_id FROM order_items WHERE product_id = ? AND order_id IN (SELECT order_id FROM orders WHERE user_id = ?))";
        $stmt_order_item_update = mysqli_prepare($conn, $query_update_order_item);
        mysqli_stmt_bind_param($stmt_order_item_update, "iiii", $user_id, $product_id, $product_id, $user_id);
        mysqli_stmt_execute($stmt_order_item_update);

        echo json_encode(["status" => "success", "message" => "Cập nhật đánh giá thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Có lỗi khi cập nhật đánh giá."]);
    }
} else {
    // Thêm mới review
    $query_insert = "INSERT INTO review (user_id, product_id, rating, review_text, feedback, status_id) VALUES (?, ?, ?, ?, NULL, 1)";
    $stmt_insert = mysqli_prepare($conn, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, "iiis", $user_id, $product_id, $rating, $review_text);
    
    if (mysqli_stmt_execute($stmt_insert)) {
        $query_update_order_item = "UPDATE order_items SET review = 1 WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = ?) AND product_id = ? AND order_item_id IN (SELECT order_item_id FROM order_items WHERE product_id = ? AND order_id IN (SELECT order_id FROM orders WHERE user_id = ?))";
        $stmt_order_item_update = mysqli_prepare($conn, $query_update_order_item);
        mysqli_stmt_bind_param($stmt_order_item_update, "iiii", $user_id, $product_id, $product_id, $user_id);
        mysqli_stmt_execute($stmt_order_item_update);

        echo json_encode(["status" => "success", "message" => "Gửi đánh giá thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Có lỗi khi lưu đánh giá."]);
    }
}

mysqli_close($conn);
?>