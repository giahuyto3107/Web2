<?php
include ('../../../BackEnd/Config/config.php');

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';


$offset = ($page - 1) * $limit;

$query = "SELECT category.category_id, category.category_name, category.category_description, status.id
          FROM category 
          JOIN status ON status.id = category.status_id 
          WHERE 1"; 

if ($search !== '') {
    $query .= " AND (category.category_name LIKE '%$search%')";
}
if ($status !== '') {
    $query .= " AND status.id = '$status'";
}

$total_query = $query;
$query .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
$category = [];

while ($row = mysqli_fetch_assoc($result)) {
    $category[] = $row;
}


$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_num_rows($total_result);
$total_pages = ceil($total_records / $limit);

echo json_encode([
    'category' => $category,
    'total_pages' => $total_pages
]);

mysqli_close($conn);
?>
