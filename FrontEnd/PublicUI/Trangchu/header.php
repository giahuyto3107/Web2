<?php
session_start();

// Kiểm tra xem người dùng đã đăng ký hoặc đăng nhập chưa
$user_name = isset($_SESSION['dangky']) ? $_SESSION['dangky'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null);
?>
<style>
    .icon {
    width: 28px;
    height: 28px;
    filter: invert(1); /* Đảo màu để icon trắng trên nền đen */
}
</style>
<header class="bg-black text-white p-4 flex justify-between items-center shadow-lg">
    <h1 class="text-2xl font-bold text-yellow-400">Tên Trang Web</h1>

    <!-- Ô tìm kiếm -->
    <input type="text" placeholder="Tìm kiếm sản phẩm..." 
        class="p-2 rounded bg-white text-black w-1/3 border-2 border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">

    <div class="flex space-x-4 items-center">
        <?php if ($user_name): ?>
            <span class="font-bold max-w-xs truncate text-yellow-400">
                Xin chào, <?php echo htmlspecialchars($user_name); ?>!
            </span>
            <a href="index.php?page=logout" 
                class="px-4 py-2 border-2 border-yellow-400 text-yellow-400 rounded hover:bg-yellow-400 hover:text-black transition">
                Đăng xuất
            </a>
        <?php else: ?>
            <a href="index.php?page=signup" 
                class="px-4 py-2 border-2 border-yellow-400 text-yellow-400 rounded hover:bg-yellow-400 hover:text-black transition">
                Đăng ký
            </a>
            <a href="index.php?page=login" 
                class="px-4 py-2 border-2 border-yellow-400 text-yellow-400 rounded hover:bg-yellow-400 hover:text-black transition">
                Đăng nhập
            </a>
        <?php endif; ?>

        <!-- Icon profile -->
        <a href="#"><img src="../../Pictures/icon-profile.svg" alt="Thông tin cá nhân" class="icon"></a>

        <!-- Icon giỏ hàng -->
        <a href="#"><img src="../../Pictures/icon-cart.svg" alt="Giỏ hàng" class="icon"></a>
    </div>
</header>
