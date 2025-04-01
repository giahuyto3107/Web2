<?php
include 'header.php';
?>
<div class="content-wrapper">
<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'login', 'signup', 'about', 'contact', 'profile', 'orders', 'cart'];
$page_file = __DIR__ . "/Pages/$page.php";

if ($page === 'home' || !in_array($page, $allowed_pages) || !file_exists($page_file)) {
    include __DIR__ . '/Pages/home.php';
} else if ($page === 'profile') {
    include 'D:/xampp/htdocs/Web2/FrontEnd/PublicUI/User/user.php';
} else if ($page === 'cart') {
    include 'D:/xampp/htdocs/Web2/FrontEnd/PublicUI/Giohang/giohang.php';
} else if ($page === 'orders') {
    include 'D:/xampp/htdocs/Web2/FrontEnd/PublicUI/Lichsumuahang/listmuahang.php';
} else if (in_array($page, $allowed_pages) && file_exists($page_file)) {
    include $page_file;
} else if ($page !== 'logout') {
    echo "<section><h2>Trang không tồn tại</h2></section>";
}
?>
</div>
<?php
include 'footer.php';
?>