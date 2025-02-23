<?php
// Lấy review_id từ URL
if (isset($_GET['review_id'])) {
    $review_id = $_GET['review_id'];

    // Truy vấn để lấy chi tiết bình luận
    $sql = "SELECT r.review_id, u.full_name, p.product_name, r.rating, r.review_text, r.feedback 
            FROM review r
            JOIN user u ON r.user_id = u.user_id
            JOIN product p ON r.product_id = p.product_id
            WHERE r.review_id = '$review_id'";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);

    if (!$row) {
        echo "Không tìm thấy bình luận!";
        exit;
    }
} else {
    echo "Không có ID bình luận!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết bình luận</title>
</head>
<body>
    <h2>Chi tiết bình luận</h2>
    <p><strong>Họ và tên:</strong> <?php echo $row['full_name']; ?></p>
    <p><strong>Tên sản phẩm:</strong> <?php echo $row['product_name']; ?></p>
    <p><strong>Đánh giá:</strong> <?php echo $row['rating']; ?> sao</p>
    <p><strong>Nội dung bình luận:</strong> <?php echo $row['review_text']; ?></p>
    <p><strong>Phản hồi:</strong> <?php echo $row['feedback']; ?></p>
    <a href="lietkebinhluan.php">Quay lại</a>
</body>
</html>