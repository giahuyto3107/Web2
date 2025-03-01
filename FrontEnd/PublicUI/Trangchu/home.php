<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-500 text-white p-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Tên Trang Web</h1>
        <input type="text" placeholder="Tìm kiếm sản phẩm..." class="p-2 rounded text-black">
        <div class="flex space-x-4">
            <a href="#"><img src="icon-user.svg" alt="Đăng ký" class="w-6 h-6"></a>
            <a href="#"><img src="icon-login.svg" alt="Đăng nhập" class="w-6 h-6"></a>
            <a href="#"><img src="icon-profile.svg" alt="Thông tin cá nhân" class="w-6 h-6"></a>
            <a href="#"><img src="icon-cart.svg" alt="Giỏ hàng" class="w-6 h-6"></a>
        </div>
    </header>
    
    <!-- Sản phẩm -->
    <div class="container mx-auto p-4">
        <h2 class="text-xl font-semibold mb-4">Sản phẩm mới nhất</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php
            include ('../../../BackEnd/Config/config.php');
            
            $sql_sp = "SELECT product_id, product_name, product_description, price, image_url FROM product ORDER BY product_id DESC LIMIT 4";
            $query_sp = mysqli_query($conn, $sql_sp);
            
            while ($row_sp = mysqli_fetch_array($query_sp)) { ?>
                <div class="bg-white p-4 shadow rounded-lg">
                    <a href="chitietsanpham.php?id=<?php echo $row_sp['product_id']; ?>">
                        <img src="<?php 
                            // Nếu đường dẫn chứa "example.com", thay bằng đường dẫn nội bộ
                            if (strpos($row_sp['image_url'], 'example.com') !== false) {
                                echo '/Web2/BackEnd/Uploads/Product Picture/' . strtolower(str_replace(' ', '_', $row_sp['product_name'])) . '.png';
                            } else {
                                echo $row_sp['image_url']; // Nếu là URL hợp lệ khác thì giữ nguyên
                            }
                        ?>" 
                        alt="<?php echo $row_sp['product_name']; ?>" 
                        class="w-full h-40 object-cover rounded">

                        <h3 class="text-lg font-semibold mt-2"><?php echo $row_sp['product_name']; ?></h3>
                        <p class="text-gray-600 text-sm"><?php echo $row_sp['product_description']; ?></p>
                        <p class="text-red-500 font-bold"><?php echo number_format($row_sp['price'], 0, ',', '.'); ?> VND</p>
                    </a>
                </div>
            <?php } ?>
        </div>
        <div class="text-center mt-4">
            <button id="seeMore" class="text-blue-500 underline">See More</button>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center p-4 mt-8">
        <p>&copy; 2025 Tên Trang Web. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('seeMore').addEventListener('click', function() {
            window.location.href = 'danhsachSP.php';
        });
    </script>
</body>
</html>
