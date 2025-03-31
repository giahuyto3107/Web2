<?php
include("../../../BackEnd/Config/config.php");

header('Content-Type: application/json');

// Sử dụng prepared statement để tăng bảo mật
$sql_pn = "SELECT po.purchase_order_id, 
                  s.supplier_name, 
                  u.full_name AS user_name, 
                  po.order_date, 
                  po.total_amount AS amount,
                  po.total_price, 
                  po.status_id, 
                  po.import_status
           FROM purchase_order po
           JOIN supplier s ON s.supplier_id = po.supplier_id
           JOIN purchase_order_items pot ON po.purchase_order_id = pot.purchase_order_id
           JOIN user u ON u.user_id = po.user_id
           WHERE pot.purchase_order_item_id = (
               SELECT MIN(pot2.purchase_order_item_id)
               FROM purchase_order_items pot2
               WHERE pot2.purchase_order_id = po.purchase_order_id
           )
           ORDER BY po.purchase_order_id ASC";

$stmt = $conn->prepare($sql_pn);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $conn->error]);
    exit;
}

$purchase_orders = [];
while ($row = $result->fetch_assoc()) {
    $purchase_orders[] = [
        'purchase_order_id' => $row['purchase_order_id'],
        'supplier_name' => $row['supplier_name'],
        'user_name' => $row['user_name'],
        'order_date' => $row['order_date'],
        'amount' => $row['amount'],
        'total_price' => $row['total_price'],
        'status_id' => $row['status_id'],
        'import_status' => $row['import_status']
    ];
}

echo json_encode(['status' => 'success', 'data' => $purchase_orders]);

$stmt->close();
$conn->close();
?>