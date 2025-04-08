<?php 
include ('../../../BackEnd/Config/config.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #ffffff;
            /* padding: 50px; */
            min-height: 100vh;
            color: #1a1a1a;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
        }

        /* Header */
        h2 {
            font-size: 1.8rem;
            font-weight: 400;
            color: #1a1a1a;
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        /* Filters */
        .filters {
            display: flex;
            
            margin-bottom: 40px;
        }

        input, select {
            border-radius: 0;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            font-size: 0.9rem;
            font-weight: 300;
            color: #1a1a1a;
            background: #fff;
            width: 100%;
        }

        input:focus, select:focus {
            border-color: #d4af37;
            box-shadow: none;
        }

        /* Product List */
        #product-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Sử dụng auto-fill để lấp đầy không gian */
    gap: 30px;
}

        .card {
            border: 1px solid #e0e0e0;
            border-radius: 0;
            background: #fff;
            overflow: hidden;
        }

        .card-img-top {
            height: 230px;
            object-fit: contain;
            padding: 15px;
            background: #fff;
            width: 100%;
        }

        .card-body {
            text-align: center;
            padding: 20px;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 400;
            color: #1a1a1a;
            height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 0.85rem;
            font-weight: 300;
            color: #666;
            height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 15px;
        }

        .text-danger {
            font-size: 1.1rem;
            font-weight: 400;
            color: #d4af37;
            margin-bottom: 15px;
        }

        .btn-primary {
            background: #1a1a1a;
            border: none;
            padding: 8px 20px;
            font-size: 0.85rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #333;
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

        /* Responsive */
        @media (max-width: 768px) {
            #product-list {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }

            .card-img-top {
                height: 200px;
            }

            .card-title {
                font-size: 0.9rem;
            }

            .text-danger {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Danh Sách Sản Phẩm</h2>

        <div class="row mb-3 filters">
            <div class="col-md-6">
                <input type="text" id="search_name" class="form-control" placeholder="Tìm kiếm sản phẩm">
            </div>
            <div class="col-md-2">
                <select id="category" class="form-control">
                    <option value="">Tất cả</option>
                    <option value="1">Fiction</option>
                    <option value="2">Non-Fiction</option>
                    <option value="3">Science Fiction</option>
                    <option value="4">Mystery</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" id="min_price" class="form-control" placeholder="Giá thấp nhất">
            </div>
            <div class="col-md-2">
                <input type="number" id="max_price" class="form-control" placeholder="Giá cao nhất">
            </div>
        </div>

        <div id="product-list"></div>

        <nav>
            <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function () {
    function fetchProducts(page = 1) {
        let search_name = $("#search_name").val();
        let category = $("#category").val();
        let min_price = $("#min_price").val();
        let max_price = $("#max_price").val();

        min_price = min_price < 0 ? 0 : min_price;
        max_price = max_price < 0 ? 0 : max_price;

        if (min_price && max_price && parseFloat(min_price) > parseFloat(max_price)) {
            [min_price, max_price] = [max_price, min_price];
        }

        $.ajax({
            url: "http://localhost/Web2/FrontEnd/PublicUI/SanPham/fetch_products.php",
            method: "GET",
            data: { search_name, category, min_price, max_price, page },
            dataType: "json",
            success: function (response) {
                let productsHtml = "";
                if (response.products.length > 0) {
                    response.products.forEach(product => {
                        productsHtml += `
                            <div class="col">
                                <div class="card mb-4">
                                    <a href="?page=product_details&id=${product.product_id}" 
                                       data-page="product_details&id=${product.product_id}">
                                        <img src="${product.image_url}" class="card-img-top" alt="Hình ảnh sản phẩm">                                    
                                    </a>
                                    <div class="card-body">
                                        <h5 class="card-title">${product.product_name}</h5>
                                        
                                        <p class="text-danger">${parseFloat(product.price).toLocaleString()} VNĐ</p>
                                        <a href="?page=product_details&id=${product.product_id}" 
                                           data-page="product_details&id=${product.product_id}" 
                                           class="btn btn-primary">Xem chi tiết</a>
                                    </div>
                                </div>
                            </div>`;
                    });
                } else {
                    productsHtml = "<p class='text-center' style='font-weight: 300; color: #666;'>Không có sản phẩm nào</p>";
                }
                $("#product-list").html(productsHtml);
                generatePagination(response.total_pages, response.current_page);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function generatePagination(total_pages, current_page) {
        let paginationHtml = "";
        if (total_pages > 1) {
            for (let i = 1; i <= total_pages; i++) {
                paginationHtml += `
                    <li class="page-item ${i === current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page-number="${i}">${i}</a>
                    </li>`;
            }
        }
        $("#pagination").html(paginationHtml);
    }

    $("#min_price, #max_price").on('input', function() {
        if (this.value < 0) {
            this.value = 0;
        }
    });

    $("#search_name, #category, #min_price, #max_price").on("keyup change", function () {
        fetchProducts();
    });

    // Xử lý click phân trang
    $(document).on("click", ".page-link[data-page-number]", function (e) {
        e.preventDefault();
        let page = $(this).data("page-number");
        if (page !== undefined && !$(this).parent().hasClass('disabled')) {
            fetchProducts(page);
        }
    });

    // Gọi lần đầu
    fetchProducts();
});
</script>
</body>
</html>