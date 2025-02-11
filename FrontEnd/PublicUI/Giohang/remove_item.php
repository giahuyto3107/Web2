<?php
session_start();
include ('../../../BackEnd/Config/config.php');

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']); 
    $user_id = 1; 

    $sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Sản phẩm đã được xóa khỏi giỏ hàng!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi xóa sản phẩm: " . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["success" => false, "message" => "Không có sản phẩm nào được chọn để xóa."]);
}

mysqli_close($conn);
?>
