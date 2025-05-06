<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
            padding: 60px 0;
            width: 100%;
            margin-top:50px;
        }

        .login-container {
            background: #ffffff;
            padding: 40px;
            border: 1px solid #e0e0e0;
            width: 400px;
            text-align: center;
        }

        .login-container h2 {
            font-size: 1.8rem;
            font-weight: 400;
            color: #1a1a1a;
            letter-spacing: 2px;
            margin-bottom: 30px;
            text-transform: uppercase;
            position: relative;
        }

        .login-container h2::after {
            content: '';
            width: 40px;
            height: 1px;
            background: #d4af37;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .login-container input {
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

        .login-container input:focus {
            border-color: #d4af37;
        }

        .login-container button {
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

        .login-container button:hover {
            background: #d4af37;
            color: #1a1a1a;
        }

        .login-container button:disabled {
            background: #666;
            border-color: #666;
            cursor: not-allowed;
        }

        .login-container p a {
            color: #d4af37;
            text-decoration: none;
            font-weight: 400;
            transition: color 0.3s ease;
        }

        .login-container p a:hover {
            color: #8b0000;
        }

        #login-message {
            font-size: 0.85rem;
            margin-top: 15px;
            font-weight: 300;
            display: none;
            padding: 8px;
            border-radius: 4px;
        }

        #login-message.error {
            color: #8b0000;
            background: #ffe6e6;
        }

        #login-message.success {
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
        <div class="login-container">
            <h2>Đăng Nhập</h2>
            <form id="loginForm">
                <input id="email" type="email" name="email" placeholder="Email" required>
                <div class="password-wrapper">
                    <input id="password" type="password" name="password" placeholder="Mật khẩu" required>
                    <span class="password-toggle" data-target="password">👁️</span>
                </div>
                <button id="submit-login" type="submit">Đăng nhập</button>
                <p id="login-message"></p>
            </form>
            <p class="text-sm mt-4">Chưa có tài khoản? <a href="?page=signup" data-page="signup">Đăng ký ngay</a></p>
        </div>
    </div>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const messageBox = document.getElementById("login-message");
            const submitButton = document.getElementById("submit-login");

            messageBox.style.display = "none";
            messageBox.textContent = "";
            messageBox.classList.remove("success", "error");

            if (!email || !password) {
                messageBox.textContent = "Vui lòng nhập đầy đủ email và mật khẩu!";
                messageBox.classList.add("error");
                messageBox.style.display = "block";
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = "Đang đăng nhập...";

            fetch("Pages/process-login.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageBox.textContent = "Đăng nhập thành công! Đang chuyển hướng...";
                    messageBox.classList.remove("error");
                    messageBox.classList.add("success");
                    messageBox.style.display = "block";
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    messageBox.textContent = data.message;
                    messageBox.classList.remove("success");
                    messageBox.classList.add("error");
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