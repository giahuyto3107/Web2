<?php
require_once '../../../BackEnd/Config/database.php';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;
$db = new Database();
$data = $db->getMonthlyRevenue($date_from, $date_to);
if ($data !== false) {
    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "Lỗi khi lấy dữ liệu doanh thu"]);
}
?>