<?php

// Lấy ID sản phẩm từ URL
if (isset($_GET['idsanpham'])) {
    $idsanpham = $_GET['idsanpham'];

    // Truy vấn để lấy thông tin sản phẩm cần sửa
    $sql_sua = "SELECT * FROM product WHERE product_id = '$idsanpham'";
    $query_sua = mysqli_query($conn, $sql_sua);
    $row = mysqli_fetch_assoc($query_sua);

    if (!$row) {
        echo "Không tìm thấy sản phẩm!";
        exit;
    }
} else {
    echo "Không có ID sản phẩm!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[type="file"] {
            padding: 5px;
        }
        .form-group button {
            padding: 10px 15px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sửa sản phẩm</h2>
        <form method="POST" action="../../BackEnd/Model/quanlisanpham/xulisanpham.php?idsanpham=<?php echo $idsanpham; ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="tensanpham">Tên sản phẩm</label>
                <input type="text" id="tensanpham" name="tensanpham" value="<?php echo $row['product_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="motasp">Mô tả sản phẩm</label>
                <textarea id="motasp" name="motasp" rows="4" required><?php echo $row['product_description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="giasp">Giá sản phẩm</label>
                <input type="number" id="giasp" name="giasp" step="0.01" value="<?php echo $row['price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="loaisp">Loại sản phẩm</label>
                <select id="loaisp" name="loaisp" required>
                    <option value="1" <?php echo ($row['category_id'] == 1) ? 'selected' : ''; ?>>Loại 1</option>
                    <option value="2" <?php echo ($row['category_id'] == 2) ? 'selected' : ''; ?>>Loại 2</option>
                    <!-- Thêm các loại sản phẩm khác nếu cần -->
                </select>
            </div>
            <div class="form-group">
                <label for="tinhtrang">Trạng thái</label>
                <select id="tinhtrang" name="tinhtrang" required>
                    <option value="1" <?php echo ($row['status_id'] == 1) ? 'selected' : ''; ?>>Active</option>
                    <option value="2" <?php echo ($row['status_id'] == 2) ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="hinhanh">Hình ảnh sản phẩm</label>
                <input type="file" id="hinhanh" name="hinhanh">
                <p>Hình ảnh hiện tại: <img src="../../BackEnd/Uploads/Product Picture/<?php echo $row['image_url']; ?>" alt="Hình ảnh sản phẩm" width="100"></p>
            </div>
            <div class="form-group">
                <button type="submit" name="suasanpham">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</body>
</html>