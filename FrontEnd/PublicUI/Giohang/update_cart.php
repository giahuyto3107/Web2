<?php
session_start();
include ('../../../BackEnd/Config/config.php');

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$action = $_POST['action'];

$response = [
    "success" => false,
    "new_quantity" => 0,
    "new_total_price" => 0,
    "cart_total" => 0,
    "message" => ""
];

if ($action === "increase") {
    $check_sql = "SELECT cart_items.quantity, product.stock_quantity 
                  FROM cart_items 
                  JOIN product ON cart_items.product_id = product.product_id 
                  WHERE cart_items.user_id = $user_id AND cart_items.product_id = $product_id";
    $check_result = mysqli_query($conn, $check_sql);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row['quantity'] >= $check_row['stock_quantity']) {
        $response["message"] = "Số lượng đã đạt tối đa trong kho!";
    } else {
        $sql = "UPDATE cart_items SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id";
        if (mysqli_query($conn, $sql)) {
            $response["success"] = true;
        }
    }
} elseif ($action === "decrease") {
    $sql = "UPDATE cart_items SET quantity = quantity - 1 WHERE user_id = $user_id AND product_id = $product_id AND quantity > 1";
    if (mysqli_query($conn, $sql)) {
        $response["success"] = true;
    }
}

$sql = "SELECT quantity, price FROM cart_items JOIN product ON cart_items.product_id = product.product_id WHERE cart_items.user_id = $user_id AND cart_items.product_id = $product_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$response["new_quantity"] = $row['quantity'];
$response["new_total_price"] = $row['quantity'] * $row['price'];


$sql = "SELECT SUM(cart_items.quantity * product.price) AS cart_total FROM cart_items JOIN product ON cart_items.product_id = product.product_id WHERE cart_items.user_id = $user_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$response["cart_total"] = $row['cart_total'];

mysqli_close($conn);

echo json_encode($response);
?>