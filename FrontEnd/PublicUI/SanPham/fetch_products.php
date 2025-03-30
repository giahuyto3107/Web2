<?php
include ('../../../BackEnd/Config/config.php');
include ('phantrang.php'); // Include class Pagination

$search_name = $_GET['search_name'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Lấy tất cả sản phẩm phù hợp với điều kiện tìm kiếm
$query = "SELECT p.* FROM product p 
          LEFT JOIN product_category pc ON p.product_id = pc.product_id 
          LEFT JOIN category c ON pc.category_id = c.category_id 
          WHERE 1=1";
if ($search_name) {
    $query .= " AND p.product_name LIKE '%" . $conn->real_escape_string($search_name) . "%'";
}
if ($category) {
    $query .= " AND c.category_id = " . intval($category);
}
if ($min_price) {
    $query .= " AND p.price >= " . floatval($min_price);
}
if ($max_price) {
    $query .= " AND p.price <= " . floatval($max_price);
}
$query .= " GROUP BY p.product_id";

$result = $conn->query($query);
$products = [];
$uploads_path = "../../../BackEnd/Uploads/Product Picture/";
while ($row = $result->fetch_assoc()) {
    $row['image_url'] = $uploads_path . htmlspecialchars($row['image_url']);
    $products[] = $row;
}

// Sử dụng class Pagination để phân trang
$pagination = new Pagination(2); // 8 sản phẩm mỗi trang
$paginated_result = $pagination->paginate($products);

// Trả về JSON
echo json_encode([
    'products' => $paginated_result['items'],
    'total_pages' => $paginated_result['total_pages'],
    'current_page' => $paginated_result['current_page']
]);

$conn->close();
?>