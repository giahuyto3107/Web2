<?php
/**
 * Module Permission System
 * This file provides functions to check if a user has permission to access specific modules
 */

// Include database connection
require_once '../Config/config.php';

/**
 * Check if the current user has permission to access a specific module
 * 
 * @param string $moduleName The name of the module to check (e.g., 'quanlisanpham', 'quanlidonhang')
 * @return bool Whether the user has permission to access the module
 */
function hasModulePermission($moduleName) {
    global $conn;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Get the user's role_id
    $sql = "SELECT role_id FROM account WHERE account_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false; // User not found
    }
    
    $user = $result->fetch_assoc();
    $roleId = $user['role_id'];
    
    // Map module names to permission IDs
    $modulePermissionMap = [
        'quanlisanpham' => 1,      // Quản lý sản phẩm
        'quanlidonhang' => 2,      // Quản lý đơn hàng
        'quanlinhacungcap' => 3,   // Quản lý nhập hàng
        'quanlitaikhoan' => 4,     // Quản lý tài khoản
        'quanlibinhluan' => 5,     // Quản lý đánh giá
        'thongke' => 6,            // Xem thống kê
        'quanlinhacungcap' => 7,   // Quản lý nhà cung cấp
        'dathang' => 8,            // Đặt hàng
        'quanliloaisp' => 9,       // Quản lý thể loại
        'quanliphieunhap' => 10,   // Quản lý phiếu nhập
        'quanlichucvu' => 11,      // Quản lý chức vụ
        'quanliphanquyen' => 12,   // Quản lý phân quyền
        'quanlichungloai' => 13,   // Quản lý chủng loại
        'nhaphang' => 3            // Nhập hàng (uses same permission as quanlinhacungcap)
    ];
    
    // Check if the module exists in our map
    if (!isset($modulePermissionMap[$moduleName])) {
        return false; // Module not found in map
    }
    
    $permissionId = $modulePermissionMap[$moduleName];
    
    // Check if the role has the permission
    $sql = "SELECT COUNT(*) as count FROM role_permission WHERE role_id = ? AND permission_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $roleId, $permissionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

/**
 * Redirect to access denied page if user doesn't have permission
 * 
 * @param string $moduleName The name of the module to check
 * @return void
 */
function checkModuleAccess($moduleName) {
    if (!hasModulePermission($moduleName)) {
        // Redirect to access denied page
        header("Location: ../../FrontEnd/AdminUI/access_denied.php");
        exit;
    }
}
?> 