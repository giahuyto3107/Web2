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

    <!-- Initialize PermissionSystem namespace -->
    <script>
        console.log('Initializing PermissionSystem namespace...');
        
        // Create the global namespace immediately
        window.PermissionSystem = {
            hasActionPermission: null,
            isPermissionLoaded: () => false,
            moduleLoaded: false,
            ready: new Promise((resolve) => {
                window._resolvePermissionSystem = resolve;
            })
        };
        
        // Debug global object
        console.log('PermissionSystem initialized:', window.PermissionSystem);
    </script>

    <!-- Load permission scripts -->
    <script>
        // Debug script paths
        const scriptPaths = {
            modulePermission: '<?php echo "/Web2/FrontEnd/AdminUI/js/module_permission.js"; ?>',
            actionPermission: '<?php echo "/Web2/FrontEnd/AdminUI/js/action-permission.js"; ?>',
            initPermissions: '<?php echo "/Web2/FrontEnd/AdminUI/js/init-permissions.js"; ?>'
        };
        
        console.log('Script paths:', scriptPaths);
        
        // Load a script and return a promise
        function loadScript(src) {
            return new Promise((resolve, reject) => {
                console.log('Loading script:', src);
                const script = document.createElement('script');
                script.src = src;
                script.async = false; // Maintain loading order
                script.onload = () => {
                    console.log('Successfully loaded:', src);
                    resolve();
                };
                script.onerror = (error) => {
                    console.error('Failed to load:', src, error);
                    reject(new Error(`Failed to load ${src}`));
                };
                document.head.appendChild(script);
            });
        }

        // Load all scripts in sequence
        async function loadAllScripts() {
            try {
                console.log('Starting script loading sequence...');
                
                // Load module_permission.js first
                await loadScript(scriptPaths.modulePermission);
                console.log('module_permission.js loaded, PermissionSystem:', window.PermissionSystem);
                
                // Load action-permission.js
                await loadScript(scriptPaths.actionPermission);
                console.log('action-permission.js loaded');
                
                // Load init-permissions.js
                await loadScript(scriptPaths.initPermissions);
                console.log('init-permissions.js loaded');
                
                console.log('All scripts loaded successfully');
            } catch (error) {
                console.error('Error in script loading sequence:', error);
            }
        }

        // Start loading when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadAllScripts);
        } else {
            loadAllScripts();
        }
    </script>
</body>
</html>

