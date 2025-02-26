<?php
include ('../../../BackEnd/Config/config.php');

$search_name = $_GET['search_name'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 8;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM product WHERE 1=1";

if ($search_name) {
    $query .= " AND product_name LIKE '%$search_name%'";
}
if ($category) {
    $query .= " AND category_id = $category";
}
if ($min_price) {
    $query .= " AND price >= $min_price";
}
if ($max_price) {
    $query .= " AND price <= $max_price";
}

$total_query = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);

$query .= " LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

$uploads_path = "../../../BackEnd/Uploads/Product Picture/";
$products = [];
while ($row = $result->fetch_assoc()) {
    $row['image_url'] = $uploads_path . htmlspecialchars($row['image_url']);
    $products[] = $row;
}
echo json_encode(['products' => $products, 'total_pages' => $total_pages]);



$conn->close();
?>
