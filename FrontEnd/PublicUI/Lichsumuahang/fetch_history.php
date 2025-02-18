<?php
include ('../../../BackEnd/Config/config.php');
$user_id = 1;
$limit=5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search= isset($_GET['search'])? mysqli_real_escape_string($conn, $_GET['search']):'';
$status= isset($_GET['status'])? mysqli_real_escape_string($conn, $_GET['status']):'';

$offset=($page-1)*$limit;

$sql="SELECT orders.order_id, orders.order_date, orders.total_amount, status.status_name
      FROM orders 
      JOIN status on status.id=orders.status_id
      WHERE orders.user_id=$user_id";

if($search!==""){
    $sql .= " AND (orders.order_id LIKE '%$search%')";
}

if($status!==""){
    $sql .= " AND (orders.status_id=$status)";
}

$total_query = $sql;
$sql .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
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
