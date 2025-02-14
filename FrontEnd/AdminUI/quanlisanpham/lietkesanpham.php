<?php 
$sql_sp = "SELECT * FROM product, category WHERE product.category_id = category.category_id ORDER BY product_id DESC";
$query_sp = mysqli_query($conn, $sql_sp);
?>

<div class="form2 form2-1">
    <div class="form-title">
        <p>Liệt kê Sản phẩm</p>
    </div>
    <div class="form2-content">
        <table>
            <tr>
                <th>Stt</th>
                <th>Tên Sản Phẩm</th>
                <th>Mô tả Sản Phẩm</th>
                <th>Giá Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Thể loại</th>
                <th>Hình ảnh</th>
                <th>Tình trạng</th>
                <th>Quản lí</th>
            </tr>

            <?php
            $i = 0;
            while ($row = mysqli_fetch_array($query_sp)) {
                $i++;
            ?>
                <tr>
                    <td><?= $i ?></td>
                    <td class="width1"><?= $row['product_name'] ?></td>
                    <td><?= $row['product_description'] ?></td>
                    <td><?= number_format($row['price'], 0, ',', '.') ?> VNĐ</td>
                    <td><?= $row['stock_quantity'] ?></td>
                    <td><?= $row['category_name'] ?></td>
                    <td><img src="../../BackEnd/Uploads/Product Picture/<?php echo $row['image_url'] ?>" alt="" class="img_brand"></td>
                    <td>
                        <?php 
                        echo ($row['status_id'] == 1) ? "Hiện" : "Ẩn"; 
                        ?>
                    </td>
                    <td>
                        <a class="sua" href="?action=quanlysp&query=sua&idsanpham=<?php echo $row['product_id'] ?>">Sửa</a>
                        <a class="vohieuhoa" href="#" onclick="confirmDelete(<?= $row['stock_quantity'] ?>, <?= $row['product_id'] ?>)">Xóa</a>
                    </td>
                </tr>
            <?php 
            }
            ?>
        </table>
    </div>
</div>

<script>
    function confirmDelete(soluong, idSanpham) {
    if (soluong > 0) {
        alert('Không thể xóa sản phẩm vì số lượng không bằng 0.');
    } else {
        // Nếu số lượng bằng 0, chuyển hướng đến trang xóa
        window.location.href = 'modules/quanlisanpham/xuli.php?idsanpham=' + idSanpham;
    }
}
</script>
<style>
    .form2-content {
    background: #fff;            /* Màu nền trắng */
    padding: 1.8rem;            /* Khoảng cách bên trong */
    border-radius: 2rem;        /* Bo góc */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ (tùy chọn) */
    margin: 20px;      
    width: 100%;                /* Chiếm toàn bộ chiều rộng */
}

table {
    width: 100%;                /* Đặt chiều rộng bảng */
    border-collapse: collapse;  /* Gộp viền bảng */
}

th, td {
    padding: 12px;              /* Khoảng cách bên trong ô */
    border: 1px solid #ccc;     /* Viền ô bảng */
    text-align: center;         /* Căn giữa cho tất cả các ô */
}

th {
    background-color: #f2f2f2;  /* Màu nền cho tiêu đề bảng */
}

td:first-child {
    width: 10%;                 /* Chiếm 10% chiều rộng cho cột "Stt" */
}

td:nth-child(2) {
    width: 40%;                 /* Chiếm 40% chiều rộng cho cột "Tên Sản Phẩm" */
}

td:nth-child(3) {
    width: 10%;                 /* Chiếm 10% chiều rộng cho cột "Mã Sản Phẩm" */
}

td:nth-child(4) {
    width: 20%;                 /* Chiếm 20% chiều rộng cho cột "Giá Sản Phẩm" */
}

td:nth-child(5) {
    width: 10%;                 /* Chiếm 10% chiều rộng cho cột "Số Lượng" */
}

td:nth-child(6) {
    width: 10%;                 /* Chiếm 10% chiều rộng cho cột "Brand" */
}

td:nth-child(7) {
    width: 15%;                 /* Chiếm 15% chiều rộng cho cột "Hình ảnh" */
}

td:nth-child(8) {
    width: 10%;                 /* Chiếm 10% chiều rộng cho cột "Tình trạng" */
}

td:last-child {
    width: 20%;                 /* Chiếm 20% chiều rộng cho cột "Quản lý" */
}

a.sua {
    background-color: #4caf50;  /* Màu xanh lá đậm */
}

a.vohieuhoa {
    background-color: #ff5722;   /* Màu đỏ đậm */
}

a {
    display: inline-block;        /* Để có thể áp dụng padding và border */
    padding: 5px 10px;           /* Khoảng cách bên trong */
    text-decoration: none;        /* Bỏ gạch chân */
    color: white;                 /* Màu chữ trắng */
    border: 1px solid black;      /* Khung bên ngoài màu đen */
    border-radius: 4px;          /* Bo góc nhẹ */
    transition: background-color 0.3s; /* Hiệu ứng chuyển màu nền */
}

a:hover {
    opacity: 0.8;                /* Hiệu ứng giảm độ trong suốt khi hover */
}

.form-title {
    margin-bottom: 20px;        /* Khoảng cách dưới tiêu đề */
}
</style>