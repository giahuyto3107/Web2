<?php
include('../../../BackEnd/Config/config.php');
include('../../../BackEnd/Model/phantrang.php');
session_start();

// Kiểm tra xem user_id có tồn tại không
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['orders' => [], 'total_pages' => 0, 'current_page' => 1]);
    exit;
}

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';


$sql = "SELECT orders.order_id, orders.order_date, orders.total_amount, 
               status.status_name, orders.phone, orders.address, orders.payment_method
        FROM orders 
        JOIN status ON status.id = orders.status_id
        WHERE orders.user_id = $user_id";

if ($search !== "") {
    $sql .= " AND (orders.order_id LIKE '%$search%')";
}

if ($status !== "") {
    $sql .= " AND orders.status_id = $status";
}

$sql .= " ORDER BY orders.order_date DESC";

$result = mysqli_query($conn, $sql);
if (!$result) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit;
}

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