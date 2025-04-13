<?php
// Define ADMIN_ACCESS to allow access to this file
define('ADMIN_ACCESS', true);

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include the permission script
include 'includes/permission-script.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Phân Quyền Hành Động</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .demo-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .demo-title {
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .permission-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .permission-info h3 {
            margin-top: 0;
        }
        .permission-list {
            list-style-type: none;
            padding: 0;
        }
        .permission-list li {
            margin-bottom: 5px;
        }
        .permission-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-right: 5px;
        }
        .permission-badge.view {
            background-color: #28a745;
            color: white;
        }
        .permission-badge.edit {
            background-color: #ffc107;
            color: black;
        }
        .permission-badge.delete {
            background-color: #dc3545;
            color: white;
        }
        .permission-badge.order {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Demo Phân Quyền Hành Động</h1>
        
        <div class="demo-section">
            <div class="demo-title">Quản lý Sản phẩm</div>
            <div class="button-group">
                <button class="btn btn-primary" data-permission-id="1" data-action="Xem">
                    <i class="fas fa-eye"></i> Xem Sản phẩm
                </button>
                <button class="btn btn-warning" data-permission-id="1" data-action="Sửa">
                    <i class="fas fa-edit"></i> Sửa Sản phẩm
                </button>
                <button class="btn btn-danger" data-permission-id="1" data-action="Xóa">
                    <i class="fas fa-trash"></i> Xóa Sản phẩm
                </button>
            </div>
            <div class="permission-info">
                <h3>Thông tin quyền:</h3>
                <p>Quyền ID: 1 - Quản lý sản phẩm</p>
                <p>Hành động hiện tại: <span id="product-action">Đang tải...</span></p>
            </div>
        </div>
        
        <div class="demo-section">
            <div class="demo-title">Quản lý Đơn hàng</div>
            <div class="button-group">
                <button class="btn btn-primary" data-permission-id="2" data-action="Xem">
                    <i class="fas fa-eye"></i> Xem Đơn hàng
                </button>
                <button class="btn btn-warning" data-permission-id="2" data-action="Sửa">
                    <i class="fas fa-edit"></i> Sửa Đơn hàng
                </button>
                <button class="btn btn-danger" data-permission-id="2" data-action="Xóa">
                    <i class="fas fa-trash"></i> Xóa Đơn hàng
                </button>
            </div>
            <div class="permission-info">
                <h3>Thông tin quyền:</h3>
                <p>Quyền ID: 2 - Quản lý đơn hàng</p>
                <p>Hành động hiện tại: <span id="order-action">Đang tải...</span></p>
            </div>
        </div>
        
        <div class="demo-section">
            <div class="demo-title">Quản lý Nhà cung cấp</div>
            <div class="button-group">
                <button class="btn btn-primary" data-permission-id="7" data-action="Xem">
                    <i class="fas fa-eye"></i> Xem Nhà cung cấp
                </button>
                <button class="btn btn-warning" data-permission-id="7" data-action="Sửa">
                    <i class="fas fa-edit"></i> Sửa Nhà cung cấp
                </button>
                <button class="btn btn-danger" data-permission-id="7" data-action="Xóa">
                    <i class="fas fa-trash"></i> Xóa Nhà cung cấp
                </button>
                <button class="btn btn-info" data-permission-id="7" data-action="Đặt hàng">
                    <i class="fas fa-shopping-cart"></i> Đặt hàng
                </button>
            </div>
            <div class="permission-info">
                <h3>Thông tin quyền:</h3>
                <p>Quyền ID: 7 - Quản lý nhà cung cấp</p>
                <p>Hành động hiện tại: <span id="supplier-action">Đang tải...</span></p>
            </div>
        </div>
        
        <div class="permission-info">
            <h3>Tất cả quyền của người dùng:</h3>
            <ul class="permission-list" id="user-permissions">
                <li>Đang tải quyền...</li>
            </ul>
        </div>
    </div>

    <script src="js/module_permission.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the permission system
            initModulePermissionSystem();
            
            // Update permission information when permissions are loaded
            const updatePermissionInfo = () => {
                // Update product permission info
                const productAction = getPermissionAction(1);
                document.getElementById('product-action').textContent = productAction || 'Không có quyền';
                
                // Update order permission info
                const orderAction = getPermissionAction(2);
                document.getElementById('order-action').textContent = orderAction || 'Không có quyền';
                
                // Update supplier permission info
                const supplierAction = getPermissionAction(7);
                document.getElementById('supplier-action').textContent = supplierAction || 'Không có quyền';
                
                // Update all user permissions
                const permissionsList = document.getElementById('user-permissions');
                permissionsList.innerHTML = '';
                
                for (let i = 1; i <= 13; i++) {
                    if (hasPermission(i)) {
                        const action = getPermissionAction(i);
                        const li = document.createElement('li');
                        
                        let actionClass = '';
                        if (action === 'Xem') actionClass = 'view';
                        else if (action === 'Sửa') actionClass = 'edit';
                        else if (action === 'Xóa') actionClass = 'delete';
                        else if (action === 'Đặt hàng') actionClass = 'order';
                        
                        li.innerHTML = `
                            <span class="permission-badge ${actionClass}">${action || 'Không xác định'}</span>
                            Quyền ID ${i}: ${getPermissionName(i)}
                        `;
                        
                        permissionsList.appendChild(li);
                    }
                }
                
                if (permissionsList.children.length === 0) {
                    permissionsList.innerHTML = '<li>Không có quyền nào</li>';
                }
            };
            
            // Check if permissions are loaded every 500ms
            const checkPermissionsInterval = setInterval(() => {
                if (permissionsLoaded) {
                    updatePermissionInfo();
                    clearInterval(checkPermissionsInterval);
                }
            }, 500);
            
            // Set up event listeners for buttons
            document.querySelectorAll('button[data-permission-id][data-action]').forEach(button => {
                button.addEventListener('click', function() {
                    const permissionId = parseInt(this.getAttribute('data-permission-id'));
                    const action = this.getAttribute('data-action');
                    
                    checkActionPermissionBeforeAction(
                        permissionId,
                        action,
                        () => {
                            alert(`Bạn có quyền ${action} cho quyền ID ${permissionId}`);
                        },
                        () => {
                            alert(`Bạn không có quyền ${action} cho quyền ID ${permissionId}`);
                        }
                    );
                });
            });
        });
    </script>
</body>
</html> 