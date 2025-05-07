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
            min-height: 100vh;
            color: #1a1a1a;
        }

        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
            display: flex;
            flex-wrap: wrap;
        }

        .sidebar {
            width: 250px;
            background: #fff;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin-right: 30px;
            flex-shrink: 0;
        }

        .sidebar h4 {
            font-size: 1.2rem;
            font-weight: 500;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            text-align: center;
        }

        .category-type {
            font-size: 0.95rem;
            font-weight: 400;
            color: #1a1a1a;
            padding: 10px 15px;
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .category-type:hover {
            background: #f8f9fa;
            color: #d4af37;
            cursor: pointer;
        }

        .category-list {
            list-style: none;
            padding-left: 0;
            background: #f8f9fa;
            display: none; /* Ẩn mặc định để có hiệu ứng xổ ra */
        }

        .category-item {
            font-size: 0.95rem;
            font-weight: 400;
            color: #1a1a1a;
            padding: 10px 15px;
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .category-item:hover {
            color: #d4af37;
            cursor: pointer;
        }

        .category-item.active {
            color: #d4af37;
            font-weight: 500;
            background: #fff;
        }

        .main-content {
            flex: 1;
            min-width: 0;
        }

        h2 {
            font-size: 1.8rem;
            font-weight: 400;
            color: #1a1a1a;
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        .filters {
            display: flex;
            margin-bottom: 40px;
        }

        input {
            border-radius: 0;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            font-size: 0.9rem;
            font-weight: 300;
            color: #1a1a1a;
            background: #fff;
            width: 100%;
        }

        input:focus {
            border-color: #d4af37;
            box-shadow: none;
        }

        #product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }


        .no-products {
            grid-column: 1 / -1; 
            text-align: center;
            padding: 40px 20px;
            /* background: linear-gradient(135deg, #ffffff, #f1f3f5); */
            border-radius: 12px;
            /* box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); */
            animation: fadeIn 0.8s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 300px; 
        }

        .no-products .not-found-img {
            max-width: 550px;
            height: auto;
            animation: wobble 3s infinite;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.2));
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
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                margin-right: 0;
                margin-bottom: 20px;
            }

            .main-content {
                width: 100%;
            }

            .filters {
                flex-direction: column;
            }

            .filters .col-md-6, .filters .col-md-3 {
                width: 100%;
                margin-bottom: 10px;
            }

            #product-list {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }

            .card-img-top {
                height: 200px;
            }

            .card-body {
                padding: 10px; /* Giảm padding trong các card để tiết kiệm không gian */
            }

            .card-title {
                font-size: 0.9rem; /* Thu nhỏ font chữ */
            }

            .pagination {
                justify-content: center;
                margin-top: 20px;
            }

            .pagination .page-item .page-link {
                padding: 8px 12px;
            }
        }

        @media (max-width: 576px) {
            .filters .col-md-6, .filters .col-md-3 {
                width: 100%;
                margin-bottom: 15px;
            }

            .sidebar {
                padding: 15px;
            }

            .main-content {
                padding: 15px;
            }

            .filters input {
                font-size: 0.85rem;
            }

            .category-item {
                font-size: 0.85rem;
            }

            .card-title {
                font-size: 0.85rem;
            }

            .card-img-top {
                height: 180px; /* Thu nhỏ ảnh */
            }
        }
        @media (min-width: 576px) and (max-width: 768px) {
            .content {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                margin-bottom: 15px;
            }

            .main-content {
                width: 100%;
            }

            #product-list {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 16px;
            }

            .filters {
                flex-direction: column;
            }

            .filters .col-md-6,
            .filters .col-md-3 {
                width: 100%;
                margin-bottom: 10px;
            }

            .card-img-top {
                height: 200px;
            }

            .pagination {
                justify-content: center;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="sidebar">
            <h4>Tìm Kiếm Theo Chủng loại</h4>
            <ul id="category-type-list" class="list-unstyled">
                <li class="category-item all-categories" data-category-id="">Tất cả thể loại</li>
            </ul>
        </div>

        <div class="main-content">
            <h2>Danh Sách Sản Phẩm</h2>

            <div class="row mb-3 filters">
                <div class="col-md-6">
                    <input type="text" id="search_name" class="form-control" placeholder="Tìm kiếm sản phẩm">
                </div>
                <div class="col-md-3">
                    <input type="number" id="min_price" class="form-control" placeholder="Giá thấp nhất">
                </div>
                <div class="col-md-3">
                    <input type="number" id="max_price" class="form-control" placeholder="Giá cao nhất">
                </div>
            </div>

            <div id="product-list"></div>

            <nav>
                <ul class="pagination justify-content-center" id="pagination"></ul>
            </nav>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function () {
    let selectedCategoryId = null; // Biến để lưu category_id đang chọn

    // Load danh sách chủng loại
    function loadCategoryTypes() {
        $.ajax({
            url: "http://localhost/Web2/FrontEnd/PublicUI/SanPham/fetch_category_types.php",
            method: "GET",
            dataType: "json",
            success: function (response) {
                let html = '<li class="category-item all-categories" data-category-id="">Tất cả thể loại</li>';
                response.forEach(type => {
                    html += `
                        <li class="category-type" data-type-id="${type.category_type_id}">
                            ${type.type_name}
                            <ul class="category-list" id="category-list-${type.category_type_id}"></ul>
                        </li>`;
                });
                
                $("#category-type-list").html(html);
            },
            error: function (xhr, status, error) {
                console.error("Error loading category types:", error);
            }
        });
    }

    // Load danh sách thể loại theo category_type_id
    function loadCategories(typeId = null) {
        $.ajax({
            url: "http://localhost/Web2/FrontEnd/PublicUI/SanPham/fetch_categories.php",
            method: "GET",
            data: { category_type_id: typeId },
            dataType: "json",
            success: function (response) {
                if (typeId) {
                    let html = "";
                    response.forEach(category => {
                        html += `
                            <li class="category-item" data-category-id="${category.category_id}">
                                ${category.category_name}
                            </li>`;
                    });
                    window.scrollTo(0, 0);
                    $(`#category-list-${typeId}`).html(html).slideDown(300); 
                }
            },
            error: function (xhr, status, error) {
                console.error("Error loading categories:", error);
            }
        });
    }

    // Load sản phẩm
    function fetchProducts(page = 1) {
        let search_name = $("#search_name").val();
        let category = selectedCategoryId; // Chỉ dùng category từ sidebar
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
                console.log('Search response (JSON):', response);
                console.log('JSON stringified:', JSON.stringify(response, null, 2));
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
                    productsHtml = "<div class='no-products text-center'>" +
               "<img src='../../../BackEnd/Uploads/Product Picture/notfound.png' alt='Not Found' class='not-found-img'>" +
               "</div>";
                }
                window.scrollTo(0, 0);
                $("#product-list").html(productsHtml);
                generatePagination(response.total_pages, response.current_page);
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    // Tạo phân trang
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

    // Sự kiện click vào chủng loại
    $(document).on("click", ".category-type", function () {
        let typeId = $(this).data("type-id");
        let categoryList = $(`#category-list-${typeId}`);
        if (categoryList.children().length === 0) { 
            loadCategories(typeId);
        } 
    });


    $(document).on("click", ".category-item", function () {
        let categoryId = $(this).data("category-id");
        $(".category-item").removeClass("active");

        $(this).addClass("active");
        selectedCategoryId = categoryId === "" ? null : categoryId;
        fetchProducts(1); 
    });

    $("#search_name, #min_price, #max_price").on("keyup change", function () {
        fetchProducts(); 
    });

    $(document).on("click", ".page-link[data-page-number]", function (e) {
        e.preventDefault();
        let page = $(this).data("page-number");
        if (page !== undefined && !$(this).parent().hasClass('disabled')) {
            fetchProducts(page);
        }
    });


    loadCategoryTypes();
    fetchProducts();
});
    </script>
</body>
</html>