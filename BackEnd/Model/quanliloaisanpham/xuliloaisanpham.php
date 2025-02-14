<?php
include ('../../../BackEnd/Config/config.php');

if (isset($_POST['themloaisp'])) {
    // Lấy dữ liệu từ form
    $tenloaisp = $_POST['tenloaisp'];
    $motaloaisp = $_POST['motaloaisp']; // Thêm dòng này để lấy mô tả từ form

    // Kiểm tra xem loại sản phẩm đã tồn tại hay chưa
    $sql_check = "SELECT * FROM category WHERE category_name='$tenloaisp'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Nếu loại sản phẩm đã tồn tại
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanliloaisp&query=them&thanhcong=1');
        exit;
    } else {
        // Nếu chưa tồn tại, thực hiện thêm
        $sql_them = "INSERT INTO category (category_name, category_description, status_id) VALUES ('$tenloaisp', '$motaloaisp', 1)";
        mysqli_query($conn, $sql_them);
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanliloaisp&query=them&thanhcong=2');
        exit;
    }
} elseif (isset($_POST['sualoaisp'])) {
    // Lấy dữ liệu từ form
    $category_id = $_POST['category_id']; // ID của loại sản phẩm cần sửa
    $tenloaisp = $_POST['tenloaisp'];
    $motaloaisp = $_POST['motaloaisp']; // Lấy mô tả từ form

    // Kiểm tra xem tên loại sản phẩm mới có trùng với loại sản phẩm khác không
    $sql_check = "SELECT * FROM category WHERE category_name='$tenloaisp' AND category_id != '$category_id'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Nếu loại sản phẩm đã tồn tại
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanliloaisp&query=them&thanhcong=1');
        exit;
    } else {
        // Nếu chưa tồn tại, thực hiện sửa
        $sql_update = "UPDATE category SET category_name='$tenloaisp', category_description='$motaloaisp' WHERE category_id='$category_id'";
        if (mysqli_query($conn, $sql_update)) {
            // Cập nhật thành công
            header('Location: ../../../Frontend/AdminUI/index.php?action=quanliloaisp&query=them&thanhcong=0');
        } else {
            // Lỗi khi cập nhật
            header('Location: ../../../Frontend/AdminUI/index.php?action=quanliloaisp&query=them&thanhcong=3');
        }
        exit;
    }
} else {
    // Xử lý cập nhật trạng thái
    $category_id = $_GET['category_id']; // Lấy ID của loại sản phẩm từ URL
    $status_id = $_GET['status_id']; // Lấy trạng thái mới từ URL

    // Cập nhật trạng thái của loại sản phẩm
    $sql_update_status = "UPDATE category SET status_id='$status_id' WHERE category_id='$category_id'";
    mysqli_query($conn, $sql_update_status);

    // Chuyển hướng về trang quản lý loại sản phẩm
    header('Location: ../../../Frontend/AdminUI/index.php?action=quanliloaisp&query=them');
    exit;
}
?>