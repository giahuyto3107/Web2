<?php
session_start();
$user_id = 1;
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử mua hàng</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
        }
        .container {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 2rem;
        }
        .order-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .order-card .status {
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-align: center;
        }
        .order-card .status.approved {
            background: #e8f5e9;
            color: #28a745;
        }
        .order-card .status.cancelled {
            background: #ffebee;
            color: #dc3545;
        }
        .order-card .status.pending {
            background: #fff3e0;
            color: #ffc107;
        }
        .btn-outline-primary {
            border: 2px solid #3498db;
            color: #3498db;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-outline-primary:hover {
            background: #3498db;
            color: #fff;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: border-color 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        .pagination .page-item .page-link {
            border-radius: 8px;
            margin: 0 4px;
            color: #3498db;
            border: 1px solid #3498db;
            transition: all 0.3s ease;
        }
        .pagination .page-item.active .page-link {
            background: #3498db;
            color: #fff;
            border-color: #3498db;
        }
        .pagination .page-item .page-link:hover {
            background: #3498db;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1>Lịch sử mua hàng</h1>
        <div class="row mb-4">
            <div class="col-md-8">
                <input type="text" class="form-control" id="search-input" placeholder="Tìm kiếm theo mã đơn hàng">
            </div>
            <div class="col-md-4">
                <select class="form-select" id="status-filter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="4">Đã duyệt</option>
                    <option value="2">Đã hủy</option>
                    <option value="3">Chờ duyệt</option>
                </select>
            </div>
        </div>
        <div id="order-list">
            <?php
                $conn = mysqli_connect("localhost", "root", "", "web2_sql");
                $query_lietke_dh = "SELECT orders.order_id, orders.order_date, orders.total_amount, status.status_name 
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0">Mã đơn hàng: <?= htmlspecialchars($row['order_id']) ?></h5>
                            <small class="text-muted">Ngày mua: <?= htmlspecialchars($row['order_date']) ?></small>
                        </div>
                        <div class="status <?= $status_class ?>">
                            <?= htmlspecialchars($row['status_name']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0">Tổng giá trị: <?= htmlspecialchars($row['total_amount']) ?> đ</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="chitietdonhang.php?order_id=<?= urlencode($row['order_id']) ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                        </div>
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
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="mb-0">Mã đơn hàng: ${order.order_id}</h5>
                                            <small class="text-muted">Ngày mua: ${order.order_date}</small>
                                        </div>
                                        <div class="status ${status_class}">
                                            ${order.status_name}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-0">Tổng giá trị: ${order.total_amount} đ</p>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <a href="chitietdonhang.php?order_id=${order.order_id}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>`;
                            });
                        } else {
                            html = `<div class="text-center py-4">Không có đơn hàng nào</div>`;
                        }

                        $("#order-list").html(html);
                        loadPagination();
                    }
                });
            }

            function loadPagination() {
                let paginationHtml = "";
                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                }
                $("#pagination").html(paginationHtml);

                $(".page-link").click(function(e) {
                    e.preventDefault();
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
</body>
</html>