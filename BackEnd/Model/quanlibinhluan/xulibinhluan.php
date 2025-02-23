<?php
// Kết nối đến cơ sở dữ liệu
include ('../../../BackEnd/Config/config.php');

// Kiểm tra xem form đã được submit chưa
if (isset($_POST['capnhat_feedback'])) {
    // Lấy dữ liệu từ form
    $review_id = $_POST['review_id']; // ID của bình luận cần cập nhật
    $feedback = $_POST['feedback']; // Nội dung phản hồi

    // Kiểm tra xem review_id có hợp lệ không
    if (empty($review_id)) {
        echo "Lỗi: Không có ID bình luận!";
        exit;
    }

    // Cập nhật phản hồi vào bảng review
    $sql_update = "UPDATE review SET feedback = '$feedback' WHERE review_id = '$review_id'";

    if (mysqli_query($conn, $sql_update)) {
        // Thành công, chuyển hướng về trang quản lý bình luận
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanlibinhluan&query=them');
        exit;
    } else {
        // Lỗi khi cập nhật
        echo "Lỗi: " . mysqli_error($conn);
    }
} else {
    // Xử lý cập nhật trạng thái
    $review_id = $_GET['review_id']; // Lấy ID của loại sản phẩm từ URL
    $status_id = $_GET['status_id']; // Lấy trạng thái mới từ URL

    // Cập nhật trạng thái của loại sản phẩm
    $sql_update_status = "UPDATE review SET status_id='$status_id' WHERE review_id='$review_id'";
    mysqli_query($conn, $sql_update_status);

    // Chuyển hướng về trang quản lý loại sản phẩm
    header('Location: ../../../Frontend/AdminUI/index.php?action=quanlibinhluan&query=them');
    exit;
}