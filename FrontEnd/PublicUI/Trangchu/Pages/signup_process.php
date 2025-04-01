<?php
session_start();
include ('../../../../BackEnd/Config/config.php');

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["status" => "error", "message" => "Có lỗi xảy ra!"];

// Kiểm tra kết nối database
if (!$conn) {
    $response["message"] = "Lỗi kết nối database.";
    echo json_encode($response);
    exit();
}

// Kiểm tra nếu form được gửi qua AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Kiểm tra rỗng
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $response["message"] = "Vui lòng nhập đầy đủ thông tin.";
    } 
    // Kiểm tra định dạng email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = "Email không hợp lệ!";
    } 
    // Kiểm tra độ dài mật khẩu
    elseif (strlen($password) < 6) {
        $response["message"] = "Mật khẩu phải có ít nhất 6 ký tự!";
    } 
    // Kiểm tra xác nhận mật khẩu
    elseif ($password !== $confirm_password) {
        $response["message"] = "Mật khẩu xác nhận không khớp.";
    } 
    else {
        // Kiểm tra email đã tồn tại
        $stmt_check = $conn->prepare("SELECT account_id FROM account WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $response["message"] = "Email đã được sử dụng.";
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Chèn dữ liệu vào database
            $stmt_insert = $conn->prepare("INSERT INTO account (account_name, email, password_hash, status_id, role_id, created_at, updated_at) VALUES (?, ?, ?, 1, 2, NOW(), NOW())");
            $stmt_insert->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                $_SESSION['dangky'] = $name;
                $response["status"] = "success";
                $response["message"] = "Đăng ký thành công!";
            } else {
                $response["message"] = "Có lỗi xảy ra. Vui lòng thử lại.";
            }

            $stmt_insert->close();
        }

        $stmt_check->close();
    }
}

// Trả về kết quả dưới dạng JSON
echo json_encode($response);
exit();
?>
