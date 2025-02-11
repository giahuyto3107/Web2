<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
</head>
<body>
    <h1>Thêm sản phẩm mới</h1>
    <form action="../../../BackEnd/Model/quanlisanpham/themsp.php" method="POST">
        <label for="product_name">Tên sản phẩm:</label>
        <input type="text" id="product_name" name="product_name" required><br><br>

        <label for="product_description">Mô tả sản phẩm:</label>
        <textarea id="product_description" name="product_description" required></textarea><br><br>

        <label for="price">Giá:</label>
        <input type="number" id="price" name="price" step="0.01" required><br><br>

        <label for="stock_quantity">Số lượng tồn kho:</label>
        <input type="number" id="stock_quantity" name="stock_quantity" required><br><br>

        <label for="category_id">ID danh mục:</label>
        <input type="number" id="category_id" name="category_id" required><br><br>

        <label for="status_id">ID trạng thái:</label>
        <input type="number" id="status_id" name="status_id" required><br><br>

        <label for="image_url">URL hình ảnh:</label>
        <input type="text" id="image_url" name="image_url"><br><br>

        <input type="submit" value="Thêm sản phẩm">
    </form>
</body>
</html>