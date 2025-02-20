<?php
session_start();
$user_id = 1;
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style></style>
<body>
    <div class="container">
        <div class="filter">
            <input class="input1" type="text" id="search-input" placeholder="Tìm kiếm theo mã đơn hàng">
            
            <select id="status-filter">
                <option value="">Tất cả trạng thái</option>
                <option value="4">Đã duyệt</option>
                <option value="2">Đã hủy</option>
                <option value="3">Chờ duyệt</option>
            </select>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Ngày mua hàng</th>
                    <th>Tổng giá trị đơn hàng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="order-list">
                <?php
                    $conn = mysqli_connect("localhost", "root", "", "web2_sql");
                    $query_lietke_dh = "SELECT orders.order_id, orders.order_date, orders.total_amount, status.status_name FROM orders
                                        JOIN status ON status.id=orders.status_id
                                        WHERE orders.user_id=$user_id";
                
                    $result = mysqli_query($conn, $query_lietke_dh);

                    while ($row = mysqli_fetch_array($result)) {
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['order_id']) ?></td>
                        <td><?= htmlspecialchars($row['order_date']) ?></td>               
                        <td><?= htmlspecialchars($row['total_amount']) ?></td>
                        <td><?= htmlspecialchars($row['status_name']) ?></td>
                        <td>
                            <a href="chitietdonhang.php?order_id=<?= urlencode($row['order_id']) ?>" class="btn detail-btn">Chi tiết</a>
                        </td>
                    </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
        <div class="pagination" id="pagination"></div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        let currentPage = 1;
        let totalPages = 1;

        function loadPage(page){
            let search = $('#search-input').val();
            let status = $('#status-filter').val();

            $.ajax({
                url: "fetch_history.php",
                type: "GET",
                data: {page: page, search: search, status: status},
                dataType: "json",
                success: function(response){
                    let orders = response.orders;
                    totalPages = response.total_pages;
                    let html = "";

                    if (orders.length > 0) {
                        $.each(orders, function(index, order) {
                            html += `<tr>
                                <td>${order.order_id}</td>
                                <td>${order.order_date}</td>
                                <td>${order.total_amount}</td>
                                <td>${order.status_name}</td>                            
                                <td>
                                    <a href="chitietdonhang.php?order_id=${order.order_id}" class="btn detail-btn">Chi tiết</a>
                                </td>
                            </tr>`;
                        });
                    } else {
                        html = `<tr><td colspan="5">Không có đơn hàng nào</td></tr>`;
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
                    loadPage(currentPage);
                }
            });
        }

        $("#search-input, #status-filter").on("change keyup", function() {
            currentPage = 1;
            loadPage(currentPage);
        });

        loadPage(currentPage);
    });
</script>
</html>