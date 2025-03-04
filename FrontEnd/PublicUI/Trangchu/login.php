<?php
session_start();
include ('../../../BackEnd/Config/config.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM account WHERE email = ? AND status_id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Sử dụng password_verify để kiểm tra mật khẩu
            if (password_verify($password, $row['password_hash'])) { 
                $_SESSION['user_id'] = $row['account_id'];
                $_SESSION['user_name'] = $row['account_name'];

                // Cập nhật last_login
                $update_sql = "UPDATE account SET last_login = NOW() WHERE account_id = ?";
                $stmt_update = $conn->prepare($update_sql);
                $stmt_update->bind_param("i", $row['account_id']);
                $stmt_update->execute();

                header("Location: home.php");
                exit();
            } else {
                $_SESSION['error'] = "Sai mật khẩu.";
            }
        } else {
            $_SESSION['error'] = "Email không tồn tại hoặc tài khoản bị khóa.";
        }
    } else {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin.";
    }
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-xl font-bold mb-4">Đăng nhập</h2>
        <?php if (!empty($error)): ?>
            <p id="error-message" class="text-red-500 text-sm mb-2"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label class="block mb-2">Email:</label>
            <input type="email" name="email" class="w-full p-2 border rounded mb-2" required onclick="clearError()">
            <label class="block mb-2">Mật khẩu:</label>
            <input type="password" name="password" class="w-full p-2 border rounded mb-2" required onclick="clearError()">
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Đăng nhập</button>
        </form>
        <p class="text-sm mt-4">Chưa có tài khoản? <a href="signup.php" class="text-blue-500">Đăng ký</a></p>
    </div>
    <script>
        function clearError() {
            document.getElementById('error-message').innerText = '';
        }
    </script>
</body>
</html>
