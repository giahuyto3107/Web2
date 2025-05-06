<?php
session_start();
include('../../../BackEnd/Config/config.php'); // Kết nối database

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Bạn chưa đăng nhập."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : "";

// Kiểm tra dữ liệu hợp lệ
if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($review_text)) {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
    exit;
}

// Thêm mới review
$query_insert = "INSERT INTO review (user_id, product_id, rating, review_text, feedback, status_id) VALUES (?, ?, ?, ?, NULL, 1)";
$stmt_insert = mysqli_prepare($conn, $query_insert);
mysqli_stmt_bind_param($stmt_insert, "iiis", $user_id, $product_id, $rating, $review_text);

if (mysqli_stmt_execute($stmt_insert)) {
    // Cập nhật order_items để đánh dấu đã đánh giá
    $query_update_order_item = "
        UPDATE order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        SET oi.review = 1
        WHERE o.user_id = ? AND oi.product_id = ?
    ";
    $stmt_order_item_update = mysqli_prepare($conn, $query_update_order_item);
    mysqli_stmt_bind_param($stmt_order_item_update, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt_order_item_update);

    echo json_encode(["status" => "success", "message" => "Gửi đánh giá thành công!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Có lỗi khi lưu đánh giá."]);
}

mysqli_close($conn);
?>
