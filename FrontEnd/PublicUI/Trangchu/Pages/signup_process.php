<?php
session_start();
include ('../../../../BackEnd/Config/config.php');

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["status" => "error", "message" => "Có lỗi xảy ra!"];

if (!$conn) {
    $response["message"] = "Lỗi kết nối database.";
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($name) || empty($email) || empty($address) || empty($password) || empty($confirm_password)) {
        $response["message"] = "Vui lòng nhập đầy đủ thông tin.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = "Email không hợp lệ!";
    } elseif (strlen($password) < 6) {
        $response["message"] = "Mật khẩu phải có ít nhất 6 ký tự!";
    } else {
        //  Kiểm tra tên tài khoản đã tồn tại trước
        $stmt_check_name = $conn->prepare("SELECT account_id FROM account WHERE account_name = ?");
        $stmt_check_name->bind_param("s", $name);
        $stmt_check_name->execute();
        $stmt_check_name->store_result();

        if ($stmt_check_name->num_rows > 0) {
            $response["message"] = "Tên người dùng đã được sử dụng. Vui lòng chọn tên khác.";
        } else {
            //  Kiểm tra email đã tồn tại
            $stmt_check_email = $conn->prepare("SELECT account_id FROM account WHERE email = ?");
            $stmt_check_email->bind_param("s", $email);
            $stmt_check_email->execute();
            $stmt_check_email->store_result();

            if ($stmt_check_email->num_rows > 0) {
                $response["message"] = "Email đã được sử dụng.";
            } elseif ($password !== $confirm_password) {
                $response["message"] = "Mật khẩu xác nhận không khớp.";
            } else {
                // Thực hiện đăng ký
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt_insert = $conn->prepare("INSERT INTO account (account_name, email, password_hash, status_id, role_id, created_at, updated_at) VALUES (?, ?, ?, 1, 2, NOW(), NOW())");
                $stmt_insert->bind_param("sss", $name, $email, $hashed_password);

                if ($stmt_insert->execute()) {
                    $new_account_id = $stmt_insert->insert_id;

                    //  Thêm address vào bảng user
                    $stmt_user = $conn->prepare("INSERT INTO user (account_id, full_name, address, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $stmt_user->bind_param("iss", $new_account_id, $name, $address);
                    $stmt_user->execute();
                    $stmt_user->close();

                    $_SESSION['dangky'] = $name;
                    $response["status"] = "success";
                    $response["message"] = "Đăng ký thành công!";
                } else {
                    $response["message"] = "Có lỗi xảy ra. Vui lòng thử lại.";
                }

                $stmt_insert->close();
            }

            $stmt_check_email->close();
        }

        $stmt_check_name->close();
    }
}

echo json_encode($response);
exit();
