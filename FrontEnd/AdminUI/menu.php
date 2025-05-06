<head>
    <link rel="stylesheet" href="css/sidebar.css" />
</head>
<?php
include '../../BackEnd/Config/config.php';

// Kiểm tra xem $conn có phải là đối tượng mysqli không
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Lỗi: Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra file config.php hoặc đường dẫn include.");
}

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $conn->connect_error);
}

// Lấy thông tin người dùng từ session
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$userEmail = isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'No email';
$fullName = 'Guest'; // Giá trị mặc định
$profilePicture = '/Web2/BackEnd/Uploads/Profile Picture/default.jpg'; // Ảnh mặc định

// Lấy full_name và profile_picture từ bảng user
if ($userId > 0) {
    $sql = "SELECT u.full_name, u.profile_picture 
            FROM user u 
            WHERE u.account_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $fullName = !empty($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Guest';
        if (!empty($user['profile_picture'])) {
            $profilePicture = "/Web2/BackEnd/Uploads/Profile Picture/" . htmlspecialchars($user['profile_picture']);
        }
    } else {
        error_log("Không tìm thấy user với account_id: $userId");
    }
    $stmt->close();
}

$conn->close();
?>

<div class="sidebar">
    <div class="top">
        <div class="logo">
            <span>Góc Sách Nhỏ</span>
        </div>
        <i class="bx bx-menu" id="sidebar-menu-btn"></i>
    </div>
    <div class="user">
        <img src="<?php echo $profilePicture; ?>" alt="User Avatar" class="user-img" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
        <div class="user-info">
            <p class="bold" id="sidebar-username"><?php echo $fullName; ?></p>
            <p id="sidebar-user-email"><?php echo $userEmail; ?></p>
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
        <li data-permission-id="12">
            <a href="index.php?action=quanlichungloai">
                <i class="bx bx-cog"></i>
                <span class="nav-item">Quản lí chủng loại</span>
            </a>
            <span class="tooltip">Quản lí chủng loại</span>
        </li>
        <li data-permission-id="8">
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
        <li data-permission-id="10">
            <a href="index.php?action=quanlichucvu">
                <i class="bx bx-cog"></i>
                <span class="nav-item">Quản lí chức vụ</span>
            </a>
            <span class="tooltip">Quản lí chức vụ</span>
        </li>
        <li data-permission-id="11">
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
        <li>
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
    const menuItems = document.querySelectorAll(".sidebar ul li");

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

    // Highlight active menu item on click
    menuItems.forEach(item => {
        const link = item.querySelector('a');
        link.addEventListener('click', (e) => {
            // Remove 'active' class from all menu items
            menuItems.forEach(i => i.classList.remove('active'));
            // Add 'active' class to the clicked menu item
            item.classList.add('active');
        });
    });

    // Set active menu item based on current URL
    const currentAction = new URLSearchParams(window.location.search).get('action');
    menuItems.forEach(item => {
        const link = item.querySelector('a');
        const href = link.getAttribute('href');
        const actionMatch = href.match(/action=([^&]+)/);
        if (actionMatch && actionMatch[1] === currentAction) {
            item.classList.add('active');
        } else if (href.includes('logout') && !currentAction) {
            // Do not auto-highlight logout
            item.classList.remove('active');
        }
    });
});
</script>