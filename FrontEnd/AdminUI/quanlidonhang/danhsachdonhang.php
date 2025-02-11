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

</head>
<body>
    <div class="container">
        <h1>Danh Sách Đơn Hàng</h1>
        <div class="filter">
            <input class="input1" type="text" id="search-input" placeholder="Tìm kiếm theo tên hoặc địa chỉ...">
            
            <select id="status-filter">
                <option value="">Tất cả trạng thái</option>
                <option value="4">Đã duyệt</option>
                <option value="2">Đã hủy</option>
            </select>

            <button id="filter-btn">Tìm kiếm</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên Khách Hàng</th>
                    <th>Địa chỉ</th>
                    <th>Ngày Đặt</th>
                    <th>Thanh Toán</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody id="order-list">
                <?php
                    $conn = mysqli_connect("localhost", "root", "", "web2_sql");
                    $query_lietke_dh = "SELECT orders.order_id, user.full_name, orders.order_date, orders.payment_method, status.id, orders.address 
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
                    <td><?= htmlspecialchars($row['address']) ?></td>               
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
                            } else if ($row['id'] == '2') {
                                echo '<span class="status-refused">Đã hủy</span>';
                            }else {
                                echo '
                                    <form method="POST" action="../../../BackEnd/Model/quanlisanpham/quanlidonhang/xulidonhang.php" style="display:inline;">
                                        <input type="hidden" name="order_id" value="'.$row['order_id'].'">
                                        <button type="submit" name="approve" class="btn approve-btn">Duyệt</button>
                                    </form>
                                    <form method="POST" action="../../../BackEnd/Model/quanlisanpham/quanlidonhang/xulidonhang.php" style="display:inline;">
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
        <div id="pagination" style="text-align: center; margin-top: 20px;">
            
        </div>


    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let currentPage = 1; 
        let totalPages = 1; 

        function loadOrders(page) {
            let search = $("#search-input").val();
            let status = $("#status-filter").val();
            let date = $("#date-filter").val();

            $.ajax({
                url: "fetch_orders.php",
                type: "GET",
                data: { 
                    page: page, 
                    search: search, 
                    status: status, 
                    date: date 
                },
                dataType: "json",
                success: function(response) {
                    let orders = response.orders;
                    totalPages = response.total_pages;
                    let html = "";

                    if (orders.length > 0) {
                        $.each(orders, function(index, order) {
                            html += `<tr>
                                <td>${(page - 1) * 5 + index + 1}</td>
                                <td>${order.full_name}</td>
                                <td>${order.address}</td>
                                <td>${order.order_date}</td>
                                <td>${order.payment_method === 'Cash' ? '<i class="fas fa-truck"></i> COD' : '<i class="fas fa-credit-card"></i> Online'}</td>
                                <td>${order.id == '4' ? '<span class="status-approved">✅ Đã duyệt</span>' : 
                                    (order.id == '2' ? '<span class="status-refused">❌ Đã hủy</span>' : 
                                    `<form method="POST" action="../../../BackEnd/Model/quanlisanpham/quanlidonhang/xulidonhang.php" style="display:inline;">
                                        <input type="hidden" name="order_id" value="${order.order_id}">
                                        <button type="submit" name="approve" class="btn approve-btn">✔️ Duyệt</button>
                                    </form>
                                    <form method="POST" action="../../../BackEnd/Model/quanlisanpham/quanlidonhang/xulidonhang.php" style="display:inline;">
                                        <input type="hidden" name="order_id" value="${order.order_id}">
                                        <button type="submit" name="cancel" class="btn cancel-btn">❌ Hủy</button>
                                    </form>`) }
                                </td>
                                <td>
                                    <a href="chitietdonhang.php?order_id=${order.order_id}" class="btn detail-btn">Chi tiết</a>
                                </td>
                            </tr>`;
                        });
                    } else {
                        html = `<tr><td colspan="7">Không có đơn hàng nào</td></tr>`;
                    }

                    $("#order-list").html(html);
                    loadPagination();
                }
            });
        }

        function loadPagination() {
            let paginationHtml = "";
            for (let i = 1; i <= totalPages; i++) {
                paginationHtml += `<div class="page-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</div>`;
            }
            $("#pagination").html(paginationHtml);

            $(".page-btn").click(function() {
                let page = $(this).data("page");
                if (page !== currentPage) {
                    currentPage = page;
                    loadOrders(currentPage);
                }
            });
        }

        $("#filter-btn").click(function() {
            currentPage = 1;
            loadOrders(currentPage);
        });

        $("#search-input, #status-filter, #date-filter").on("change keyup", function() {
            currentPage = 1;
            loadOrders(currentPage);
        });

        loadOrders(currentPage);
    });
</script>


</html>