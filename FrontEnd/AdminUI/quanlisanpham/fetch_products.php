<?php
// fetch_products.php
include '../../../BackEnd/Config/config.php';

// Thiết lập header để trả về JSON
header('Content-Type: application/json');

// Truy vấn lấy dữ liệu từ bảng product, join với product_category và category
$sql = "
    SELECT 
        p.product_id,
        p.product_name,
        p.product_description,
        p.price,
        p.stock_quantity,
        p.status_id,
        p.image_url,
        p.created_at,
        p.updated_at,
        GROUP_CONCAT(pc.category_id) AS category_ids,
        GROUP_CONCAT(c.category_name) AS category_names
    FROM product p
    LEFT JOIN product_category pc ON p.product_id = pc.product_id
    LEFT JOIN category c ON pc.category_id = c.category_id
    GROUP BY p.product_id
    ORDER BY p.product_id ASC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    // Nếu truy vấn thất bại, trả về lỗi
    $response = array(
        'status' => 'error',
        'message' => 'Database query failed: ' . mysqli_error($conn)
    );
    echo json_encode($response);
    exit;
}

// Tạo mảng để lưu dữ liệu
$products = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Xử lý đường dẫn hình ảnh
        $imagePath = !empty($row['image_url']) 
            ? "../../BackEnd/Uploads/Product Picture/" . $row['image_url'] 
            : "../../BackEnd/Uploads/Product Picture/default.jpg";

        // Xử lý danh sách category_ids và category_names
        $category_ids = !empty($row['category_ids']) ? explode(',', $row['category_ids']) : [];
        $category_names = !empty($row['category_names']) ? explode(',', $row['category_names']) : [];

        // Thêm dữ liệu vào mảng
        $products[] = array(
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'product_description' => $row['product_description'],
            'price' => floatval($row['price']), // Chuyển price sang float
            'stock_quantity' => intval($row['stock_quantity']), // Chuyển stock_quantity sang int
            'status_id' => $row['status_id'],
            'image_url' => $imagePath,
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'category_ids' => $category_ids, // Mảng các category_id
            'category_names' => $category_names // Mảng các category_name
        );
    }
}

// Trả về dữ liệu dưới dạng JSON
$response = array(
    'status' => 'success',
    'data' => $products
);

echo json_encode($response);

// Đóng kết nối database
mysqli_close($conn);
?>