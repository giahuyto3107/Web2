<?php
session_start();
$user_id = $_SESSION['user_id'];
?>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Mua Hàng</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* body {
            background: #ffffff;
            padding: 50px;
            min-height: 100vh;
            color: #1a1a1a;
        } */

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px;
        }

        /* Header */
        h1 {
            font-size: 1.8rem;
            font-weight: 400;
            color: #1a1a1a;
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 40px;
        }

        /* Filters */
        .filters {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }

        .form-control, .form-select {
            border-radius: 0;
            border: 1px solid #e0e0e0;
            /* padding: 10px 15px; */
            font-size: 0.9rem;
            font-weight: 300;
            color: #1a1a1a;
            background: #fff;
        }

        .form-control:focus, .form-select:focus {
            border-color: #d4af37;
            box-shadow: none;
        }

        /* Order Card */
        .order-card {
            border-bottom: 1px solid #e0e0e0;
            padding: 20px 0;
        }

        .order-card:last-child {
            border-bottom: none;
        }

        .order-card .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-card h5 {
            font-size: 1rem;
            font-weight: 400;
            color: #1a1a1a;
            margin: 0;
        }

        .order-card .date {
            font-size: 0.85rem;
            font-weight: 300;
            color: #666;
        }

        .order-card .status {
            font-size: 0.85rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .order-card .status.approved {
            color: #1a1a1a;
        }

        .order-card .status.cancelled {
            color: #1a1a1a;
        }

        .order-card .status.pending {
            color: #d4af37;
        }

        .order-card .order-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-card .total {
            font-size: 0.9rem;
            font-weight: 400;
            color: #1a1a1a;
        }

        .order-card .btn-outline-primary {
            border: 1px solid #1a1a1a;
            color: #1a1a1a;
            font-size: 0.85rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 15px;
            background: none;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .order-card .btn-outline-primary:hover {
            background: #1a1a1a;
            color: #fff;
        }

        /* Pagination */
        .pagination {
            justify-content: center;
            margin-top: 40px;
        }

        .pagination .page-item .page-link {
            border-radius: 0;
            border: 1px solid #e0e0e0;
            color: #1a1a1a;
            font-size: 0.9rem;
            font-weight: 400;
            padding: 8px 12px;
            margin: 0 5px;
            background: #fff;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background: #d4af37;
            color: #fff;
            border-color: #d4af37;
        }

        .pagination .page-item .page-link:hover {
            background: #1a1a1a;
            color: #fff;
            border-color: #1a1a1a;
        }

        .btn btn-primary {
            background-color: #d4af37 !important;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lịch Sử Mua Hàng</h1>
        <div class="filters">
            <input type="text" class="form-control" id="search-input1" placeholder="Tìm kiếm theo mã đơn hàng">
            <select class="form-select" id="status-filter">
                <option value="">Tất cả trạng thái</option>
                <option value="4">Đã duyệt</option>
                <option value="7">Đã hủy</option>
                <option value="3">Chờ duyệt</option>
                <option value="5">Đã giao</option>
            </select>
        </div>
        <div id="order-list">
            <?php
                include '../../../BackEnd/Config/config.php';
                $query_lietke_dh = "SELECT orders.order_id, orders.order_date, orders.total_amount, status.status_name , orders.payment_method
                                    FROM orders
                                    JOIN status ON status.id = orders.status_id
                                    WHERE orders.user_id = $user_id";
                $result = mysqli_query($conn, $query_lietke_dh);

                while ($row = mysqli_fetch_array($result)) {
                    $status_class = "";
                    if ($row['status_name'] == "Đã duyệt") {
                        $status_class = "approved";
                    } elseif ($row['status_name'] == "Đã hủy") {
                        $status_class = "cancelled";
                    } elseif ($row['status_name'] == "Chờ duyệt") {
                        $status_class = "pending";
                    }
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <p>
                                <h5>Mã đơn hàng: <?= htmlspecialchars($row['order_id']) ?></h5>  
                                <?= htmlspecialchars($row['payment_method']) ?>: 
                                <?php 
                                    if (strtolower($row['payment_method']) === 'cod') {
                                        echo 'Thanh toán khi nhận hàng';
                                    } else {
                                        echo 'Thanh toán online';
                                    }
                                ?>
                            </p>
                            <div class="date">Ngày mua: <?= htmlspecialchars($row['order_date']) ?></div>
                        </div>
                        <div class="status <?= htmlspecialchars($status_class) ?>">
                            <?= htmlspecialchars($row['status_name']) ?>
                        </div>
                    </div>
                    <div class="order-details">
                    <div class="total">Tổng giá trị: <?= str_replace(['.00', '.'], ['', ','], number_format((float)$row['total_amount'], 2, '.', '')) ?> đ</div>
                        <a href="?page=order_details&order_id=<?= htmlspecialchars($row['order_id']) ?>" 
                        data-page="order_details&order_id=<?= htmlspecialchars($row['order_id']) ?>" 
                        class="btn btn-primary">Xem chi tiết</a>
                    </div>
                </div>
            <?php
                }
            ?>
        </div>
        <nav aria-label="Page navigation" class="d-flex justify-content-center mt-4">
            <ul class="pagination" id="pagination">
                <!-- Pagination will be loaded here -->
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
$(document).ready(function(){
    let currentPage = 1;
    let totalPages = 1;

    function loadPage(page){
        let search = $('#search-input1').val();
        let status = $('#status-filter').val();

        $.ajax({
            url: "http://localhost/Web2/FrontEnd/PublicUI/Lichsumuahang/fetch_history.php",
            type: "GET",
            data: {page: page, search: search, status: status},
            dataType: "json",
            success: function(response){
                console.log('Search response (JSON):', response);
                console.log('JSON stringified:', JSON.stringify(response, null, 2));
                let orders = response.orders;
                totalPages = response.total_pages;
                let html = "";

                if (orders.length > 0) {
                    $.each(orders, function(index, order) {
                        let status_class = "";
                        if (order.status_name == "Đã duyệt") {
                            status_class = "approved";
                        } else if (order.status_name == "Đã hủy") {
                            status_class = "cancelled";
                        } else if (order.status_name == "Chờ duyệt") {
                            status_class = "pending";
                        }

                        html += `
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <h5>Mã đơn hàng: ${order.order_id}</h5>
                                    <div class="date">Ngày mua: ${order.order_date}</div>
                                </div>
                                <div class="status ${status_class}">
                                    ${order.status_name}
                                </div>
                            </div>
                            <div class="order-details">
                                <div class="total">Tổng giá trị: ${order.total_amount} đ</div>
                                <a href="?page=order_details&order_id=${order.order_id}" 
                                   data-page="order_details&order_id=${order.order_id}" 
                                   class="btn btn-primary">Xem chi tiết</a>
                            </div>
                        </div>`;
                    });
                } else {
                    html = `<div class="text-center py-4" style="font-weight: 300; color: #666;">Không có đơn hàng nào</div>`;
                }

                $("#order-list").html(html);
                loadPagination();
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function loadPagination() {
        let paginationHtml = "";
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page-number="${i}">${i}</a>
            </li>`;
        }
        $("#pagination").html(paginationHtml);

        $(".page-link[data-page-number]").click(function(e) {
            e.preventDefault();
            let page = $(this).data("page-number");
            if (page !== currentPage) {
                currentPage = page;
                loadPage(currentPage);
            }
        });
    }

    $("#search-input1, #status-filter").on("change keyup", function() {
        currentPage = 1;
        loadPage(currentPage);
    });

    loadPage(currentPage);
});
</script>
</body>
</html>