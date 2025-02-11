<?php
include ('../../../BackEnd/Config/config.php');

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';


$offset = ($page - 1) * $limit;

$query = "SELECT orders.order_id, user.full_name, orders.order_date, orders.payment_method, status.id, orders.address 
          FROM orders 
          JOIN user ON orders.user_id = user.user_id 
          JOIN status ON status.id = orders.status_id 
          WHERE 1"; 

if ($search !== '') {
    $query .= " AND (user.full_name LIKE '%$search%' OR orders.address LIKE '%$search%')";
}
if ($status !== '') {
    $query .= " AND status.id = '$status'";
}

$total_query = $query;
$query .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}


$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_num_rows($total_result);
$total_pages = ceil($total_records / $limit);

echo json_encode([
    'orders' => $orders,
    'total_pages' => $total_pages
]);

mysqli_close($conn);
?>
