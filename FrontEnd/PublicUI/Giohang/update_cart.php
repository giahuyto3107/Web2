<?php
session_start();
include ('../../../BackEnd/Config/config.php');

$user_id = 1;
$product_id = $_POST['product_id'];
$action = $_POST['action'];

if ($action === "increase") {
    $sql = "UPDATE cart_items SET quantity = quantity +1 WHERE user_id = $user_id AND product_id = $product_id";
} elseif ($action === "decrease") {
    $sql = "UPDATE cart_items SET quantity = quantity -1  WHERE user_id = $user_id AND product_id = $product_id AND quantity > 1";
}

mysqli_query($conn, $sql);


$sql = "SELECT quantity, price FROM cart_items JOIN product ON cart_items.product_id = product.product_id WHERE cart_items.user_id = $user_id AND cart_items.product_id = $product_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$new_quantity = $row['quantity'];
$new_total_price = $row['quantity'] * $row['price'];



$sql = "SELECT SUM(cart_items.quantity * product.price) AS cart_total FROM cart_items JOIN product ON cart_items.product_id = product.product_id WHERE cart_items.user_id = $user_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$cart_total = $row['cart_total'];

mysqli_close($conn);

echo json_encode([
    "success" => true,
    "new_quantity" => $new_quantity,
    "new_total_price" => $new_total_price,
    "cart_total" => $cart_total
]);
?>
