<?php
session_start();
include('../../../BackEnd/Config/config.php');
$user_name = isset($_SESSION['dangky']) ? $_SESSION['dangky'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null);
if (isset($_GET['page']) && $_GET['page'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}
$full_name = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $query = "SELECT full_name FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $full_name = $row['full_name'];
    }

    $stmt->close();
}
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT SUM(quantity) as total FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $cart_count = $row['total'] ?? 0;
    $stmt->close();
}

$categories = $conn->query("SELECT * FROM category WHERE status_id = 1 LIMIT 4");
$best_sellers = $conn->query("SELECT * FROM product WHERE status_id = 1 ORDER BY stock_quantity ASC LIMIT 5");
$new_releases = $conn->query("SELECT * FROM product WHERE status_id = 1 ORDER BY created_at DESC LIMIT 5");
$featured_collection = $conn->query("SELECT p.* FROM product p JOIN product_category pc ON p.product_id = pc.product_id WHERE pc.category_id = 1 AND p.status_id = 1 LIMIT 3");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Góc Sách - Trang Chủ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Georgia&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f5f5;
            color: #111111;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        header {
            background: #ffffff;
            padding: 20px 60px;
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        header .logo h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: rgb(0, 0, 0);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        header .search-bar {
            flex: 1;
            max-width: 400px;
            margin: 0 40px;
            position: relative;
        }

        header input[type="text"] {
            width: 100%;
            padding: 10px 0;
            background: transparent;
            color: #111111;
            border: none;
            border-bottom: 1px solid #cccccc;
            font-size: 0.9rem;
            font-weight: 300;
            letter-spacing: 1px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        header input[type="text"]::placeholder {
            color: #888888;
        }

        header input[type="text"]:focus {
            border-color: rgb(0, 0, 0);
        }

        header .user-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
        }

        header .user-actions .icon-link {
            padding: 8px;
            color: #111111;
            text-decoration: none;
            position: relative;
        }

        header .user-actions .icon-link i {
            font-size: 22px;
        }

        header .user-actions .icon-link:hover i {
            color: rgb(0, 0, 0);
        }

        header .user-actions .icon-link .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: rgb(255, 0, 0);
            color: #ffffff;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        header .user-actions .profile-icon {
            padding: 8px;
            cursor: pointer;
            color: #111111;
        }

        header .user-actions .profile-icon i {
            font-size: 22px;
        }

        header .user-actions .profile-icon:hover i {
            color: rgb(0, 0, 0);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            min-width: 220px;
            padding: 15px;
            z-index: 1001;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            padding: 10px 30px;
            color: #333333;
            font-size: 0.9rem;
            font-weight: 400;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .dropdown-menu a i {
            margin-right: 10px;
            font-size: 1rem;
            color: #666666;
        }

        .dropdown-menu a:hover {
            color: rgb(0, 0, 0);
        }

        .dropdown-menu a:hover i {
            color: rgb(53, 53, 53);
        }

        .btn2 {
            display: inline-block;
            padding: 0.9rem 1.8rem;
            font-size: 16px;
            font-weight: 500;
            color: black;
            border: 3px solid rgb(255, 255, 255);
            cursor: pointer;
            position: relative;
            background-color: white;
            text-decoration: none;
            overflow: hidden;
            z-index: 1;
            font-family: 'Poppins', sans-serif;
            transition: color 0.3s;
        }

        .btn2::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(255, 255, 255);
            transform: translateX(-100%);
            transition: all .3s;
            z-index: -1;
        }

        .btn2:hover {
            color: white;
        }

        .btn2:hover::before {
            transform: translateX(0);
            background-color: black;
        }
        /* Username style*/
        .profile-icon {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: #fff; /* hoặc màu chữ tùy theo giao diện */
        }

        .profile-icon .username {
            font-weight: 400;
            font-size: 1.2rem;
            font-family: 'Poppins', sans-serif;
        }
        /* Search Results Styles */
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1001;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .search-results.active {
            display: block;
        }

        .search-results .result-item {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333333;
            transition: background 0.3s ease;
        }

        .search-results .result-item:hover {
            background: #f5f5f5;
        }

        .search-results .result-item img {
            width: 40px;
            height: 60px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 4px;
        }

        .search-results .result-item span {
            font-size: 0.9rem;
            font-weight: 400;
        }

        .search-results .no-results {
            padding: 10px 20px;
            color: #888888;
            font-size: 0.9rem;
        }
        @media (max-width: 1024px) {
            header {
                padding: 15px 30px;
                flex-wrap: wrap;
            }

            header .search-bar {
                margin: 15px 0;
                width: 100%;
                max-width: 100%;
                order: 3;
            }

            header .user-actions {
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px 20px;
            }

            header .logo h1 {
                font-size: 1.5rem;
            }

            header .search-bar {
                order: 2;
                margin-top: 10px;
            }

            header .user-actions {
                order: 3;
                width: 100%;
                justify-content: space-between;
                margin-top: 10px;
            }

            header .user-actions .icon-link,
            header .user-actions .profile-icon {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 10px 15px;
            }

            header .logo h1 {
                font-size: 1.3rem;
            }

            header input[type="text"] {
                font-size: 0.8rem;
            }

            .btn2 {
                padding: 0.6rem 1.2rem;
                font-size: 14px;
            }

            .search-results .result-item span {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="logo">
            <a href="?page=home"><h1>Góc Sách Nhỏ</h1></a>
        </div>
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Tìm kiếm sách...">
            <div class="search-results" id="search-results"></div>
        </div>
        <div class="user-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?page=cart" class="icon-link" data-page="cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="profile-icon">
                    <i class="fa-solid fa-user"></i>
                    <span class="username"><?php echo htmlspecialchars($full_name); ?></span>
                </div>
                <div class="dropdown-menu">
                    <a href="?page=profile" data-page="profile"><i class="fa-solid fa-user"></i> Hồ sơ</a>
                    <a href="?page=orders" data-page="orders"><i class="fa-solid fa-box"></i> Đơn hàng</a>
                    <a href="http://localhost/Web2/FrontEnd/PublicUI/Trangchu/Pages/logout.php"><i class="fa-solid fa-sign-out-alt"></i> Đăng xuất</a>
                </div>
            <?php else: ?>
                <button>
                    <a style="text-decoration: none;" href="?page=login" data-page="login" class="btn2">Đăng nhập</a>
                    <a style="text-decoration: none;" href="?page=signup" data-page="signup" class="btn2">Đăng ký</a>
                </button>
            <?php endif; ?>
        </div>
    </header>

    <div id="main-content"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileIcon = document.querySelector('.profile-icon');
            const dropdown = document.querySelector('.dropdown-menu');
            const homeLink = document.getElementById('home-link');
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            const mainContent = document.getElementById('main-content');

            // Xử lý dropdown menu
            if (profileIcon && dropdown) {
                profileIcon.addEventListener('click', (e) => {
                    e.preventDefault();
                    dropdown.classList.toggle('active');
                    e.stopPropagation();
                });
                document.addEventListener('click', (e) => {
                    if (!profileIcon.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.remove('active');
                    }
                });
            }

            // Xử lý load trang chủ
            if (homeLink) {
                homeLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Logo clicked!');
                    loadPage('/Web2/FrontEnd/PublicUI/Trangchu/Pages/home.php');
                });
            }

            // Hàm load nội dung bằng AJAX
            function loadPage(url) {
                console.log('Attempting to load:', url);
                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.text();
                    })
                    .then(data => {
                        console.log('Data received:', data);
                        mainContent.innerHTML = data;
                    })
                    .catch(error => {
                        console.error('Error loading page:', error);
                        mainContent.innerHTML = '<p>Có lỗi xảy ra khi tải trang. Vui lòng thử lại.</p>';
                    });
            }

            let searchTimeout;
                searchInput.addEventListener('input', () => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const query = searchInput.value.trim();
                        if (query.length > 0) {
                            $.ajax({
                                url: 'http://localhost/Web2/FrontEnd/PublicUI/Trangchu/search.php', // Sửa URL đúng
                                method: 'POST',
                                data: { query: query },
                                dataType: 'json',
                                success: (response) => {
                                    console.log('Search response (JSON):', response);
                                    console.log('JSON stringified:', JSON.stringify(response, null, 2));
                                    searchResults.innerHTML = '';
                                    if (response.success && response.results.length > 0) {
                                        response.results.forEach(book => {
                                            const imagePath = book.image_url 
                                                ? `/Web2/BackEnd/Uploads/Product Picture/${book.image_url}` 
                                                : '/Web2/FrontEnd/PublicUI/assets/images/default-book.jpg';
                                            const resultItem = `
                                                <a href="?page=product_details&id=${book.product_id}" data-page="product_details&id=${book.product_id}" class="result-item">
                                                    <img src="${imagePath}" alt="${book.product_name}">
                                                    <span>${book.product_name}</span>
                                                </a>`;
                                            searchResults.innerHTML += resultItem;
                                        });
                                        searchResults.classList.add('active');
                                    } else {
                                        searchResults.innerHTML = '<div class="no-results">Không tìm thấy sách nào.</div>';
                                        searchResults.classList.add('active');
                                    }
                                },
                                error: (xhr, status, error) => {
                                    console.error('Search error:', error);
                                    console.log('Response:', xhr.responseText); // In phản hồi để debug
                                    searchResults.innerHTML = '<div class="no-results">Có lỗi xảy ra khi tìm kiếm.</div>';
                                    searchResults.classList.add('active');
                                }
                            });
                        } else {
                            searchResults.innerHTML = '';
                            searchResults.classList.remove('active');
                        }
                    }, 300);
                });

                // Ẩn kết quả tìm kiếm khi click ra ngoài
                document.addEventListener('click', (e) => {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.remove('active');
                    }
                });
        });
    </script>
</body>
</html>