<?php

include '../../Config/config.php'; // Đường dẫn tương đối đến config.php

// Lấy dữ liệu từ form
$product_name = $_POST['product_name'];
$product_description = $_POST['product_description'];
$price = $_POST['price'];
$stock_quantity = $_POST['stock_quantity'];
$category_id = $_POST['category_id'];
$status_id = $_POST['status_id'];
$image_url = $_POST['image_url'];

$sql = "INSERT INTO product (product_name, product_description, price, stock_quantity, category_id, status_id, image_url)
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdiiis", $product_name, $product_description, $price, $stock_quantity, $category_id, $status_id, $image_url);

if ($stmt->execute()) {
    echo "Sản phẩm đã được thêm thành công!";
} else {
    echo "Lỗi: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>