/**
 * Action Permission System
 * This file contains functions for checking and applying action-based permissions
 */

// Action Permission System
let actionPermissionsLoaded = false;

/**
 * Initialize the action permission system
 */
async function initActionPermissionSystem() {
    console.log('Action permission system starting initialization...');
    
    try {
        // Wait for PermissionSystem to be available
        const maxWaitTime = 5000; // 5 seconds
        const startTime = Date.now();
        
        while (!window.PermissionSystem?.moduleLoaded && (Date.now() - startTime < maxWaitTime)) {
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        
        if (!window.PermissionSystem?.moduleLoaded) {
            throw new Error('PermissionSystem not found or not loaded after waiting');
        }
        
        // Wait for the permission system to be ready
        await window.PermissionSystem.ready;
        
        console.log('Permission system ready, initializing action buttons...');
        actionPermissionsLoaded = true;
        initializeActionButtons();
        
    } catch (error) {
        console.error('Failed to initialize action permission system:', error);
        document.dispatchEvent(new CustomEvent('actionPermissionError', { detail: error }));
    }
}

/**
 * Check if the user has a specific action for a permission
 */
function checkActionPermission(permissionId, action) {
    if (!actionPermissionsLoaded || !window.PermissionSystem?.hasActionPermission) {
        console.warn('Action permission system not ready');
        return false;
    }
    
    return window.PermissionSystem.hasActionPermission(permissionId, action);
}

/**
 * Apply action-based permissions to UI elements
 */
function initializeActionButtons() {
    console.log('Initializing action buttons...');
    
    // Find all buttons with permission attributes
    document.querySelectorAll('[data-permission-id][data-action]').forEach(element => {
        const permissionId = element.getAttribute('data-permission-id');
        const action = element.getAttribute('data-action');
        
        console.log('Processing element:', {
            element,
            permissionId,
            action,
            isButton: element.tagName === 'BUTTON'
        });
        
        // Update element state based on permission
        if (!checkActionPermission(permissionId, action)) {
            if (element.tagName === 'BUTTON' || element.tagName === 'INPUT') {
                element.disabled = true;
                element.style.opacity = '0.5';
            } else {
                element.style.display = 'none';
            }
        } else {
            if (element.tagName === 'BUTTON' || element.tagName === 'INPUT') {
                element.disabled = false;
                element.style.opacity = '1';
            } else {
                element.style.display = '';
            }
        }
        
        // Add click handler for interactive elements
        if (element.tagName === 'BUTTON' || element.tagName === 'INPUT' || element.tagName === 'A') {
            element.addEventListener('click', function(e) {
                if (!checkActionPermission(permissionId, action)) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Bạn không có quyền thực hiện hành động này');
                    return false;
                }
            });
        }
    });
}

/**
 * Check if the user has permission to perform an action before executing a callback
 */
function checkActionPermissionBeforeAction(permissionId, action, callback) {
    console.log('Checking action permission before action:', { permissionId, action });
    
    if (!actionPermissionsLoaded) {
        console.warn('Permissions not loaded yet, waiting...');
        document.addEventListener('permissionsLoaded', function() {
            if (checkActionPermission(permissionId, action)) {
                callback();
            } else {
                alert('Bạn không có quyền thực hiện hành động này');
            }
        });
        return;
    }
    
    if (checkActionPermission(permissionId, action)) {
        callback();
    } else {
        alert('Bạn không có quyền thực hiện hành động này');
    }
}

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initActionPermissionSystem);
} else {
    initActionPermissionSystem();
}