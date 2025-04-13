<?php
/**
 * Action Permission System
 * This file provides functions to check if a user has permission to perform specific actions
 */

// Include database connection
require_once '../Config/config.php';

/**
 * Check if the current user has permission to perform a specific action
 * 
 * @param string $actionName The name of the action to check (e.g., 'Xem', 'Sửa', 'Xóa')
 * @param string $moduleName The name of the module the action belongs to (e.g., 'quanlisanpham', 'quanlidonhang')
 * @return bool Whether the user has permission to perform the action
 */
function hasActionPermission($actionName, $moduleName) {
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
        'quanlitaikhoan' => 3,   // Quản lý nhà cung cấp
        'quanlidanhgia' => 4,     // Quản lý tài khoản
        'thongke' => 5,     // Quản lý đánh giá
        'quanlinhacungcap' => 6,            // Xem thống kê
        'dathang' => 7,   // Quản lý nhà cung cấp
        'quanlitheloai' => 8,            // Đặt hàng
        'quanliphieunhap' => 9,       // Quản lý thể loại
        'quanlichucvu' => 10,   // Quản lý phiếu nhập
        'quanliphanquyen' => 11,      // Quản lý chức vụ
        'quanlichungloai' => 12,   // Quản lý phân quyền
    ];
    
    // Check if the module exists in our map
    if (!isset($modulePermissionMap[$moduleName])) {
        return false; // Module not found in map
    }
    
    $permissionId = $modulePermissionMap[$moduleName];
    
    // Check if the role has the permission with the specified action
    $sql = "SELECT action FROM role_permission WHERE role_id = ? AND permission_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $roleId, $permissionId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false; // User doesn't have the permission at all
    }
    
    $row = $result->fetch_assoc();
    $allowedAction = $row['action'];
    
    // Check if the requested action is allowed
    // For now, we'll assume that if a user has the permission, they can perform the action
    // In a more complex system, you might want to check if the action matches the allowed action
    // For example: if ($allowedAction === $actionName || $allowedAction === 'Tất cả')
    
    return true;
}

/**
 * Check if the current user has permission to perform a specific action and redirect if not
 * 
 * @param string $actionName The name of the action to check
 * @param string $moduleName The name of the module the action belongs to
 * @return void
 */
function checkActionPermission($actionName, $moduleName) {
    if (!hasActionPermission($actionName, $moduleName)) {
        // Redirect to access denied page
        header("Location: ../../FrontEnd/AdminUI/access_denied.php?module=" . urlencode($moduleName) . "&action=" . urlencode($actionName));
        exit;
    }
}

/**
 * Check if the current user has permission to perform a specific action and return a boolean
 * This is useful for AJAX requests or API endpoints
 * 
 * @param string $actionName The name of the action to check
 * @param string $moduleName The name of the module the action belongs to
 * @return bool Whether the user has permission to perform the action
 */
function checkActionPermissionAjax($actionName, $moduleName) {
    if (!hasActionPermission($actionName, $moduleName)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Bạn không có quyền thực hiện hành động này.'
        ]);
        exit;
    }
    
    return true;
}
?> 