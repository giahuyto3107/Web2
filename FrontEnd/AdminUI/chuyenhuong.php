<main class="main-content">
    <?php
    // Lấy tham số action từ URL
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    // Điều hướng nội dung dựa trên action
    if ($action === 'quanlisanpham') {
        include "quanlisanpham/lietkesanpham.php";
    } elseif ($action === 'quanliloaisp') {
        include "quanliloaisp/lietkeloaisp.php";
    } elseif ($action === 'thongke') {
        include "thongke/thongke.php";
    } elseif ($action === 'quanlibinhluan') {
        include "quanlibinhluan/lietkebinhluan.php";
    } elseif ($action === 'quanlidonhang') {
        include "quanlidonhang/danhsachdonhang.php";
    } elseif ($action === 'quanlinhacungcap') {  
        include "quanlinhacungcap/lietkenhacungcap.php";
    } elseif ($action === 'quanlichucvu') {
        include "quanlichucvu/lietkechucvu.php";
    } elseif ($action === 'quanliphieunhap') {
        include "quanliphieunhap/lietkephieunhap.php";
    } elseif ($action === 'quanlitaikhoan') {
        include "quanlitaikhoan/lietketaikhoan.php";
    } elseif ($action === 'quanliphanquyen') {
        include "quanliphanquyen/lietkephanquyen.php";
    } elseif ($action === 'quanlinhaphang') {
        include "nhaphang/menunhaphang.php";
    }elseif ($action === 'quanlichungloai') {
        include "quanlichungloai/lietkechungloai.php";
     } else {
        include "thongke/thongke.php"; // Include thongke.php làm nội dung mặc định
    }
    ?>
</main>