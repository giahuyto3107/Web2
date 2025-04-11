<head>
<link rel="stylesheet" href="css/sidebar.css" />
</head>
<div class="sidebar">
    <div class="top">
        <div class="logo">
            <span>Product Management</span>
        </div>
        <i class="bx bx-menu" id="sidebar-menu-btn"></i>
    </div>
    <div class="user">
        <i class="bx bxs-user-rectangle user-img"></i>
        <div class="user-info">
            <p class="bold" id="sidebar-username"></p>
            <p id="sidebar-user-email"></p>
        </div>
    </div>
    <ul>
        <li data-permission-id="5">
            <a href="index.php?action=thongke">
                <i class="bx bxs-shopping-bag"></i>
                <span class="nav-item">Thống kê</span>
            </a>
            <span class="tooltip">Thống kê</span>
        </li>
        <li data-permission-id="1">
            <a href="index.php?action=quanlisanpham">
                <i class="bx bxs-shopping-bag"></i>
                <span class="nav-item">Quản lí sản phẩm</span>
            </a>
            <span class="tooltip">Quản lí sản phẩm</span>
        </li>
        <li data-permission-id="8"> <!-- Assuming this falls under "Quản lý sản phẩm" -->
            <a href="index.php?action=quanliloaisp">
                <i class="bx bxs-grid-alt"></i>
                <span class="nav-item">Quản lí thể loại</span>
            </a>
            <span class="tooltip">Quản lí thể loại</span>
        </li>
        <li data-permission-id="6">
            <a href="index.php?action=quanlinhacungcap">
                <i class="bx bx-list-check"></i>
                <span class="nav-item">Quản lí nhà cung cấp</span>
            </a>
            <span class="tooltip">Quản lí nhà cung cấp</span>
        </li>
        <li data-permission-id="4">
            <a href="index.php?action=quanlibinhluan">
                <i class="bx bxs-food-menu"></i>
                <span class="nav-item">Quản lí bình luận</span>
            </a>
            <span class="tooltip">Quản lí bình luận</span>
        </li>
        <li data-permission-id="2">
            <a href="index.php?action=quanlidonhang">
                <i class="bx bx-body"></i>
                <span class="nav-item">Quản lí hóa đơn</span>
            </a>
            <span class="tooltip">Quản lí hóa đơn</span>
        </li>
        <li data-permission-id="9">
            <a href="index.php?action=quanliphieunhap">
                <i class="bx bx-location-plus"></i>
                <span class="nav-item">Quản lí phiếu nhập</span>
            </a>
            <span class="tooltip">Quản lí phiếu nhập</span>
        </li>
        <li data-permission-id="3">
            <a href="index.php?action=quanlitaikhoan">
                <i class="bx bx-cog"></i>
                <span class="nav-item">Quản lí tài khoản</span>
            </a>
            <span class="tooltip">Quản lí tài khoản</span>
        </li>
        <li data-permission-id="10"> <!-- Assuming this falls under "Quản lý tài khoản" -->
            <a href="index.php?action=quanlichucvu">
                <i class="bx bx-cog"></i>
                <span class="nav-item">Quản lí chức vụ</span>
            </a>
            <span class="tooltip">Quản lí chức vụ</span>
        </li>
        <li data-permission-id="11"> <!-- Assuming this falls under "Quản lý tài khoản" -->
            <a href="index.php?action=quanliphanquyen">
                <i class="bx bx-cog"></i>
                <span class="nav-item">Quản lí phân quyền</span>
            </a>
            <span class="tooltip">Quản lí phần quyền</span>
        </li>
        <li data-permission-id="7">
            <a href="index.php?action=quanlinhaphang">
                <i class="bx bx-cog"></i>
                <span class="nav-item">Quản lí nhập hàng</span>
            </a>
            <span class="tooltip">Quản lí nhập hàng</span>
        </li>
        <li> <!-- Logout doesn't need a permission ID -->
            <a href="login signup/logout.php" id="logout">
                <i class="bx bx-log-out"></i>
                <span class="nav-item">Logout</span>
            </a>
            <span class="tooltip">Logout</span>
        </li>
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarBtn = document.querySelector("#sidebar-menu-btn");
    const sidebar = document.querySelector(".sidebar");

    // Sidebar toggle functionality
    sidebarBtn.addEventListener("click", () => {
        sidebar.classList.toggle("active");
    });

    function toggleActiveClass() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.add("active");
        } else {
            sidebar.classList.remove("active");
        }
    }

    window.onload = toggleActiveClass;
    window.onresize = toggleActiveClass;

    // Fetch user permissions and filter sidebar
    fetch('../../BackEnd/Model/quanlitaikhoan/fetch_role_permission.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const userPermissions = data.permissions;
                const menuItems = document.querySelectorAll('.sidebar ul li');

                menuItems.forEach(item => {
                    const permissionId = item.getAttribute('data-permission-id');
                    // Show item if it has no permission ID (e.g., Logout) or if user has the permission
                    if (!permissionId || userPermissions.includes(parseInt(permissionId))) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            } else {
                console.error('Error fetching permissions:', data.message);
            }
        })
        .catch(error => console.error('Fetch error:', error));
});
</script>