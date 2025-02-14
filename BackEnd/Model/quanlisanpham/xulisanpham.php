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

    // Đổi tên hình ảnh để tránh trùng lặp
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
	//sua
	$tensanpham = $_POST['tensanpham'];
	$giasp = $_POST['giasp'];
	$soluong = $_POST['soluong'];
	$sanxuat = $_POST['sanxuat'];
	$brand = $_POST['brand'];
	$danhmuc = $_POST['danhmuc'];
	$noidung = $_POST['noidung'];
	$tomtat  = $_POST['tomtat'];
	$tinhtrang = $_POST['tinhtrang'];
	$thongso = $_POST['thongso'];
	$idloaisp = $_POST['danhmuc'];
	$hinhanh = $_FILES['hinhanh']['name'];
	$hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
	$hinhanh = time() . '_' . $hinhanh;


	$files = $_FILES['images'];
	$files_names = $files['name'];
	if (!empty($_FILES['hinhanh']['name'])) {
		move_uploaded_file($hinhanh_tmp, 'uploads/' . $hinhanh);

		$sql_update = "UPDATE tbl_sanpham SET tensanpham='" . $tensanpham . "',giasp='" . $giasp . "',soluong='" . $soluong . "',sanxuat='" . $sanxuat . "',hinhanh='" . $hinhanh . "',tomtat='" . $tomtat . "',noidung='" . $noidung . "',thongso='" . $thongso . "',tinhtrang='" . $tinhtrang . "',id_brand='" . $brand . "' ,idloaisp='" . $idloaisp . "' WHERE id_sanpham='$_GET[idsanpham]'";
		//xoa hinh anh cu
		$sql = "SELECT * FROM tbl_sanpham WHERE id_sanpham = '$_GET[idsanpham]' LIMIT 1";
		$query = mysqli_query($mysqli, $sql);
		while ($row = mysqli_fetch_array($query)) {
			unlink('uploads/' . $row['hinhanh']);
		}
	} else {
		$sql_update = "UPDATE tbl_sanpham SET tensanpham='" . $tensanpham . "',giasp='" . $giasp . "',soluong='" . $soluong . "',sanxuat='" . $sanxuat . "',tomtat='" . $tomtat . "',noidung='" . $noidung . "',thongso='" . $thongso . "',tinhtrang='" . $tinhtrang . "',id_brand='" . $brand . "',idloaisp='" . $idloaisp . "' WHERE id_sanpham='$_GET[idsanpham]'";
	}




	foreach ($files_names as $key => $value) {
		if (!empty($files['name'][$key])) {
			move_uploaded_file($files['tmp_name'][$key], 'upload2/' . $value);
			$sql_them2 = "INSERT INTO img_product(id_product,img_product) VALUES ('$_GET[idsanpham]','$value')";
			mysqli_query($mysqli, $sql_them2);
		}
	}






	mysqli_query($mysqli, $sql_update);
	header('Location:../../index.php?action=quanlysanpham&query=them');
	
} else {
    $id = $_GET['idsanpham'];

    // Xóa hình ảnh phụ
    $sql2 = "SELECT * FROM img_product WHERE id_product = '$id'";
    $query2 = mysqli_query($mysqli, $sql2);
    while ($row2 = mysqli_fetch_array($query2)) {
        if (file_exists('upload2/' . $row2['img_product'])) {
            unlink('upload2/' . $row2['img_product']);
        }
    }
    $sql_xoa2 = "DELETE FROM img_product WHERE id_product='$id'";
    mysqli_query($mysqli, $sql_xoa2);

    // Xóa bản ghi liên quan trong giỏ hàng
    $sql_cart_delete = "DELETE FROM tbl_cart_details WHERE id_sanpham='$id'";
    mysqli_query($mysqli, $sql_cart_delete);

    // Xóa sản phẩm chính
    $sql = "SELECT * FROM tbl_sanpham WHERE id_sanpham = '$id' LIMIT 1";
    $query = mysqli_query($mysqli, $sql);
    while ($row = mysqli_fetch_array($query)) {
        if (file_exists('modules/quanlisanpham/uploads/' . $row['hinhanh'])) {
            unlink('modules/quanlisanpham/uploads/' . $row['hinhanh']);
        }
    }

    // Xóa sản phẩm
    $sql_xoa = "DELETE FROM tbl_sanpham WHERE id_sanpham='$id'";
    mysqli_query($mysqli, $sql_xoa);

    // Chuyển hướng về trang quản lý
    header('Location:../../index.php?action=quanlysanpham&query=them');
}