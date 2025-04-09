<?php
include '../../../BackEnd/Config/config.php';
header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

if ($user_id <= 0 || empty($date_from) || empty($date_to)) {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$sql = "SELECT o.order_id, o.order_date, o.total_amount, o.status_id
        FROM orders o
        WHERE o.user_id = ? AND o.order_date BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $date_from, $date_to);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $orders]);
$stmt->close();
$conn->close();
?>