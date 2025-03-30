<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Căn giữa form */
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 100px);
            background: #f9f9f9;
        }

        /* Khung đăng nhập */
        .login-container {
            background: linear-gradient(135deg,#fef3c7 , #ffffff);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            border: 2px solid #000;
        }

        /* Tiêu đề */
        .login-container h2 {
            font-size: 26px;
            color: #000;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Input */
        .login-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #000;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s, background 0.3s;
        }

        /* Hiệu ứng khi focus vào ô nhập */
        .login-container input:focus {
            border-color: #facc15; /* Vàng */
            background: #fff9db;
        }

        /* Nút đăng nhập */
        .login-container button {
            width: 100%;
            background-color: #facc15; /* Vàng */
            color: #000;
            padding: 12px;
            border: 2px solid #000;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s, color 0.3s;
        }

        /* Hiệu ứng hover */
        .login-container button:hover {
            background-color: #000;
            color: #facc15;
        }

        /* Link chuyển sang đăng ký */
        .login-container p a {
            color: #facc15;
            text-decoration: none;
            font-weight: bold;
        }

        .login-container p a:hover {
            text-decoration: underline;
        }

        /* Hiển thị lỗi khi đăng nhập */
        #login-error {
            font-size: 14px;
            color: red;
            margin-top: 10px;
        }

        /* Thông báo thành công (màu xanh) */
        #login-success {
            font-size: 14px;
            color: green;
            margin-top: 10px;
            font-weight: bold;
        }
        #login-message {
            font-size: 14px;
            margin-top: 10px;
            font-weight: bold;
            display: none;
        }

        /* Màu đỏ khi có lỗi */
        #login-message.error {
            color: red;
        }

        /* Màu xanh khi đăng nhập thành công */
        #login-message.success {
            color: green;
        }
        .flex-grow{
            background-color: #f7e6c4;
        }
    </style>
</head>
<body class="flex flex-col justify-center items-center min-h-screen">

    <div class="login-container">
        <h2>Đăng Nhập</h2>

        <form id="loginForm">
            <input id="email" type="email" name="email" placeholder="Email" required>
            <input id="password" type="password" name="password" placeholder="Mật khẩu" required>
            <button id="submit-login" type="submit">Đăng nhập</button>
            <p id="login-message"></p> <!-- Thông báo lỗi hoặc thành công -->
        </form>

        <p class="text-sm mt-4">Chưa có tài khoản? <a href="index.php?page=signup">Đăng ký ngay</a></p>
    </div>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Ngăn chặn form submit mặc định

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const messageBox = document.getElementById("login-message");
            const submitButton = document.getElementById("submit-login");

            // Xóa thông báo cũ
            messageBox.style.display = "none";
            messageBox.textContent = "";
            messageBox.classList.remove("success", "error");

            // Kiểm tra nếu bỏ trống
            if (!email || !password) {
                messageBox.textContent = "Vui lòng nhập đầy đủ email và mật khẩu!";
                messageBox.classList.add("error");
                messageBox.style.display = "block";
                return;
            }

            // Vô hiệu hóa nút để tránh spam request
            submitButton.disabled = true;
            submitButton.textContent = "Đang đăng nhập...";

            fetch("Pages/process-login.php", { // Đường dẫn xử lý đăng nhập
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageBox.textContent = "Đăng nhập thành công! Đang chuyển hướng...";
                    messageBox.classList.remove("error"); // Xóa class error nếu có
                    messageBox.classList.add("success"); // Thêm class success (màu xanh)
                    messageBox.style.display = "block";
                    // Chờ 2 giây trước khi chuyển hướng
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    messageBox.textContent = data.message;
                    messageBox.classList.remove("success"); // Xóa class success nếu có
                    messageBox.classList.add("error"); // Thêm class error (màu đỏ)
                    messageBox.style.display = "block";
                }
            })
            .catch(error => {
                messageBox.textContent = "Lỗi hệ thống, vui lòng thử lại!";
                messageBox.classList.add("error");
                messageBox.style.display = "block";
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = "Đăng nhập";
            });
        });
    </script>

</body>
</html>
