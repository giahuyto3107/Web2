<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

try {
    // Lấy danh sách sản phẩm
    $sql = "SELECT p.product_id, p.product_name, p.product_description, p.price, p.stock_quantity, p.status_id, p.image_url 
            FROM product p 
            WHERE p.status_id != 6"; // Không lấy sản phẩm đã bị xóa (status_id = 6)
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];

        // Lấy danh sách thể loại của sản phẩm
        $category_sql = "SELECT c.category_name 
                         FROM product_category pc 
                         JOIN category c ON pc.category_id = c.category_id 
                         WHERE pc.product_id = ?";
        $category_stmt = $conn->prepare($category_sql);
        $category_stmt->bind_param("i", $product_id);
        $category_stmt->execute();
        $category_result = $category_stmt->get_result();

        $categories = [];
        while ($category_row = $category_result->fetch_assoc()) {
            $categories[] = $category_row['category_name'];
        }
        $category_stmt->close();

        // Thêm danh sách thể loại vào sản phẩm
        $row['categories'] = implode(', ', $categories);

        // Xử lý đường dẫn hình ảnh
        $imagePath = !empty($row['image_url']) 
            ? "../../BackEnd/Uploads/Product Picture/" . $row['image_url'] 
            : "../../BackEnd/Uploads/Product Picture/default.jpg";
        $row['image_url'] = $imagePath;

        $products[] = $row;
    }

    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi khi lấy dữ liệu sản phẩm: ' . $e->getMessage()
    ]);
}

$conn->close();
?>