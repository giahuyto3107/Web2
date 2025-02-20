<section class="home-section">
    <div class="home-content">
        <i class='bx bx-menu'></i>
        <span class="text">DANH MỤC QUẢN LÍ</span>
    </div>
    <div class="home-content2">
        <?php
        include ('../../BackEnd/Config/config.php');

        // Kiểm tra các tham số action và query từ URL
        $tam = isset($_GET['action']) ? $_GET['action'] : '';
        $query = isset($_GET['query']) ? $_GET['query'] : '';

        // Điều hướng các module dựa trên action và query
        if ($tam == 'quanliloaisp' && $query == 'them') {
            include 'quanliloaisp/themloaisp.php';
            include 'quanliloaisp/lietkeloaisp.php';
        } elseif ($tam == 'quanliloaisp' && $query == 'sua') {
            include 'quanliloaisp/sualoaisp.php';
        } elseif ($tam == 'quanlibinhluan' && $query == 'them') {
            include "quanlibinhluan/thembinhluan.php";
            include "quanlibinhluan/lietkebinhluan.php";
        } elseif ($tam == 'quanlibinhluan' && $query == 'sua') {
            include "quanlibinhluan/suabinhluan.php";
        } elseif ($tam == 'quanlidonhang' && $query == 'them') {
            include "quanlidonhang/danhsachdonhang.php";
        } elseif ($tam == 'quanlidonhang' && $query == 'xemdonhang') {
            include "quanlidonhang/chitietdonhang.php";
        } elseif ($tam == 'quanliphanquyen' && $query == 'them') {
            include "quanliphanquyen/themphanquyen.php";
            include "quanliphanquyen/lietkephanquyen.php";
        } elseif ($tam == 'quanliphanquyen' && $query == 'sua') {
            include "quanliphanquyen/suaphanquyen.php";
        } elseif ($tam == 'quanlisanpham' && $query == 'them') {
            include "quanlisanpham/themsanpham.php";
            include "quanlisanpham/lietkesanpham.php";
        } elseif ($tam == 'quanlisanpham' && $query == 'sua') {
            include "quanlisanpham/suasanpham.php";
        } elseif ($tam == 'quanliphieunhap' && $query == 'them') {
            include "quanliphieunhap/themphieunhap.php";
            include "quanliphieunhap/lietkephieunhap.php";
        }elseif ($tam == 'quanliphieunhap' && $query == 'xemphieunhap') {
            include "quanliphieunhap/XemChiTietDon.php";
        } elseif ($tam == 'quanlitaikhoan' && $query == 'them') {
            include "quanlitaikhoan/themtaikhoan.php";
            include "quanlitaikhoan/lietketaikhoan.php";
        } elseif ($tam == 'quanlitaikhoan' && $query == 'sua') {
            include "quanlitaikhoan/suataikhoan.php";
        } elseif ($tam == 'quanlydonhang' && $query == 'them') {
            include "modules/quanlidonhang/lietke.php";
        } elseif ($tam == 'quanlinhacungcap' && $query == 'them') {
            include "quanlinhacungcap/themnhacungcap.php";
            include "quanlinhacungcap/lietkenhacungcap.php";
        } elseif ($tam == 'quanlinhacungcap' && $query == 'sua') {
            include "quanlinhacungcap/suanhacungcap.php";
        }
        ?>
    </div>
</section>