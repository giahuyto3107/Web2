<?php
include ('../../../BackEnd/Config/config.php');
if (isset($_GET['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
    $query = "SELECT orders.order_id, orders.order_date, orders.status_id
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
    $order_status = $order['status_id'];
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=
"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity=
"sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" 
          referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href=
"https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" 
          integrity=
"sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" 
          crossorigin="anonymous">
</head>
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

        h3{
            font-size: 1.2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
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
        .mr-n2, .mx-n2 {
            margin-right: 0rem !important;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Chi Tiết Đơn Hàng #<?= htmlspecialchars($order['order_id']) ?></h2>
    <div class="header">
        <h3>Danh sách sản phẩm</h3>
        <h3>Ngày mua: <?= htmlspecialchars($order['order_date']) ?></h2>
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
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
        <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="container-fluid p-2 align-items-center">
                <div class="d-flex justify-content-around">
                    <?php
                    // Danh sách trạng thái
                    $status_map = [
                        3 => 'Pending',
                        4 => 'Order Confirmed',
                        6 => 'Out for Delivery', 
                        5 => 'Delivered'
                    ];


                    $status_colors = [
                        3 => 'bg-success',
                        4 => 'bg-success',
                        6 => 'bg-success',
                        5 => 'bg-success'
                    ];

                    $icons = [
                        3 => "fa-spinner",
                        4 => "fa-clipboard-check",
                        6 => "fa-truck-arrow-right",
                        5 => "fa-house-chimney"
                    ];

                    // Lặp qua từng trạng thái
                    foreach ($status_map as $key => $value) {
                        $is_completed = $key <= $order_status;
                        $btn_color = $is_completed ? $status_colors[$key] : 'bg-secondary';
                        $icon = $icons[$key];
                    ?>
                        <!-- Nút trạng thái -->
                        <button class="btn <?= $btn_color ?> text-white rounded-circle"
                                data-bs-toggle="tooltip"
                                title="<?= $value ?>">
                            <i class="fa-solid <?= $icon ?>"></i>
                        </button>

                        <!-- Thanh nối, chỉ hiển thị nếu không phải là trạng thái cuối cùng -->
                        <?php if ($key != array_key_last($status_map)): ?>
                            <span class="<?= $is_completed ? $btn_color : 'bg-secondary' ?> w-50 p-1 mx-n1 rounded mt-auto mb-auto"></span>
                        <?php endif; ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

            <div class="row d-flex 
                        justify-content-between 
                        mx-n2">
                <div class="row d-inline-flex 
                            align-items-center">
                    <i class="text-primary fa-solid 
                              fa-spinner 
                              fa-2xl mx-4 mb-3">
                      </i>
                    <p class="text-dark font-weight-bolder 
                              py-1 px-1 mx-n2">
                      Pending
                      </p>
                </div>
                <div class="row d-inline-flex
                            align-items-center">
                    <i class="text-warning fa-solid
                               fa-clipboard-check
                              fa-2xl mx-4 mb-3">
                      </i>
                    <p class="text-dark  
                              font-weight-bolder
                              py-1 px-1 mx-n2">
                      Order
                      <br>
                      Confirmed
                      </p>
                </div>
                <div class="row d-inline-flex 
                            align-items-center">
                    <i class="text-info fa-solid 
                              fa-truck-arrow-right
                              fa-2xl mx-4 mb-3">
                      </i>
                    <p class="text-dark 
                              font-weight-bolder
                              py-1 px-1 mx-n2">
                      Out for
                      <br>
                      Delivery
                      </p>
                </div>
                <div class="row d-inline-flex 
                            align-items-center">
                    <i class="text-success fa-solid
                              fa-house-chimney 
                              fa-2xl mx-4 mb-3">
                      </i>
                    <p class="text-dark font-weight-bolder
                              py-1 px-1 mx-n2">
                        Delivered
                      </p>
                </div>
            </div>
    <div class="total">
        Tổng tiền: <?= number_format($total_price, 0, ',', '.') ?> đ
    </div>
    <a href="http://localhost/Web2/FrontEnd/PublicUI/Lichsumuahang/listmuahang.php" class="back-btn">Quay lại</a>
</div>
</body>
</html>