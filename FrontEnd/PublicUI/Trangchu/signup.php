<?php
session_start();
include ('../../../BackEnd/Config/config.php');

$error = "";
$success = "";

// Kiểm tra nếu có thông báo thành công từ URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Đăng ký thành công! Bạn có thể <a href='login.php' class='text-blue-500'>đăng nhập</a> ngay bây giờ.";
}

// Xử lý khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Kiểm tra dữ liệu nhập
    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = "Mật khẩu xác nhận không khớp.";
        } else {
            // Kiểm tra email đã tồn tại chưa
            $sql_check = "SELECT * FROM account WHERE email = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $error = "Email đã được sử dụng.";
            } else {
                // Băm mật khẩu trước khi lưu vào DB
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO account (account_name, email, password_hash, status_id, role_id, created_at, updated_at) 
                               VALUES (?, ?, ?, 1, 2, NOW(), NOW())";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("sss", $name, $email, $hashed_password);
                
                if ($stmt_insert->execute()) {
                    // Chuyển hướng để tránh resubmission
                    header("Location: signup.php?success=1");
                    exit();
                } else {
                    $error = "Có lỗi xảy ra. Vui lòng thử lại.";
                }
            }
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-xl font-bold mb-4">Đăng ký</h2>

        <?php if (!empty($error)): ?>
            <p id="error-message" class="text-red-500 text-sm mb-2"><?php echo $error; ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="text-green-500 text-sm mb-2"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST" action="signup.php">
            <label class="block mb-2">Tên tài khoản:</label>
            <input type="text" name="name" class="w-full p-2 border rounded mb-2" required onclick="clearError()">

            <label class="block mb-2">Email:</label>
            <input type="email" name="email" class="w-full p-2 border rounded mb-2" required onclick="clearError()">

            <label class="block mb-2">Mật khẩu:</label>
            <input type="password" name="password" class="w-full p-2 border rounded mb-2" required onclick="clearError()">

            <label class="block mb-2">Xác nhận mật khẩu:</label>
            <input type="password" name="confirm_password" class="w-full p-2 border rounded mb-2" required onclick="clearError()">

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Đăng ký</button>
        </form>

        <p class="text-sm mt-4">Đã có tài khoản? <a href="login.php" class="text-blue-500">Đăng nhập</a></p>
    </div>

    <script>
        function clearError() {
            document.getElementById('error-message').innerText = '';
        }
    </script>
</body>
</html>
