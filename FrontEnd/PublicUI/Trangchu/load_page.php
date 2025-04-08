<!-- D:\xampp\htdocs\Web2\FrontEnd\PublicUI\Trangchu\load_page.php -->
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'login', 'signup', 'about', 'contact', 'profile', 'orders', 'cart', 'product', 'product_details', 'order_details'];

$custom_paths = [
    'cart' => '../../PublicUI/Giohang/giohang.php',
    'profile' => '../../PublicUI/User/user.php',
    'orders' => '../../PublicUI/Lichsumuahang/listmuahang.php',
    'order_details' => '../../PublicUI/Lichsumuahang/chitietdonhang.php',
    'product' => '../../PublicUI/SanPham/danhsachSP.php',
    'product_details' => '../../PublicUI/SanPham/product_detail.php',
    
];

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    die('Truy cập không hợp lệ');
}

ob_start();

// Tách page và các tham số
$page = str_replace('?', '&', $page); 
$page_parts = explode('&', $page);
$page_key = $page_parts[0];
parse_str(implode('&', array_slice($page_parts, 1)), $params);

if ($page_key === 'order_details' && isset($params['order_id'])) {
    $file = $custom_paths['order_details'];
    if (file_exists($file)) {
        $_GET['order_id'] = $params['order_id'];
        include $file;
    } else {
        echo "<section><h2>Lỗi: File chi tiết đơn hàng không tồn tại - $file</h2></section>";
    }
} elseif ($page_key === 'product_details' && isset($params['id'])) {
    $file = $custom_paths['product_details'];
    if (file_exists($file)) {
        $_GET['id'] = $params['id'];
        include $file;
    } else {
        echo "<section><h2>Lỗi: File chi tiết sản phẩm không tồn tại - $file</h2></section>";
    }
} elseif (array_key_exists($page_key, $custom_paths)) {
    $file = $custom_paths[$page_key];
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<section><h2>Lỗi: File " . htmlspecialchars($file) . " không tồn tại</h2></section>";
    }
} elseif (in_array($page_key, $allowed_pages)) {
    $page_file = "D:/xampp/htdocs/Web2/FrontEnd/PublicUI/Trangchu/Pages/$page_key.php";
    if (file_exists($page_file)) {
        include $page_file;
    } else {
        echo "<section><h2>Lỗi: File " . htmlspecialchars($page_file) . " không tồn tại</h2></section>";
    }
} else {
    echo "<section><h2>Trang không tồn tại: " . htmlspecialchars($page_key) . "</h2></section>";
}
echo ob_get_clean();
?>