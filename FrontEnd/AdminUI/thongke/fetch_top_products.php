<?php
require_once '../../../BackEnd/Config/database.php';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;
$db = new Database();
$data = $db->getTopProducts($date_from, $date_to);
$formattedData = [];
foreach ($data as $product_name => $stats) {
    $formattedData[] = [
        'product_name' => $product_name,
        'total_sold' => $stats['total_sold'],
        'total_revenue' => $stats['total_revenue']
    ];
}
if ($data !== false) {
    echo json_encode(["status" => "success", "data" => $formattedData]);
} else {
    echo json_encode(["status" => "error", "message" => "Lỗi khi lấy dữ liệu top sản phẩm"]);
}
?>