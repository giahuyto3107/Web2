<?php
include("../../../BackEnd/Config/config.php");

header('Content-Type: application/json');

// Lấy purchase_order_id từ query string
$purchase_order_id = isset($_GET['purchase_order_id']) ? intval($_GET['purchase_order_id']) : 0;

if ($purchase_order_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID phiếu nhập không hợp lệ']);
    exit;
}

// Truy vấn thông tin chung của phiếu nhập
$sql_order = "SELECT u.full_name AS user_name, s.supplier_name, po.order_date
              FROM purchase_order po
              JOIN supplier s ON po.supplier_id = s.supplier_id
              JOIN user u ON po.user_id = u.user_id
              WHERE po.purchase_order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $purchase_order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order_info = $result_order->fetch_assoc();

// Truy vấn danh sách sản phẩm
$sql_items = "SELECT p.product_id, p.image_url, p.product_name, pot.quantity, pot.price, pot.profit
              FROM purchase_order_items pot
              JOIN product p ON pot.product_id = p.product_id
              WHERE pot.purchase_order_id = ?
              ORDER BY p.product_name ASC";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $purchase_order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$items = [];
$total_value = 0.0;
while ($row = $result_items->fetch_assoc()) {
    $profit = floatval($row['profit']);
    $quantity = floatval($row['quantity']);
    $price = floatval($row['price']);
    $item_total = (1 + $profit / 100.0) * $quantity * $price;
    $total_value += $item_total;

    $items[] = [
        'product_id' => $row['product_id'], // Thêm product_id
        'image_url' => $row['image_url'],
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'profit' => $row['profit']
    ];
}

// Trả về dữ liệu JSON
$response = [
    'status' => 'success',
    'data' => [
        'order_info' => $order_info,
        'items' => $items,
        'total_value' => $total_value
    ]
];

echo json_encode($response);

$stmt_order->close();
$stmt_items->close();
$conn->close();
?>