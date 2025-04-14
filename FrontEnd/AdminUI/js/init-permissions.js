/**
 * Initialize both permission systems
 * This file ensures that both the module/action permission system and the permission ID system are initialized
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing permission systems...');
    
    // Check if the module/action permission system is available
    if (typeof window.initModulePermissionSystem === 'function') {
        console.log('Module/action permission system found, initializing...');
        window.initModulePermissionSystem();
    } else {
        console.warn('Module/action permission system not found');
    }
    
    // Check if the permission ID system is available
    if (typeof window.initPermissionSystem === 'function') {
        console.log('Permission ID system found, initializing...');
        window.initPermissionSystem();
    } else {
        console.warn('Permission ID system not found');
    }
    
    // Apply permission-based UI adjustments
    setTimeout(function() {
        // Hide elements based on permissions
        const permissionElements = document.querySelectorAll('[data-permission]');
        permissionElements.forEach(function(element) {
            const permission = element.getAttribute('data-permission');
            if (typeof window.hasPermission === 'function' && !window.hasPermission(permission)) {
                element.style.display = 'none';
            }
        });
        
        // Disable buttons based on permissions
        const permissionButtons = document.querySelectorAll('button[data-permission]');
        permissionButtons.forEach(function(button) {
            const permission = button.getAttribute('data-permission');
            if (typeof window.hasPermission === 'function' && !window.hasPermission(permission)) {
                button.disabled = true;
            }
        });
        
        // Hide elements based on permission IDs
        const permissionIdElements = document.querySelectorAll('[data-permission-id]');
        permissionIdElements.forEach(function(element) {
            const permissionId = parseInt(element.getAttribute('data-permission-id'));
            if (typeof window.hasPermission === 'function' && !window.hasPermission(permissionId)) {
                element.style.display = 'none';
            } else if (typeof window.getPermissionAction === 'function') {
                // If the element is visible, add the action as a data attribute
                const action = window.getPermissionAction(permissionId);
                if (action) {
                    element.setAttribute('data-permission-action', action);
                }
            }
        });
        
        // Set a flag on the body element to indicate that permissions have been loaded
        document.body.setAttribute('data-permissions-loaded', 'true');
    }, 500);
}); 