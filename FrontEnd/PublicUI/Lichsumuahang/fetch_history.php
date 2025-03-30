<?php
include ('../../../BackEnd/Config/config.php');
include ('../../../BackEnd/Model/phantrang.php');

$user_id = 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

$sql = "SELECT orders.order_id, orders.order_date, orders.total_amount, status.status_name
        FROM orders 
        JOIN status ON status.id = orders.status_id
        WHERE orders.user_id = $user_id";

if ($search !== "") {
    $sql .= " AND (orders.order_id LIKE '%$search%')";
}

if ($status !== "") {
    $sql .= " AND (orders.status_id = $status)";
}


$result = mysqli_query($conn, $sql);
$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}


$pagination = new Pagination(5); 
$paginated_result = $pagination->paginate($orders);


echo json_encode([
    'orders' => $paginated_result['items'],
    'total_pages' => $paginated_result['total_pages'],
    'current_page' => $paginated_result['current_page']
]);

mysqli_close($conn);
?>