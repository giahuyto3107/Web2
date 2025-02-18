<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ["success" => false, "message" => ""];

    if (!empty($_POST['address'])) {
        $_SESSION['user_address'] = $_POST['address'];
        $response["success"] = true;
        $response["message"] .= "ok";
    }

    if (!empty($_POST['phone'])) {
        $_SESSION['user_phone'] = $_POST['phone'];
        $response["success"] = true;
        $response["message"] .= "ok";
    }

    if (!$response["success"]) {
        $response["message"] = "not ok";
    }

    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "message" => "Yêu cầu không hợp lệ!"]);
}
?>
