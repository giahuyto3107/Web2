<?php
session_start();
include ('../../../BackEnd/Config/config.php');
$user_name = isset($_SESSION['dangky']) ? $_SESSION['dangky'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null);
if (isset($_GET['page']) && $_GET['page'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
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
            display: flex;
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
            font-family: 'Georgia', serif;
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
            padding: 10px 0;
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
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>Góc Sách Nhỏ</h1>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Tìm kiếm sách...">
        </div>
        <div class="user-actions">
            <a href="/Web2/FrontEnd/PublicUI/Giohang/giohang.php" class="icon-link">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if ($cart_count > 0): ?>
                    <span class="cart-badge"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="profile-icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="/Web2/FrontEnd/PublicUI/User/user.php"><i class="fa-solid fa-user"></i> Hồ sơ</a>
                    <a href="/Web2/FrontEnd/PublicUI/Lichsumuahang/listmuahang.php"><i class="fa-solid fa-box"></i> Đơn hàng</a>
                    <a href="?page=logout"><i class="fa-solid fa-sign-out-alt"></i> Đăng xuất</a>
                </div>
            <?php else: ?>
                <a href="?page=login">Đăng nhập</a>
                <a href="?page=signup">Đăng ký</a>
            <?php endif; ?>
        </div>
    </header>
    <main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileIcon = document.querySelector('.profile-icon');
            const dropdown = document.querySelector('.dropdown-menu');

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
            } else {
                console.error('Profile icon or dropdown menu not found in DOM');
            }
        });
    </script>