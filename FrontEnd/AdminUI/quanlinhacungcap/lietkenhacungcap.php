<?php
    $sql_supplier = "SELECT * FROM supplier ";
   $query_supplier = mysqli_query($conn, $sql_supplier);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Đơn Hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <div class="container">

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên nhà cung cấp</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Trạng thái</th>
                    <th>Quản lý</th>

                </tr>
            </thead>
            <tbody id="order-list">
        <?php
        $i = 0;
        while ($row = mysqli_fetch_array($query_supplier)) {
            $i++;
        ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                    <td><?= htmlspecialchars($row['contact_phone']) ?></td>  
                    <td><?= htmlspecialchars($row['address']) ?></td>              
                    <td>
                        <?php 
                            if ($row['status_id'] == 1) {
                                echo '<i class="fas fa-truck"></i> Hoạt động';
                            } elseif ($row['status_id'] == 2) {
                                echo '<i class="fas fa-credit-card"></i> Không hoạt động';
                            }
                        ?>
                    </td>
                    <td>
                        <a class="sua" href="?action=quanlinhacungcap&query=sua&idncc=<?= $row['supplier_id'] ?>">Sửa</a>
                        <?php if ($row['status_id'] == 2) {
                        echo '<a class="vohieuhoa" href="../../BackEnd/Model/quanlinhacungcap/xulinhacungcap.php?idncc=' . $row['supplier_id'] . '&status_id=1">Vô hiệu hóa</a>';
                        } else {
                        echo '<a class="khoiphuc" style="background-color:green" href="../../BackEnd/Model/quanlinhacungcap/xulinhacungcap.php?idncc=' . $row['supplier_id'] . '&status_id=2">Khôi phục</a>';
                        } ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
        <div id="pagination" style="text-align: center; margin-top: 20px;">
            
        </div>


    </div>
</body>


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

        .status-refused {
            color: red;
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

        #pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .page-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f0f0f0;
            color: #333;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            user-select: none;
        }

        .page-btn:hover {
            background: #007bff;
            color: white;
        }

        .page-btn.active {
            background: #007bff;
            color: white;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.6);
        }

        .filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .filter input, .filter select, .filter button {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .filter input {
            flex: 1;
            min-width: 200px;
        }

        .filter button {
            background: #007bff;
            color: white;
            cursor: pointer;
            border: none;
            transition: 0.3s;
        }

        .filter button:hover {
            background: #0056b3;
        }

    </style>

</html>