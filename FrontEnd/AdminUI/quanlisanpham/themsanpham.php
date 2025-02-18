
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
</head>
<body>
    <div class="container">
        <h2>Thêm sản phẩm mới</h2>
        <form method="POST" action="../../BackEnd/Model/quanlisanpham/xulisanpham.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="tensanpham">Tên sản phẩm</label>
                <input type="text" id="tensanpham" name="tensanpham" required>
            </div>
            <div class="form-group">
                <label for="motasp">Mô tả sản phẩm</label>
                <textarea id="motasp" name="motasp" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="giasp">Giá sản phẩm</label>
                <input type="number" id="giasp" name="giasp" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="stock_quantity">Số lượng tồn kho</label>
                <input type="number" id="stock_quantity" name="stock_quantity" required>
            </div>
            <div class="form-group">
                <label for="loaisp">Loại sản phẩm</label>
                <select id="loaisp" name="loaisp" required>
                                <?php
                                $sql_danhmucsp = "SELECT DISTINCT category_id, category_name FROM category WHERE status_id = 1";
                                $query_danhmucsp = mysqli_query($conn, $sql_danhmucsp);
                                while ($row_danhmucsp = mysqli_fetch_array($query_danhmucsp)) {
                                ?>
                                    <option value="<?php echo $row_danhmucsp['category_id'] ?>"><?php echo $row_danhmucsp['category_name'] ?></option>
                                <?php
                                } 
                                ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tinhtrang">Trạng thái</label>
                <select id="tinhtrang" name="tinhtrang" required>
                    <option value="1">Hoạt động</option>
                    <option value="2">Không hoạt động</option>
                </select>
            </div>
            <div class="form-group">
                <label for="hinhanh">Hình ảnh sản phẩm</label>
                <input type="file" id="hinhanh" name="hinhanh" required>
            </div>
            <div class="form-group">
                <button type="submit" name="themsanpham">Thêm sản phẩm</button>
            </div>
        </form>
    </div>
</body>
</html>