<?php

$conn = mysqli_connect("localhost", "root", "", "web2_sql");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id']; 
    if (isset($_POST['approve'])) {
        $query = "UPDATE orders SET status_id = 4 WHERE order_id = '$order_id'";
    } elseif (isset($_POST['cancel'])) {
        $query = "UPDATE orders SET status_id = 2 WHERE order_id = '$order_id'";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Cập nhật trạng thái thành công!'); window.location.href = 'danhsachdonhang.php';</script>";
    } else {
        echo "Lỗi cập nhật: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
