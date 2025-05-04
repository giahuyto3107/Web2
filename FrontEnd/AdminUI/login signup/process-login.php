<?php
session_start();
include ('../../../BackEnd/Config/config.php');
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["success" => false, "message" => "Có lỗi xảy ra!"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $response["message"] = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        // Kiểm tra tài khoản trong database
        $sql = "SELECT account_id, account_name, email, password_hash, role_id FROM account WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password_hash'])) {
                // Kiểm tra role_id
                if ($user['role_id'] == 2) {
                    $response["message"] = "Tài khoản không có quyền truy cập.";
                } else {
                    // Đăng nhập thành công, lưu session
                    $_SESSION["user_id"] = $user["account_id"];
                    $_SESSION["user_name"] = $user["account_name"];
                    $_SESSION["user_email"] = $user["email"];
                    $response["success"] = true;
                    $response["message"] = "Đăng nhập thành công!";
                    $response["redirect"] = "index.php"; // Chuyển hướng đến trang chủ
                }
            } else {
                $response["message"] = "Mật khẩu không đúng.";
            }
        } else {
            $response["message"] = "Tài khoản không tồn tại.";
        }
    }
}

// Trả về kết quả dưới dạng JSON
echo json_encode($response);
exit();