/**
 * Permission Management System
 * This file handles permission checking and UI adjustments based on user permissions
 */

// Cache for permissions to avoid multiple API calls
let userPermissions = null;

/**
 * Initialize the permission system
 * Fetches user permissions and sets up event listeners
 */
function initPermissionSystem() {
    // Fetch user permissions
    fetchUserPermissions().then(() => {
        // Apply permission-based UI adjustments
        applyPermissionBasedUI();
    });
}

/**
 * Fetch user permissions from the server
 * @returns {Promise} Promise that resolves when permissions are fetched
 */
function fetchUserPermissions() {
    return fetch('../../BackEnd/Model/quanlitaikhoan/check_permission.php?action=get_permissions')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userPermissions = data.permissions;
                console.log('User permissions loaded:', userPermissions);
                return userPermissions;
            } else {
                console.error('Failed to load permissions:', data.message);
                return [];
            }
        })
        .catch(error => {
            console.error('Error fetching permissions:', error);
            return [];
        });
}

/**
 * Check if the current user has a specific permission
 * @param {number} permissionId - The ID of the permission to check
 * @returns {boolean} Whether the user has the permission
 */
function hasPermission(permissionId) {
    if (!userPermissions) {
        console.warn('Permissions not loaded yet');
        return false;
    }
    
    return userPermissions.some(permission => permission.permission_id == permissionId);
}

/**
 * Apply permission-based UI adjustments
 * Hides elements that the user doesn't have permission to access
 */
function applyPermissionBasedUI() {
    // Define permission mappings for UI elements
    const permissionMappings = [
        { permissionId: 1, elements: ['.quanlisanpham', '#quanlisanpham-link'] }, // Quản lý sản phẩm
        { permissionId: 2, elements: ['.quanlidonhang', '#quanlidonhang-link'] }, // Quản lý đơn hàng
        { permissionId: 3, elements: ['.quanlinhacungcap', '#quanlinhacungcap-link', '.quanliphieunhap', '#quanliphieunhap-link'] }, // Quản lý nhập hàng
        { permissionId: 4, elements: ['.quanlitaikhoan', '#quanlitaikhoan-link'] }, // Quản lý tài khoản
        { permissionId: 5, elements: ['.quanlibinhluan', '#quanlibinhluan-link'] }, // Quản lý đánh giá
        { permissionId: 6, elements: ['.thongke', '#thongke-link'] }, // Xem thống kê
        { permissionId: 7, elements: ['.quanlinhacungcap', '#quanlinhacungcap-link'] }, // Quản lý nhà cung cấp
        { permissionId: 8, elements: ['.dathang', '#dathang-link'] }, // Đặt hàng
        { permissionId: 9, elements: ['.quanliloaisp', '#quanliloaisp-link'] }, // Quản lý thể loại
        { permissionId: 10, elements: ['.quanliphieunhap', '#quanliphieunhap-link'] }, // Quản lý phiếu nhập
        { permissionId: 11, elements: ['.quanlichucvu', '#quanlichucvu-link'] }, // Quản lý chức vụ
        { permissionId: 12, elements: ['.quanliphanquyen', '#quanliphanquyen-link'] }, // Quản lý phân quyền
        { permissionId: 13, elements: ['.quanlichungloai', '#quanlichungloai-link'] } // Quản lý chủng loại
    ];
    
    // Hide elements based on permissions
    permissionMappings.forEach(mapping => {
        if (!hasPermission(mapping.permissionId)) {
            mapping.elements.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(element => {
                    element.style.display = 'none';
                });
            });
        }
    });
    
    // Check if any menu items are visible
    const menuItems = document.querySelectorAll('.nav-item');
    let hasVisibleItems = false;
    
    menuItems.forEach(item => {
        if (item.style.display !== 'none') {
            hasVisibleItems = true;
        }
    });
    
    // If no menu items are visible, show a message
    if (!hasVisibleItems) {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            const noAccessMessage = document.createElement('div');
            noAccessMessage.className = 'no-access-message';
            noAccessMessage.innerHTML = '<p>Bạn không có quyền truy cập vào bất kỳ tính năng nào.</p>';
            sidebar.appendChild(noAccessMessage);
        }
    }
}

/**
 * Check permission before performing an action
 * @param {number} permissionId - The ID of the permission to check
 * @param {Function} callback - Function to call if the user has permission
 * @param {Function} [noPermissionCallback] - Function to call if the user doesn't have permission
 */
function checkPermissionBeforeAction(permissionId, callback, noPermissionCallback) {
    if (hasPermission(permissionId)) {
        callback();
    } else {
        if (noPermissionCallback) {
            noPermissionCallback();
        } else {
            alert('Bạn không có quyền thực hiện hành động này.');
        }
    }
}

// Initialize the permission system when the DOM is loaded
document.addEventListener('DOMContentLoaded', initPermissionSystem); 