<?php
session_start();
include ('../../../../BackEnd/Config/config.php');

header('Content-Type: application/json'); // Đảm bảo trả về JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT account_id, account_name, email, password_hash FROM account WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Lỗi truy vấn cơ sở dữ liệu!"]);
            exit();
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user'] = [
                    "account_id" => $user['account_id'],
                    "account_name" => $user['account_name'],
                    "email" => $user['email']
                ];
                echo json_encode(["success" => true, "redirect" => "index.php"]);
            } else {
                echo json_encode(["success" => false, "message" => "Sai mật khẩu!"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Tài khoản không tồn tại!"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Vui lòng nhập đầy đủ thông tin!"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Yêu cầu không hợp lệ!"]);
}
?>
