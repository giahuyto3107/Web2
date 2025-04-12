    <div class="home-content2">
        <?php
            include ('../../BackEnd/Config/config.php');
            include ('../../BackEnd/Model/quanlitaikhoan/module_permission.php');

            $tam = isset($_GET['action']) ? $_GET['action'] : '';
            $query = isset($_GET['query']) ? $_GET['query'] : '';

            // Điều hướng các module dựa trên action và query
            if ($tam == 'home') {
                include 'home.php';
            }

            if ($tam == 'quanliloaisp' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanliloaisp');
                include 'quanliloaisp/themloaisp.php';
                include 'quanliloaisp/lietkeloaisp.php';
            } elseif ($tam == 'quanliloaisp' && $query == 'sua') {
                // Check permission before including the module
                checkModuleAccess('quanliloaisp');
                include 'quanliloaisp/sualoaisp.php';
            } 

            elseif ($tam == 'quanlibinhluan' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanlibinhluan');
                include "quanlibinhluan/lietkebinhluan.php";
            } elseif ($tam == 'quanlibinhluan' && $query == 'sua') {
                // Check permission before including the module
                checkModuleAccess('quanlibinhluan');
                include "quanlibinhluan/traloibinhluan.php";
            } elseif ($tam == 'quanlibinhluan' && $query == 'xemchitiet') {
                // Check permission before including the module
                checkModuleAccess('quanlibinhluan');
                include "quanlibinhluan/chitietbinhluan.php";
            }
            elseif ($tam == 'quanlidonhang' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanlidonhang');
                include "quanlidonhang/danhsachdonhang.php";
            } elseif ($tam == 'quanlidonhang' && $query == 'xemdonhang') {
                // Check permission before including the module
                checkModuleAccess('quanlidonhang');
                include "quanlidonhang/chitietdonhang.php";
            }
            elseif ($tam == 'quanliphanquyen' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanliphanquyen');
                include "quanliphanquyen/themphanquyen.php";
                include "quanliphanquyen/lietkephanquyen.php";
            } elseif ($tam == 'quanliphanquyen' && $query == 'sua') {
                // Check permission before including the module
                checkModuleAccess('quanliphanquyen');
                include "quanliphanquyen/suaphanquyen.php";
            }
            elseif ($tam == 'quanlichucvu' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanlichucvu');
                include "quanlichucvu/themchucvu.php";
                include "quanlichucvu/lietkechucvu.php";
            } elseif ($tam == 'quanlichucvu' && $query == 'sua') {
                // Check permission before including the module
                checkModuleAccess('quanlichucvu');
                include "quanlichucvu/suachucvu.php";
            } elseif ($tam == 'quanlichucvu' && $query == 'menu') {
                // Check permission before including the module
                checkModuleAccess('quanlichucvu');
                include "quanlichucvu/menuphanquyen.php";
            }
            elseif ($tam === 'quanlisanpham') {
                // Check permission before including the module
                checkModuleAccess('quanlisanpham');
                // include "quanlisanpham/themsanpham.php";
                // include "quanlisanpham/lietkesanpham.php";
                include "quanlisanpham/lietkesanphamtest.php";
            } 
            elseif ($tam == 'quanlisanpham' && $query == 'sua') {
                // Check permission before including the module
                checkModuleAccess('quanlisanpham');
                include "quanlisanpham/suasanpham.php";
            }
            elseif ($tam == 'quanliphieunhap' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanliphieunhap');
                include "quanliphieunhap/themphieunhap.php";
                include "quanliphieunhap/lietkephieunhap.php";
            }elseif ($tam == 'quanliphieunhap' && $query == 'xemphieunhap') {
                // Check permission before including the module
                checkModuleAccess('quanliphieunhap');
                include "quanliphieunhap/XemChiTietDon.php";
            }
            elseif ($tam == 'quanlitaikhoan' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanlitaikhoan');
                include "quanlitaikhoan/themtaikhoan.php";
                include "quanlitaikhoan/lietketaikhoan.php";
            } elseif ($tam == 'quanlitaikhoan' && $query == 'sua') {
                // Check permission before including the module
                checkModuleAccess('quanlitaikhoan');
                include "quanlitaikhoan/suataikhoan.php";
            }
            elseif ($tam == 'quanlydonhang' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanlidonhang');
                include "modules/quanlidonhang/lietke.php";
            }
            
            elseif ($tam == 'quanlinhacungcap' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('quanlinhacungcap');
                include "quanlinhacungcap/themnhacungcap.php";
                include "quanlinhacungcap/lietkenhacungcap.php";
            } elseif ($tam == 'quanlinhacungcap' && $query == 'sua') {
                // Check permission before including the module
                checkModuleAccess('quanlinhacungcap');
                include "quanlinhacungcap/suanhacungcap.php";
            }

            elseif($tam == 'nhaphang' && $query == 'them') {
                // Check permission before including the module
                checkModuleAccess('nhaphang');
                include "nhaphang/menunhaphang.php";
            }
        ?>
    </div>

    <!-- Include permission scripts -->
    <script src="js/permissions.js"></script>
    <script src="js/module_permission.js"></script>
    <script src="js/init-permissions.js"></script>

