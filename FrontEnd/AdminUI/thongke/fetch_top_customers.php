<?php
include '../../../BackEnd/Config/config.php';
header('Content-Type: application/json');

$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

if (empty($date_from) || empty($date_to)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng cung cấp khoảng thời gian']);
    exit;
}

$sql = "SELECT u.user_id, u.full_name AS user_name, COUNT(o.order_id) AS order_count, SUM(o.total_amount) AS total_spent
        FROM orders o
        JOIN user u ON o.user_id = u.user_id
        WHERE o.order_date BETWEEN ? AND ?
        GROUP BY u.user_id, u.full_name
        ORDER BY total_spent DESC
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $date_from, $date_to);
$stmt->execute();
$result = $stmt->get_result();

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $customers]);
$stmt->close();
$conn->close();
?>