<?php
include("../../../BackEnd/Config/config.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchase_order_id = isset($_POST['purchase_order_id']) ? intval($_POST['purchase_order_id']) : 0;
    $import_status = isset($_POST['import_status']) ? intval($_POST['import_status']) : 0;

    if ($purchase_order_id <= 0 || $import_status !== 1) {
        echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    // Kiểm tra xem phiếu nhập có import_status = 0 không
    $check_sql = "SELECT import_status FROM purchase_order WHERE purchase_order_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $purchase_order_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Phiếu nhập không tồn tại']);
        exit;
    }

    if ($row['import_status'] != 0) {
        echo json_encode(['status' => 'error', 'message' => 'Phiếu nhập đã được duyệt hoặc không hợp lệ']);
        exit;
    }

    // Cập nhật import_status thành 1
    $purchaseOrderSql = "UPDATE purchase_order SET import_status = ? WHERE purchase_order_id = ?";
    $purchaseOrderStmt = mysqli_prepare($conn, $purchaseOrderSql);
    mysqli_stmt_bind_param($purchaseOrderStmt, "ii", $import_status, $purchase_order_id);

    if (mysqli_stmt_execute($purchaseOrderStmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Phiếu nhập đã được duyệt']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi duyệt phiếu nhập: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($purchaseOrderStmt);
    mysqli_close($conn);


    // Cập nhật import_status của chi tiết thành 1
    $purchaseOrderItemSql = "UPDATE purchase_order_items SET import_status = ? WHERE purchase_order_id = ?";
    $ItemStmt = mysqli_prepare($conn, $purchaseOrderSql);
    mysqli_stmt_bind_param($ItemStmt, "ii", $import_status, $purchase_order_id);

    if (mysqli_stmt_execute($ItemStmt)) {
        echo json_encode(['status' => 'success', 'message' => 'chi tiết phiếu nhập đã được duyệt']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi duyệt chi tiết phiếu nhập: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($ItemStmt);
    mysqli_close($conn);


    $fetchProductSql = "Select product_id, quantity, price, profit from Product where status_id = 1";
    $productStmt = mysqli_prepare($conn, $fetchProductSql);
    for ()
} else {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ']);
}


// DELIMITER //
// CREATE TRIGGER after_price_update
// AFTER UPDATE ON product
// FOR EACH ROW
// BEGIN
//     IF NEW.price != OLD.price THEN
//         INSERT INTO price_history (
//             product_id,
//             old_price,
//             new_price,
//             changed_by,
//             reason
//         ) VALUES (
//             NEW.product_id,
//             OLD.price,
//             NEW.price,
//             -- Giả sử có biến @current_user_id từ ứng dụng
//             IFNULL(@current_user_id, NULL), 
//             CONCAT('Tự động cập nhật. Giá thay đổi từ ', 
//                   OLD.price, ' → ', NEW.price)
//         );
//     END IF;
// END //
// DELIMITER ;
?>