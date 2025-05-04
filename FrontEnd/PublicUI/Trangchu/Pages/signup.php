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
        /* Global Styles */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #ffffff; 
            color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }


        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0; 
            width: 100%;
        }

      
        .register-container {
            background: #ffffff; 
            padding: 40px;
            border: 1px solid #e0e0e0; 
            width: 400px;
            text-align: center;
            margin-top:50px
        }

        .register-container h2 {
            font-size: 1.8rem;
            font-weight: 400;
            color: #1a1a1a;
            letter-spacing: 2px;
            margin-bottom: 30px;
            text-transform: uppercase;
            position: relative;
        }

        .register-container h2::after {
            content: '';
            width: 40px;
            height: 1px;
            background: #d4af37; 
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .register-container input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            font-size: 0.9rem;
            font-weight: 300;
            color: #1a1a1a;
            background: #fff;
            outline: none;
            transition: border-color 0.3s ease; 
        }

        .register-container input:focus {
            border-color: #d4af37; 
        }

        .register-container button {
            width: 100%;
            background: #1a1a1a;
            color: #fff;
            padding: 12px;
            border: 1px solid #1a1a1a;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .register-container button:hover {
            background: #d4af37; 
            color: #1a1a1a;
        }

        .register-container p a {
            color: #d4af37; 
            text-decoration: none;
            font-weight: 400;
            transition: color 0.3s ease; 
        }

        .register-container p a:hover {
            color: #8b0000; 
        }

        #message {
            font-size: 0.85rem;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        #message.hidden {
            display: none;
        }

        #message.text-red-500 {
            color: #8b0000; 
            background: #ffe6e6;
        }

        #message.text-green-500 {
            color: #1a1a1a; 
            background: #e6f0e6;
        }
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            top: 36%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.5rem; /* Tăng kích thước */
            color: #777;
            user-select: none;
            line-height: 1;
        }
        .password-toggle:hover {
            color: #d4af37;
        }

    </style>
</head>
<body>
    <div class="main-container">
        <div class="register-container">
            <h2>Đăng ký</h2>

            <!-- Thông báo -->
            <div id="message" class="hidden p-2 rounded text-center mb-2"></div>

            <form id="signup-form">
                <input type="text" id="name" name="name" placeholder="Tên tài khoản" required>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="text" id="address" name="address" placeholder="Địa chỉ" required>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
                    <span class="password-toggle" data-target="password">👁️</span>
                </div>

                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                    <span class="password-toggle" data-target="confirm_password">👁️</span>
                </div>
                <button type="submit">Đăng ký</button>
            </form>

            <p class="text-sm mt-4 text-center">Đã có tài khoản? 
                <a href="?page=login" data-page="login">Đăng nhập</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById("signup-form").addEventListener("submit", function(event) {
            event.preventDefault();
            
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
                    messageBox.classList.remove("text-red-500", "bg-red-100");
                    messageBox.classList.add("text-green-500", "bg-green-100");

                    setTimeout(function() {
                        console.log("Chuyển hướng...");
                        window.location.href = "?page=login";
                    }, 2000);
                } else {
                    messageBox.classList.remove("text-green-500", "bg-green-100");
                    messageBox.classList.add("text-red-500", "bg-red-100");
                }
            })
            .catch(error => console.error("Lỗi:", error));
        });

        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input.type === "password") {
                    input.type = "text";
                    this.textContent = "🙈";
                } else {
                    input.type = "password";
                    this.textContent = "👁️";
                }
            });
        });

    </script>
</body>
</html>