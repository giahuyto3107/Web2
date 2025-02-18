<?php
include ('../../../BackEnd/Config/config.php');

if (isset($_POST['themncc'])) {
    // Lấy dữ liệu từ form
    $tenncc = $_POST['tennhacungcap'];
    $sdt=$_POST['sdt'];
    $diachi=$_POST['diachi'];
    $trangthai=$_POST['trangthai'];

    // Kiểm tra xem loại sản phẩm đã tồn tại hay chưa
    $sql_check = "SELECT * FROM supplier WHERE supplier_name='$tenncc'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Nếu loại sản phẩm đã tồn tại
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanlinhacungcap&query=them&thanhcong=1');
        exit;
    } else {
        // Nếu chưa tồn tại, thực hiện thêm
        $sql_them = "INSERT INTO supplier (supplier_name, contact_phone, address, status_id) VALUES ('$tenncc', '$sdt','$diachi', '$trangthai')";
        mysqli_query($conn, $sql_them);
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanlinhacungcap&query=them&thanhcong=2');
        exit;
    }
} elseif (isset($_POST['suancc'])) {
    // Lấy dữ liệu từ form
    $idncc = $_POST['idncc']; // ID của loại sản phẩm cần sửa
    $tenncc = $_POST['tennhacungcap'];
    $sdt=$_POST['sdt'];
    $diachi=$_POST['diachi'];

    // Kiểm tra xem tên loại sản phẩm mới có trùng với loại sản phẩm khác không
    $sql_check = "SELECT * FROM supplier WHERE supplier_name='$tenncc' AND supplier_id != '$idncc'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Nếu loại sản phẩm đã tồn tại
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanlinhacungcap&query=them&thanhcong=1');
        exit;
    } else {
        // Nếu chưa tồn tại, thực hiện sửa
        $sql_update = "UPDATE supplier SET supplier_name='$tenncc', contact_phone='$sdt',address='$diachi' WHERE supplier_id='$idncc'";
        if (mysqli_query($conn, $sql_update)) {
            // Cập nhật thành công
            header('Location: ../../../Frontend/AdminUI/index.php?action=quanlinhacungcap&query=them&thanhcong=0');
        } else {
            // Lỗi khi cập nhật
            header('Location: ../../../Frontend/AdminUI/index.php?action=quanlinhacungcap&query=them&thanhcong=3');
        }
        exit;
    }
} else {
    // Xử lý cập nhật trạng thái
    $idncc = $_GET['idncc']; // Lấy ID của loại sản phẩm từ URL
    $status_id = $_GET['status_id']; // Lấy trạng thái mới từ URL

    // Cập nhật trạng thái của loại sản phẩm
    $sql_update_status = "UPDATE supplier SET status_id='$status_id' WHERE supplier_id='$idncc'";
    mysqli_query($conn, $sql_update_status);

    // Chuyển hướng về trang quản lý loại sản phẩm
    header('Location: ../../../Frontend/AdminUI/index.php?action=quanlinhacungcap&query=them');
    exit;
}
?>