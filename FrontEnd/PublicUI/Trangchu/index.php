<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <?php 
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
        $allowed_pages = ['home', 'login', 'signup'];

        if (in_array($page, $allowed_pages)) {
            switch ($page) {
                case 'login':
                    $file = __DIR__ . '/Login/login.php';
                    if (!file_exists($file)) {
                        die("<p class='text-center text-red-500'>File không tồn tại: Login/login.php</p>");
                    }
                    include $file;
                    break;
                default:
                    include "$page.php";
                    break;
            }
        } else {
            echo "<p class='text-center text-red-500'>Trang không tồn tại</p>";
        }
        ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
