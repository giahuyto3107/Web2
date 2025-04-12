<?php
/**
 * Permission Actions Demo
 * This page demonstrates how to use the updated permission system with actions
 */

// Define ADMIN_ACCESS to allow access to included files
define('ADMIN_ACCESS', true);

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login signup/login.php');
    exit;
}

// Include the permission script
include_once 'includes/permission-script.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission Actions Demo</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .action-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
            background-color: #4CAF50;
            color: white;
        }
        .action-badge.view {
            background-color: #2196F3;
        }
        .action-badge.edit {
            background-color: #FF9800;
        }
        .action-badge.delete {
            background-color: #F44336;
        }
        .action-badge.order {
            background-color: #9C27B0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Permission Actions Demo</h1>
        <p>This page demonstrates how to use the updated permission system with actions.</p>
        
        <div class="card">
            <h2>Module Access with Actions</h2>
            <p>Each module below shows the action associated with the permission:</p>
            
            <div class="module-list">
                <div class="module-item" data-permission-id="1">
                    <h3>Quản lý sản phẩm <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage products.</p>
                </div>
                
                <div class="module-item" data-permission-id="2">
                    <h3>Quản lý đơn hàng <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage orders.</p>
                </div>
                
                <div class="module-item" data-permission-id="3">
                    <h3>Quản lý nhà cung cấp <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage suppliers.</p>
                </div>
                
                <div class="module-item" data-permission-id="4">
                    <h3>Quản lý tài khoản <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage accounts.</p>
                </div>
                
                <div class="module-item" data-permission-id="5">
                    <h3>Quản lý bình luận <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage comments.</p>
                </div>
                
                <div class="module-item" data-permission-id="6">
                    <h3>Thống kê <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to view statistics.</p>
                </div>
                
                <div class="module-item" data-permission-id="7">
                    <h3>Quản lý nhà cung cấp <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage suppliers.</p>
                </div>
                
                <div class="module-item" data-permission-id="8">
                    <h3>Đặt hàng <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to place orders.</p>
                </div>
                
                <div class="module-item" data-permission-id="9">
                    <h3>Quản lý loại sản phẩm <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage product types.</p>
                </div>
                
                <div class="module-item" data-permission-id="10">
                    <h3>Quản lý phiếu nhập <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage import vouchers.</p>
                </div>
                
                <div class="module-item" data-permission-id="11">
                    <h3>Quản lý chức vụ <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage positions.</p>
                </div>
                
                <div class="module-item" data-permission-id="12">
                    <h3>Quản lý phân quyền <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage permissions.</p>
                </div>
                
                <div class="module-item" data-permission-id="13">
                    <h3>Quản lý chủng loại <span class="action-badge">Loading...</span></h3>
                    <p>This module allows users to manage categories.</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Action-Based UI Controls</h2>
            <p>This section demonstrates how to use the action information to control UI elements:</p>
            
            <div class="action-demo">
                <h3>Product Management</h3>
                <div class="action-controls">
                    <button class="btn btn-primary" data-permission-id="1" data-action="Xem">View Products</button>
                    <button class="btn btn-warning" data-permission-id="1" data-action="Sửa">Edit Products</button>
                    <button class="btn btn-danger" data-permission-id="1" data-action="Xóa">Delete Products</button>
                </div>
                
                <h3>Order Management</h3>
                <div class="action-controls">
                    <button class="btn btn-primary" data-permission-id="2" data-action="Xem">View Orders</button>
                    <button class="btn btn-warning" data-permission-id="2" data-action="Sửa">Edit Orders</button>
                    <button class="btn btn-danger" data-permission-id="2" data-action="Xóa">Delete Orders</button>
                </div>
                
                <h3>Supplier Management</h3>
                <div class="action-controls">
                    <button class="btn btn-primary" data-permission-id="3" data-action="Xem">View Suppliers</button>
                    <button class="btn btn-warning" data-permission-id="3" data-action="Sửa">Edit Suppliers</button>
                    <button class="btn btn-danger" data-permission-id="3" data-action="Xóa">Delete Suppliers</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Include both permission scripts -->
    <script src="js/permissions.js"></script>
    <script src="js/module_permission.js"></script>
    <script src="js/init-permissions.js"></script>
    
    <script>
        // Update the action badges and control buttons based on permissions
        document.addEventListener('DOMContentLoaded', function() {
            // Function to update the action badges
            function updateActionBadges() {
                const actionBadges = document.querySelectorAll('.action-badge');
                actionBadges.forEach(function(badge) {
                    const moduleItem = badge.closest('.module-item');
                    const permissionId = moduleItem.getAttribute('data-permission-id');
                    
                    if (permissionId && typeof window.getPermissionAction === 'function') {
                        const action = window.getPermissionAction(permissionId);
                        if (action) {
                            badge.textContent = action;
                            
                            // Add a class based on the action
                            badge.classList.remove('view', 'edit', 'delete', 'order');
                            if (action === 'Xem') {
                                badge.classList.add('view');
                            } else if (action === 'Sửa') {
                                badge.classList.add('edit');
                            } else if (action === 'Xóa') {
                                badge.classList.add('delete');
                            } else if (action === 'Đặt hàng') {
                                badge.classList.add('order');
                            }
                        } else {
                            badge.textContent = 'None';
                        }
                    }
                });
            }
            
            // Function to update the action controls
            function updateActionControls() {
                const actionButtons = document.querySelectorAll('.action-controls button');
                actionButtons.forEach(function(button) {
                    const permissionId = button.getAttribute('data-permission-id');
                    const requiredAction = button.getAttribute('data-action');
                    
                    if (permissionId && typeof window.getPermissionAction === 'function') {
                        const action = window.getPermissionAction(permissionId);
                        
                        // If the user doesn't have the permission or the action doesn't match, disable the button
                        if (!action || action !== requiredAction) {
                            button.disabled = true;
                            button.classList.add('disabled');
                        } else {
                            button.disabled = false;
                            button.classList.remove('disabled');
                        }
                    }
                });
            }
            
            // Update the UI immediately
            updateActionBadges();
            updateActionControls();
            
            // Set up a timeout to check again after a short delay
            setTimeout(function() {
                updateActionBadges();
                updateActionControls();
            }, 1000);
            
            // Set up another timeout to check again after a longer delay
            setTimeout(function() {
                updateActionBadges();
                updateActionControls();
            }, 3000);
        });
    </script>
</body>
</html> 