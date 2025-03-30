<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col min-h-screen">
    <?php include 'header.php'; ?>

    <main class="flex-grow">
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
        $allowed_pages = ['home', 'login', 'signup', 'about', 'contact', 'logout']; 
        $page_file = __DIR__ . "/Pages/$page.php";

        if (in_array($page, $allowed_pages) && file_exists($page_file)) {
            include $page_file;
        } else {
            echo "<p class='text-center text-red-500'>Trang không tồn tại</p>";
        }
        ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
