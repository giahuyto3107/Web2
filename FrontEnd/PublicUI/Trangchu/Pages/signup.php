<?php
include ('../../../BackEnd/Config/config.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
       
        /* Định dạng nền */
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
        }
        .flex-grow{
            background-color: #f7e6c4;
        }
        /* Định dạng chính */
        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 90px 0;
            
        }

        /* Form đăng ký */
        .register-container {
            background: linear-gradient(135deg,#fef3c7 , #ffffff); /* Trắng sang vàng nhạt */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            border: 2px solid #000;
        }

        /* Tiêu đề */
        .register-container h2 {
            font-size: 26px;
            color: #000;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Ô nhập liệu */
        .register-container input {
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
        .register-container input:focus {
            border-color: #facc15; /* Vàng */
            background: #fff9db;
        }

        /* Nút đăng ký */
        .register-container button {
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
        .register-container button:hover {
            background-color: #000;
            color: #facc15;
        }

        /* Link chuyển sang đăng nhập */
        .register-container p a {
            color: #facc15;
            text-decoration: none;
            font-weight: bold;
        }

        .register-container p a:hover {
            text-decoration: underline;
        }


    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
<div class="main-container">
    <div class="register-container bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold mb-4 text-center">Đăng ký</h2>

        <!-- Thông báo -->
        <div id="message" class="hidden p-2 rounded text-center mb-2"></div>

        <form id="signup-form">
            <input type="text" id="name" name="name" placeholder="Tên tài khoản" required class="w-full p-2 border rounded mb-2">
            <input type="email" id="email" name="email" placeholder="Email" required class="w-full p-2 border rounded mb-2">
            <input type="password" id="password" name="password" placeholder="Mật khẩu" required class="w-full p-2 border rounded mb-2">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required class="w-full p-2 border rounded mb-2">

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Đăng ký</button>
        </form>

        <p class="text-sm mt-4 text-center">Đã có tài khoản? 
            <a href="index.php?page=login" class="text-blue-500">Đăng nhập</a>
        </p>
    </div>
</div>
    <script>
        document.getElementById("signup-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn chặn load lại trang
            
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let messageBox = document.getElementById("message");

            if (password !== confirmPassword) {
                messageBox.textContent = "Mật khẩu xác nhận không khớp!";
                messageBox.classList.remove("hidden");
                messageBox.classList.add("text-red-500", "bg-red-100");
                return;
            }

            let formData = new FormData(this);

            fetch("Pages/signup_process.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageBox.textContent = data.message;
                messageBox.classList.remove("hidden");

                if (data.status === "success") {
                    messageBox.classList.remove("text-red-500", "bg-red-100"); // Xóa màu lỗi
                    messageBox.classList.add("text-green-500", "bg-green-100"); // Thêm màu thành công

                    setTimeout(function() {
                        console.log("Chuyển hướng..."); // Kiểm tra xem có log ra không
                        window.location.href = "index.php";
                    }, 2000);
                } else {
                    messageBox.classList.remove("text-green-500", "bg-green-100");
                    messageBox.classList.add("text-red-500", "bg-red-100");
                }
            })
            .catch(error => console.error("Lỗi:", error));
        });

    </script>
</body>
</html>


