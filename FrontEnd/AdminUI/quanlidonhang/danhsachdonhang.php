<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Đơn Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 4px solid #007bff;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background: #007bff;
            color: white;
            font-weight: bold;
        }

        thead tr {
            background: #007bff !important;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        tbody tr:hover {
            background: #f1f1f1;
            transition: 0.2s;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        .approve-btn {
            background: #28a745;
            color: white;
        }

        .approve-btn:hover {
            background: #218838;
        }

        .cancel-btn {
            background: #dc3545;
            color: white;
        }

        .cancel-btn:hover {
            background: #c82333;
        }

        .detail-btn {
            background:rgb(158, 204, 253);
            color: white;
            
        }

        .detail-btn:hover {
            background: #0056b3;
        }


        .status-approved {
            color: green;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                width: 100%;
                padding: 15px;
            }

            table, th, td {
                font-size: 14px;
            }

            button, a {
                font-size: 14px;
                padding: 6px 8px;
            }
        }
    </style>

</head>
<body>
    <div class="container">
        <h1>Danh Sách Đơn Hàng</h1>
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên Khách Hàng</th>
                    <!-- <th>Địa chỉ</th> -->
                    <th>Ngày Đặt</th>
                    <th>Thanh Toán</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
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
                    <td><?= htmlspecialchars($row['full_name']) ?></td>                
                    <td><?= htmlspecialchars($row['order_date']) ?></td>
                    <td>
                        <?php 
                            if ($row['payment_method'] == 'Cash') {
                                echo '<i class="fas fa-truck"></i> COD';
                            } elseif ($row['payment_method'] == 'Credit Card') {
                                echo '<i class="fas fa-credit-card"></i> Online';
                            }
                        ?>
                    </td>
                    <td>
                        <?php 
                            if ($row['id'] == '4') {
                                echo '<span class="status-approved">Đã duyệt</span>';
                            } else {
                                echo '
                                    <form method="POST" action="xulidonhang.php" style="display:inline;">
                                        <input type="hidden" name="order_id" value="'.$row['order_id'].'">
                                        <button type="submit" name="approve" class="btn approve-btn">Duyệt</button>
                                    </form>
                                    <form method="POST" action="xulidonhang.php" style="display:inline;">
                                        <input type="hidden" name="order_id" value="'.$row['order_id'].'">
                                        <button type="submit" name="cancel" class="btn cancel-btn">Hủy</button>
                                    </form>
                                ';
                            }
                        ?>
                    </td>
                    <td>
                        <a href="chitietdonhang.php?order_id=<?= $row['order_id'] ?>" class="btn detail-btn"><b></b>Chi tiết</a>
                    </td>
                </tr>
                <?php
                    }
                    mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>