<?php
$conn = mysqli_connect("localhost", "root", "", "web2_sql");

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5; 
$offset = ($page - 1) * $limit;

$query = "SELECT orders.order_id, user.full_name, orders.order_date, orders.payment_method, status.id 
          FROM orders 
          JOIN user ON orders.user_id = user.user_id 
          JOIN status ON status.id = orders.status_id 
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

$total_query = "SELECT COUNT(*) as total FROM orders";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $limit);


$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    'orders' => $data,
    'total_pages' => $total_pages
]);

mysqli_close($conn);
?>
