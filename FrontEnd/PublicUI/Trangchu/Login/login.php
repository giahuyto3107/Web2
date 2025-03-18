<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <main class="flex flex-grow items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold text-center mb-4">Đăng Nhập</h2>
            <form id="loginForm" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Email</label>
                    <input id="email" type="email" name="email" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
                    <input id="password" type="password" name="password" class="w-full p-2 border rounded" required>
                </div>
                <button id="submit-login" type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                    Đăng nhập
                </button>
                <p id="login-error" class="text-red-500 text-center mt-2 hidden"></p>
            </form>
            <p class="text-center mt-4">
                Chưa có tài khoản? <a href="index.php?page=signup" class="text-blue-500">Đăng ký ngay</a>
            </p>
        </div>
    </main>
    <script>
        document.getElementById("submit-login").addEventListener("click", function (event) {
        event.preventDefault(); // Ngăn form submit mặc định
        
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        fetch("Login/process-login.php", { // Cập nhật đường dẫn
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect; // Điều hướng đến home.php
            } else {
                document.getElementById("login-error").textContent = data.message;
                document.getElementById("login-error").classList.remove("hidden");
            }
        })
        .catch(error => console.error("Lỗi:", error));
    });

    </script>

</body>
</html>
