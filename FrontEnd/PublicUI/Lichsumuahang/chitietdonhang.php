<?php
include ('../../../BackEnd/Config/config.php');
if (isset($_GET['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
    $query = "SELECT orders.order_id, orders.order_date
              FROM order_items 
              JOIN orders ON order_items.order_id = orders.order_id 
              WHERE orders.order_id = '$order_id'";

    $result = mysqli_query($conn, $query);
    $order = mysqli_fetch_assoc($result);

    if (!$order) {
        echo "Không tìm thấy đơn hàng!";
        exit;
    }
    $query_items = "SELECT product.product_name, order_items.quantity, product.price, product.image_url
                    FROM order_items 
                    JOIN product ON order_items.product_id = product.product_id 
                    WHERE order_items.order_id = '$order_id'";

    $result_items = mysqli_query($conn, $query_items);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #f4f7f6;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 900px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .total {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            color: #d9534f;
            padding: 15px;
            background: #fff3f3;
            border-radius: 8px;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
            text-align: center;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        .header{
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Chi Tiết Đơn Hàng #<?= htmlspecialchars($order['order_id']) ?></h2>
    <div class="header">
        <h3>Danh sách sản phẩm</h3>
        <h4>Ngày mua: <?= htmlspecialchars($order['order_date']) ?></h2>
    </div>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Tên sản phẩm</th>

                <th>Số lượng</th>
                <th>Giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $total_price = 0;
                while ($item = mysqli_fetch_assoc($result_items)) { 
                    $subtotal = $item['quantity'] * $item['price'];
                    $total_price += $subtotal;
            ?>
                <tr>
                    <td><img class="rounded" src="../../../BackEnd/Uploads/Product Picture/<?= htmlspecialchars($item['image_url']) ?>" width="50"></td>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                    <td><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="tracking">
        
    </div>
    <div class="total">
        Tổng tiền: <?= number_format($total_price, 0, ',', '.') ?> đ
    </div>
    <a href="http://localhost/Web2/FrontEnd/PublicUI/Lichsumuahang/listmuahang.php" class="back-btn">Quay lại</a>
</div>
</body>
</html>