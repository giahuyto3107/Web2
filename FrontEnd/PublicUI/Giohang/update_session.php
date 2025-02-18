<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST["product_id"];
    $checked = filter_var($_POST["checked"], FILTER_VALIDATE_BOOLEAN);

    if (!isset($_SESSION["selected_products"])) {
        $_SESSION["selected_products"] = [];
    }

    if ($checked) {
        if (!in_array($product_id, $_SESSION["selected_products"])) {
            $_SESSION["selected_products"][] = $product_id;
        }
    } else {
        $_SESSION["selected_products"] = array_diff($_SESSION["selected_products"], [$product_id]);
    }

    echo json_encode(["status" => "success", "selected_products" => $_SESSION["selected_products"]]);
    exit();
}
?>
