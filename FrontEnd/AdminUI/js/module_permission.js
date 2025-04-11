/**
 * Module Permission System - Client Side
 * This file handles permission checking for modules on the client side
 */

// Cache for permissions to avoid multiple API calls
let userPermissions = null;
let permissionActions = null;
let permissionsLoaded = false;
let permissionsPromise = null;

// Default permissions for testing/fallback
const defaultPermissions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
const defaultActions = {
    1: 'Xem',
    2: 'Xem',
    3: 'Xem',
    4: 'Xem',
    5: 'Xem',
    6: 'Xem',
    7: 'Đặt hàng',
    8: 'Xem',
    9: 'Xem',
    10: 'Xem',
    11: 'Xem',
    12: 'Xem'
};

/**
 * Initialize the module permission system
 * Fetches user permissions and sets up event listeners
 */
function initModulePermissionSystem() {
    // Fetch user permissions
    fetchUserPermissions().then(() => {
        // Apply permission-based UI adjustments
        applyPermissionBasedUI();
    }).catch(error => {
        console.error('Error initializing permission system:', error);
        // Use default permissions as fallback
        userPermissions = defaultPermissions;
        permissionActions = defaultActions;
        permissionsLoaded = true;
        applyPermissionBasedUI();
    });
}

/**
 * Fetch user permissions from the server
 * @returns {Promise} Promise that resolves when permissions are fetched
 */
function fetchUserPermissions() {
    // If permissions are already being fetched, return the existing promise
    if (permissionsPromise) {
        return permissionsPromise;
    }
    
    // Create a new promise to fetch permissions
    permissionsPromise = fetch('../../BackEnd/Model/quanlitaikhoan/fetch_role_permission.php')
        .then(response => {
            // Check if the response is OK
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            // Check the content type to see if it's JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.warn('Response is not JSON, using default permissions');
                // Return a mock response with default permissions
                return {
                    success: true,
                    permissions: defaultPermissions,
                    permissionActions: defaultActions
                };
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                userPermissions = data.permissions;
                permissionActions = data.permissionActions || {};
                permissionsLoaded = true;
                console.log('User permissions loaded:', userPermissions);
                console.log('Permission actions:', permissionActions);
                return userPermissions;
            } else {
                console.error('Failed to load permissions:', data.message);
                userPermissions = defaultPermissions;
                permissionActions = defaultActions;
                permissionsLoaded = true;
                return userPermissions;
            }
        })
        .catch(error => {
            console.error('Error fetching permissions:', error);
            // Use default permissions as fallback
            userPermissions = defaultPermissions;
            permissionActions = defaultActions;
            permissionsLoaded = true;
            return userPermissions;
        });
    
    return permissionsPromise;
}

/**
 * Check if the current user has a specific permission
 * @param {number} permissionId - The ID of the permission to check
 * @returns {boolean} Whether the user has the permission
 */
function hasPermission(permissionId) {
    // If permissions are not loaded yet, try to load them
    if (!permissionsLoaded) {
        console.warn('Permissions not loaded yet, attempting to load them now');
        fetchUserPermissions();
        return false; // Return false until permissions are loaded
    }
    
    // If permissions are loaded but userPermissions is null, return false
    if (!userPermissions) {
        console.warn('Permissions loaded but userPermissions is null');
        return false;
    }
    
    return userPermissions.includes(permissionId);
}

/**
 * Get the action associated with a specific permission
 * @param {number} permissionId - The ID of the permission to check
 * @returns {string|null} The action associated with the permission, or null if not found
 */
function getPermissionAction(permissionId) {
    // If permissions are not loaded yet, try to load them
    if (!permissionsLoaded) {
        console.warn('Permissions not loaded yet, attempting to load them now');
        fetchUserPermissions();
        return null; // Return null until permissions are loaded
    }
    
    // If permissions are loaded but permissionActions is null, return null
    if (!permissionActions) {
        console.warn('Permissions loaded but permissionActions is null');
        return null;
    }
    
    return permissionActions[permissionId] || null;
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
        { permissionId: 3, elements: ['.quanlinhacungcap', '#quanlinhacungcap-link', '.quanliphieunhap', '#quanliphieunhap-link', '.nhaphang', '#nhaphang-link'] }, // Quản lý nhập hàng
        { permissionId: 4, elements: ['.quanlitaikhoan', '#quanlitaikhoan-link'] }, // Quản lý tài khoản
        { permissionId: 5, elements: ['.quanlibinhluan', '#quanlibinhluan-link'] }, // Quản lý đánh giá
        { permissionId: 6, elements: ['.thongke', '#thongke-link'] }, // Xem thống kê
        { permissionId: 7, elements: ['.quanlinhacungcap', '#quanlinhacungcap-link'] }, // Quản lý nhà cung cấp
        { permissionId: 8, elements: ['.dathang', '#dathang-link', '.quanliloaisp', '#quanliloaisp-link'] }, // Đặt hàng & Quản lý thể loại
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
        } else {
            // If the user has the permission, check the action
            const action = getPermissionAction(mapping.permissionId);
            if (action) {
                // Add a data attribute to the element with the action
                mapping.elements.forEach(selector => {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(element => {
                        element.setAttribute('data-permission-action', action);
                    });
                });
            }
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
    // If permissions are not loaded yet, try to load them
    if (!permissionsLoaded) {
        console.warn('Permissions not loaded yet, attempting to load them now');
        fetchUserPermissions().then(() => {
            if (hasPermission(permissionId)) {
                callback();
            } else {
                if (noPermissionCallback) {
                    noPermissionCallback();
                } else {
                    alert('Bạn không có quyền thực hiện hành động này.');
                }
            }
        });
        return;
    }
    
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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initModulePermissionSystem); 