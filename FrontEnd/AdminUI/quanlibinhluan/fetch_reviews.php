<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

try {
    // Truy vấn tất cả đánh giá từ bảng review với thông tin user, admin và product
    $sql = "SELECT r.review_id, r.user_id, r.user_admin_id, r.product_id, r.rating, 
                   r.review_text, r.feedback, r.review_date, r.status_id,
                   u1.full_name AS user_name, u2.full_name AS admin_name, 
                   p.product_name AS product_name
            FROM review r
            LEFT JOIN user u1 ON r.user_id = u1.user_id
            LEFT JOIN user u2 ON r.user_admin_id = u2.user_id
            LEFT JOIN product p ON r.product_id = p.product_id
            WHERE r.status_id IN (1, 2);";
    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    $reviews = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reviews[] = [
                'review_id' => $row['review_id'],
                'user_id' => $row['user_id'],
                'user_admin_id' => $row['user_admin_id'],
                'product_id' => $row['product_id'],
                'rating' => $row['rating'],
                'review_text' => $row['review_text'],
                'feedback' => $row['feedback'],
                'review_date' => $row['review_date'],
                'status_id' => $row['status_id'],
                'user_name' => $row['user_name'] ?? 'N/A', // Tên người dùng (nếu có)
                'admin_name' => $row['admin_name'] ?? 'N/A', // Tên admin (nếu có)
                'product_name' => $row['product_name'] ?? 'N/A' // Tên sản phẩm (nếu có)
            ];
        }
    }

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Lấy danh sách đánh giá thành công',
        'data' => $reviews
    ]);
} catch (Exception $e) {
    // Trả về lỗi nếu có
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'data' => []
    ]);
} finally {
    // Đóng kết nối
    $conn->close();
}
?>