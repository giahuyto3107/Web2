<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Thay bằng username của bạn
$password = "1234"; // Thay bằng password của bạn
$dbname = "web2_sql";
$port = "3305"; // Thay bằng port của bạn

// Tạo kết nối
$conn = new mysqli($servername, $username,$password , $dbname, $port);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>




