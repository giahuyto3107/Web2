<?php
// pdo.php

// Thông tin kết nối cơ sở dữ liệu
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Thay bằng password của bạn
define('DB_NAME', 'web2_sql');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

try {
    // Tạo DSN (Data Source Name) cho PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;

    // Tạo kết nối
    $pdo = new PDO($dsn, DB_USER, DB_PASS);

    // Thiết lập chế độ báo lỗi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Thiết lập chế độ trả về dữ liệu dưới dạng mảng kết hợp
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Thiết lập charset
    $pdo->exec("SET NAMES '" . DB_CHARSET . "'");
} catch (PDOException $e) {
    // Ghi log lỗi (trong môi trường production)
    error_log("Kết nối thất bại: " . $e->getMessage());
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
}
?>