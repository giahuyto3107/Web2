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

<body>
    <h2>Cập nhật phản hồi</h2>
    <form method="POST" action="../../BackEnd/Model/quanlibinhluan/xulibinhluan.php">
        <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($row['full_name']); ?></p>
        <p><strong>Tên sản phẩm:</strong> <?php echo htmlspecialchars($row['product_name']); ?></p>
        <p><strong>Đánh giá:</strong> <?php echo htmlspecialchars($row['rating']); ?> sao</p>
        <p><strong>Nội dung bình luận:</strong> <?php echo htmlspecialchars($row['review_text']); ?></p>

        <?php if ($row['feedback'] !== NULL): ?>
            <p><strong>Phản hồi của cửa hàng:</strong> <?php echo htmlspecialchars($row['feedback']); ?></p>
            <button type="button" onclick="document.getElementById('feedback').style.display='block'; this.style.display='none';">Sửa feedback</button>
        <?php else: ?>
            <p><strong>Phản hồi:</strong></p>
        <?php endif; ?>

        <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($_GET['review_id']); ?>">

        <textarea id="feedback" name="feedback" rows="4" cols="50" style="display: <?php echo $row['feedback'] !== NULL ? 'none' : 'block'; ?>;" required><?php echo $row['feedback'] !== NULL ? htmlspecialchars($row['feedback']) : ''; ?></textarea><br><br>
        
        <button type="submit" name="capnhat_feedback">Cập nhật phản hồi</button>
    </form>
</body>