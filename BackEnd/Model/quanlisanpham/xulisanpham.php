<?php
include ('../../../BackEnd/Config/config.php');
// Kiểm tra xem form đã được submit chưa
if (isset($_POST['themsanpham'])) {
    // Lấy dữ liệu từ form
    $tensanpham = $_POST['tensanpham'];
    $motasp = $_POST['motasp'];
    $giasp = $_POST['giasp'];
    $stock_quantity = $_POST['stock_quantity'];
    $idloaisp = $_POST['loaisp'];
    $tinhtrang = $_POST['tinhtrang'];
    $hinhanh = $_FILES['hinhanh']['name'];
    $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
    $hinhanh = time() . '_' . $hinhanh;

    // Đường dẫn lưu hình ảnh
    $upload_dir = '../../Uploads/Product Picture/'; // Thư mục lưu hình ảnh
    $image_url = $upload_dir . $hinhanh;

    // Di chuyển hình ảnh vào thư mục
    if (move_uploaded_file($hinhanh_tmp, $image_url)) {
        // Thêm sản phẩm vào cơ sở dữ liệu
        $sql_them = "INSERT INTO product (product_name, product_description, price, stock_quantity, category_id, status_id, image_url, created_at, updated_at) 
                     VALUES ('$tensanpham', '$motasp', '$giasp', '$stock_quantity', '$idloaisp', '$tinhtrang', '$image_url', NOW(), NOW())";

        if (mysqli_query($conn, $sql_them)) {
            // Thành công, chuyển hướng về trang quản lý sản phẩm
            header('Location: ../../../Frontend/AdminUI/index.php?action=quanlisanpham&query=them');
            exit;
        } else {
            // Lỗi khi thêm sản phẩm
            echo "Lỗi: " . mysqli_error($conn);
        }
    } else {
        // Lỗi khi di chuyển hình ảnh
        echo "Lỗi: Không thể tải lên hình ảnh.";
    }
} elseif (isset($_POST['suasanpham'])) {
	// Lấy ID sản phẩm từ URL
    $idsanpham = $_GET['idsanpham'];

    // Lấy dữ liệu từ form
    $tensanpham = $_POST['tensanpham'];
    $motasp = $_POST['motasp'];
    $giasp = $_POST['giasp'];
    $idloaisp = $_POST['loaisp'];
    $tinhtrang = $_POST['tinhtrang'];
    $hinhanh = $_FILES['hinhanh']['name'];
    $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];

    // Nếu có hình ảnh mới được tải lên
    if (!empty($hinhanh)) {
        // Đổi tên hình ảnh để tránh trùng lặp
        $hinhanh = time() . '_' . $hinhanh;

        // Đường dẫn lưu hình ảnh
        $upload_dir = '../../Uploads/Product Picture/';
        $image_url = $upload_dir . $hinhanh;

        // Di chuyển hình ảnh vào thư mục
        move_uploaded_file($hinhanh_tmp, $image_url);
    } else {
        // Giữ nguyên hình ảnh cũ
        $sql_anh = "SELECT image_url FROM product WHERE product_id = '$idsanpham'";
        $query_anh = mysqli_query($conn, $sql_anh);
        $row_anh = mysqli_fetch_assoc($query_anh);
        $image_url = $row_anh['image_url'];
    }

    // Cập nhật thông tin sản phẩm
    $sql_sua = "UPDATE product SET 
                product_name = '$tensanpham', 
                product_description = '$motasp', 
                price = '$giasp', 
                category_id = '$idloaisp', 
                status_id = '$tinhtrang', 
                image_url = '$image_url' 
                WHERE product_id = '$idsanpham'";

    if (mysqli_query($conn, $sql_sua)) {
        // Thành công, chuyển hướng về trang quản lý sản phẩm
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanlisanpham&query=them');
        exit;
    } else {
        // Lỗi khi cập nhật
        echo "Lỗi: " . mysqli_error($conn);
    }
} else {
    $id = $_GET['idsanpham'];

    // Xóa bản ghi liên quan trong giỏ hàng
    $sql_cart_delete = "DELETE FROM cart_item WHERE product_id='$id'";
    mysqli_query($conn, $sql_cart_delete);

    // Xóa sản phẩm chính
    $sql = "SELECT * FROM product WHERE product_id = '$id' LIMIT 1";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($query)) {
        if (file_exists('../../Uploads/Product Picture/' . $row['image_url'])) {
            unlink('../../Uploads/Product Picture/' . $row['image_url']);
        }
    }

    // Xóa sản phẩm
    $sql_xoa = "DELETE FROM product WHERE product_id='$id'";
    mysqli_query($conn, $sql_xoa);

    // Chuyển hướng về trang quản lý
    header('Location: ../../../Frontend/AdminUI/index.php?action=quanlisanpham&query=them');
}