<?php

// Truy vấn để lấy thông tin bình luận
$sql = "SELECT r.review_id, r.user_id, r.product_id, r.rating, r.review_text, r.feedback, r.status_id, 
               u.full_name, p.product_name 
        FROM review r
        JOIN user u ON r.user_id = u.user_id
        JOIN product p ON r.product_id = p.product_id";

$result = mysqli_query($conn, $sql);

// Kiểm tra xem có bình luận nào không
if (mysqli_num_rows($result) > 0) {
    echo '<table border="1">';
    echo '<tr>
            <th>Họ và tên</th>
            <th>Tên sản phẩm</th>
            <th>Đánh giá</th>
            <th>Nội dung bình luận</th>
            <th>Trạng thái</th>
            <th>Quản lý</th>
          </tr>';

          while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['rating']) . '</td>';
            echo '<td>' . htmlspecialchars($row['review_text']) . '</td>';
            echo '<td>' . ($row['status_id'] == 1 ? 'Hoạt động' : 'Không hoạt động') . '</td>';
            echo '<td>';
            
            if ($row['feedback'] !== NULL) {
                // Nếu có feedback, hiển thị nút "Xem chi tiết bình luận"
                echo '<a href="?action=quanlibinhluan&query=sua&review_id=' . $row['review_id'] . '">Xem chi tiết</a>';
            } else {
                // Nếu không có feedback, hiển thị nút "Trả lời"
                echo '<a href="?action=quanlibinhluan&query=sua&review_id=' . $row['review_id'] . '">Trả lời</a>';
            }
    
            if ($row['status_id'] == 2) {
                // Nếu trạng thái là "Không hoạt động"
                echo '<a class="vohieuhoa" href="../../BackEnd/Model/quanlibinhluan/xulibinhluan.php?review_id=' . $row['review_id'] . '&status_id=1" style="color: green;">Khôi phục</a>';
            } else {
                // Nếu trạng thái là "Hoạt động"
                echo '<a class="khoiphuc" href="../../BackEnd/Model/quanlibinhluan/xulibinhluan.php?review_id=' . $row['review_id'] . '&status_id=2" style="color: red;">Vô hiệu hóa</a>';
            }
        
            echo '</td>';
            echo '</tr>';
        



            
    }
    echo '</table>';
} else {
    echo 'Không có bình luận nào.';
}
?>