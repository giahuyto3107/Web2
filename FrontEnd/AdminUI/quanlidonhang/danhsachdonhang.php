<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Đơn Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên Khách Hàng</th>
                <!-- <th>Địa Chỉ Giao Hàng</th> -->
                <!-- <th>Số Điện Thoại</th> -->
                <th>Ngày Đặt</th>
                <th>Phương Thức Thanh Toán</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $conn = mysqli_connect("localhost", "root", "", "web2_sql");
                if (!$conn) {
                    die("Kết nối database thất bại: " . mysqli_connect_error());
                }
                $query_lietke_dh = "SELECT orders.order_id, user.full_name, orders.order_date, orders.payment_method, status.id 
                                    FROM orders 
                                    JOIN user ON orders.user_id = user.user_id 
                                    JOIN status ON status.id = orders.status_id;";
                $result = mysqli_query($conn, $query_lietke_dh);

                $i = 0;
                while ($row = mysqli_fetch_array($result)) {
                    $i++;
            ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>                
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td>
                            <?php 
                                if ($row['payment_method'] == 'Cash') {
                                    echo '<i class="fas fa-truck"></i> Thanh toán khi nhận hàng';
                                } elseif ($row['payment_method'] == 'Credit Card') {
                                    echo '<i class="fas fa-credit-card"></i> Đã thanh toán online';
                                }
                            ?>
                        </td>
                        <td>
                            <?php 
                                if ($row['id'] == '4') {
                                    echo '<span style="color: green; font-weight: bold;">Đã duyệt</span>';
                                } else {
                                    echo '
                                        <form method="POST" action="xulidonhang.php" style="display:inline;">
                                            <input type="hidden" name="order_id" value="'.$row['order_id'].'">
                                            <button type="submit" name="approve" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer;">Duyệt đơn</button>
                                        </form>
                                        <form method="POST" action="xulidonhang.php" style="display:inline;">
                                            <input type="hidden" name="order_id" value="'.$row['order_id'].'">
                                            <button type="submit" name="cancel" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer;">Hủy đơn</button>
                                        </form>
                                    ';
                                }
                            ?>
                        </td>
                    </tr>
            <?php
                }
                mysqli_close($conn);
            ?>

        </tbody>
    </table>
</body>
</html>