<?php
include("../../../BackEnd/Config/config.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchase_order_id = isset($_POST['purchase_order_id']) ? intval($_POST['purchase_order_id']) : 0;
    $import_status = isset($_POST['import_status']) ? intval($_POST['import_status']) : 0;

    if ($purchase_order_id <= 0 || $import_status !== 1) {
        echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
        mysqli_close($conn);
        exit;
    }

    // Kiểm tra xem phiếu nhập có import_status = 0 không
    $check_sql = "SELECT import_status FROM purchase_order WHERE purchase_order_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $purchase_order_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($check_stmt);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Phiếu nhập không tồn tại']);
        mysqli_close($conn);
        exit;
    }

    if ($row['import_status'] != 0) {
        echo json_encode(['status' => 'error', 'message' => 'Phiếu nhập đã được duyệt hoặc không hợp lệ']);
        mysqli_close($conn);
        exit;
    }

    $response = ['status' => 'success', 'messages' => []];
    $isSuccess = true;

    // Cập nhật import_status thành 1
    $purchaseOrderSql = "UPDATE purchase_order SET import_status = ?, approve_date = now() WHERE purchase_order_id = ?";
    $purchaseOrderStmt = mysqli_prepare($conn, $purchaseOrderSql);
    mysqli_stmt_bind_param($purchaseOrderStmt, "ii", $import_status, $purchase_order_id);

    if (mysqli_stmt_execute($purchaseOrderStmt)) {
        $response['messages'][] = 'Phiếu nhập đã được duyệt';
    } else {
        $isSuccess = false;
        $response['status'] = 'error';
        $response['messages'][] = 'Lỗi khi duyệt phiếu nhập: ' . mysqli_error($conn);
        mysqli_close($conn);
        exit;
    }

    mysqli_stmt_close($purchaseOrderStmt);

    // Cập nhật import_status của chi tiết thành 1
    $purchaseOrderItemSql = 
        "UPDATE purchase_order_items 
        SET import_status = ?, approve_date = NOW() 
        WHERE purchase_order_id = ?";

    $ItemStmt = mysqli_prepare($conn, $purchaseOrderItemSql);
    mysqli_stmt_bind_param($ItemStmt, "ii", $import_status, $purchase_order_id);

    if (mysqli_stmt_execute($ItemStmt)) {
        $response['messages'][] = 'Chi tiết phiếu nhập đã được duyệt và thời gian xác nhận đã được cập nhật';
    } else {
        $isSuccess = false;
        $response['status'] = 'error';
        $response['messages'][] = 'Lỗi khi duyệt chi tiết phiếu nhập: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($ItemStmt);

    // Cập nhật số lượng Product
    if ($isSuccess == true) {
        $updateProductQuantity = 
            "UPDATE product p
            JOIN (
                SELECT product_id, SUM(quantity) AS total_quantity
                FROM purchase_order_items
                WHERE import_status = 1 AND purchase_order_id = ?
                GROUP BY product_id
            ) poi ON p.product_id = poi.product_id
            SET p.stock_quantity = p.stock_quantity + poi.total_quantity;";
        $updateProductSmt = mysqli_prepare($conn, $updateProductQuantity);
        mysqli_stmt_bind_param($updateProductSmt, "i", $purchase_order_id);

        if (mysqli_stmt_execute($updateProductSmt)) {
            $response['messages'][] = 'Sản phẩm đã được cập nhật số lượng';
        } else {
            $isSuccess = false;
            $response['status'] = 'error';
            $response['messages'][] = 'Lỗi khi cập nhật số lượng sản phẩm: ' . mysqli_error($conn);
        }
    
        mysqli_stmt_close($updateProductSmt);
    }

    // **Gọi stored procedure để cập nhật giá sản phẩm**
    if ($isSuccess == true) {
        // Lấy danh sách product_id từ purchase_order_items
        $queryProductIds = "
            SELECT product_id, price AS new_cost_price, profit
            FROM purchase_order_items 
            WHERE purchase_order_id = ? AND import_status = 1";

        $stmtProductIds = mysqli_prepare($conn, $queryProductIds);
        mysqli_stmt_bind_param($stmtProductIds, "i", $purchase_order_id);
        mysqli_stmt_execute($stmtProductIds);
        $resultProductIds = mysqli_stmt_get_result($stmtProductIds);

        while ($row = mysqli_fetch_assoc($resultProductIds)) {
            $product_id = $row['product_id'];
            $new_cost_price = $row['new_cost_price'];
            $profit_margin = $row['profit'];

            // Lấy giá bán hiện tại
            $querySellingPrice = "SELECT price FROM product WHERE product_id = ?";
            $stmtSellingPrice = mysqli_prepare($conn, $querySellingPrice);
            mysqli_stmt_bind_param($stmtSellingPrice, "i", $product_id);
            mysqli_stmt_execute($stmtSellingPrice);
            $resultSellingPrice = mysqli_stmt_get_result($stmtSellingPrice);
            $sellingPriceRow = mysqli_fetch_assoc($resultSellingPrice);
            mysqli_stmt_close($stmtSellingPrice);

            if ($sellingPriceRow) {
                $current_selling_price = $sellingPriceRow['price']; 
                
                // Tính lợi nhuận theo công thức (profit_margin = (Giá bán / Giá nhập) - 1)
                if ($new_cost_price > 0) {
                    $profit_margin_decimal = $profit_margin / 100.0; // Convert to decimal
                    // Gọi stored procedure để cập nhật giá bán
                    $callProcedure = "CALL UpdateProductPriceWithMaxProfit(?, ?, ?)";
                    $stmtProcedure = mysqli_prepare($conn, $callProcedure);
                    mysqli_stmt_bind_param($stmtProcedure, "idd", $product_id, $new_cost_price, $profit_margin_decimal);

                    if (mysqli_stmt_execute($stmtProcedure)) {
                        $response['messages'][] = "Đã cập nhật giá sản phẩm ID: $product_id với giá nhập: $new_cost_price và lợi nhuận: " . round($profit_margin_decimal * 100, 2) . "%";
                    } else {
                        $response['messages'][] = "Lỗi khi cập nhật giá sản phẩm ID: $product_id: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmtProcedure);
                } else {
                    $response['messages'][] = "Bỏ qua sản phẩm ID: $product_id do giá nhập không hợp lệ.";
                }
            }
        }

        mysqli_stmt_close($stmtProductIds);
    }

    // Trả về JSON phản hồi
    echo json_encode($response);
    mysqli_close($conn);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ']);
}
?>
