<?php 
include ('../../../BackEnd/Config/config.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

        body {
            background-color: aliceblue;
            font-family: 'Poppins', sans-serif;

        }


        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }


        .row.mb-3 {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
        }


        input, select {
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color:rgb(34, 56, 255);
            box-shadow: 0 0 5px rgba(255, 87, 34, 0.3);
        }


        #product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }


        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
            background: #fff;
            overflow: hidden;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }


        .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            height: 230px;
            object-fit: contain;
            padding: 10px;
            background: #f1f1f1;
        }

        /* 🌟 Nội dung sản phẩm */
        .card-body {
            text-align: center;
        }

        .card-title {
            font-size: 16px;
            font-weight: bold;
            color: #343a40;
            height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-text {
            color: #6c757d;
            font-size: 14px;
            height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .text-danger {
            font-size: 18px;
            font-weight: bold;
            color: #e44d26;
        }

        
        .btn-primary {
            background-color: #ff5722;
            border-color: #ff5722;
            transition: 0.3s;
            padding: 5px 15px;
            font-size: 14px;
        }

        .btn-primary:hover {
            background-color: #d84315;
        }

        
        .pagination {
            margin-top: 20px;
        }

        .pagination .page-item .page-link {
            color: #ff5722;
            border-radius: 5px;
            margin: 0 3px;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff5722;
            border-color: #ff5722;
            color: #fff;
        }

        .pagination .page-item .page-link:hover {
            background-color: #d84315;
            color: #fff;
        }

        
        @media (max-width: 768px) {
            #product-list {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }

            .card-img-top {
                height: 200px;
            }

            .card-title {
                font-size: 14px;
            }

            .text-danger {
                font-size: 16px;
            }
        }

    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Danh sách sản phẩm</h2>

        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="search_name" class="form-control" placeholder="Bạn cần tìm gì...?">
            </div>
            <div class="col-md-2">
                <select id="category" class="form-control">
                    <option value="">Chọn loại sản phẩm</option>
                    <option value="1">Fiction</option>
                    <option value="2">Non-Fiction</option>
                    <option value="3">Science Fiction</option>
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

        $.ajax({
            url: "fetch_products.php",
            method: "GET",
            data: { search_name, category, min_price, max_price, page },
            dataType: "json",
            success: function (response) {
                let productsHtml = "";
                if (response.products.length > 0) {
                    response.products.forEach(product => {
                        productsHtml += `
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                <div class="card mb-4">
                                    <img src="${product.image_url}" class="card-img-top" alt="Hình ảnh sản phẩm">

                                    <div class="card-body">
                                        <h5 class="card-title">${product.product_name}</h5>
                                        <p class="card-text">${product.product_description}</p>
                                        <p class="text-danger fw-bold">${parseFloat(product.price).toLocaleString()} VNĐ</p>
                                        <p class="text-muted">Kho: ${product.stock_quantity}</p>
                                        <a href="product_detail.php?id=${product.product_id}" class="btn btn-primary">Xem chi tiết</a>
                                    </div>
                                </div>
                            </div>`;
                    });
                } else {
                    productsHtml = "<p class='text-center'>Không có sản phẩm nào!</p>";
                }
                $("#product-list").html(`<div class="row">${productsHtml}</div>`);
                generatePagination(response.total_pages, page);
            }
        });
    }

    function generatePagination(total_pages, current_page) {
        let paginationHtml = "";
        for (let i = 1; i <= total_pages; i++) {
            paginationHtml += `<li class="page-item ${i == current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }
        $("#pagination").html(paginationHtml);
    }

    $(document).on("keyup change", "#search_name, #category, #min_price, #max_price", function () {
        fetchProducts();
    });

    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        let page = $(this).data("page");
        fetchProducts(page);
    });

    fetchProducts();
});

</script>
</body>
</html>
