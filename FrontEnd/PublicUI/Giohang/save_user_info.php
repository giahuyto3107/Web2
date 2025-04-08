<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ["success" => false, "message" => ""];
    $allowed_keys = ['address', 'phone'];

    foreach ($_POST as $key => $value) {
        if (in_array($key, $allowed_keys) && !empty($value) && $value !== 'undefined') {
            $_SESSION["user_" . $key] = $value;
            $response["success"] = true;
            $response["message"] .= "Đã lưu $key: $value. ";
        }
    }

    if (!$response["success"]) {
        $response["message"] = "Không có dữ liệu hợp lệ để lưu";
    }

    $response["session"] = $_SESSION;
    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "message" => "Yêu cầu không hợp lệ!"]);
}
?>