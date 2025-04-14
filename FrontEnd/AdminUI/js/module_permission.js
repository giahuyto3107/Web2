/**
 * Module Permission System - Client Side
 * This file handles permission checking for modules on the client side
 */

// Immediately create the global namespace
(function() {
    console.log('Setting up PermissionSystem namespace...');
    
    // Cache for permissions to avoid multiple API calls
    let userPermissions = null;
    let permissionActions = null;
    let permissionsLoaded = false;
    let permissionsPromise = null;

    // Create a global namespace for permission functions immediately
    window.PermissionSystem = {
        hasActionPermission: null,
        isPermissionLoaded: () => permissionsLoaded,
        moduleLoaded: false,
        ready: null
    };

    /**
     * Initialize the module permission system
     */
    async function initModulePermissionSystem() {
        console.log('Initializing module permission system...');
        
        try {
            await fetchUserPermissions();
            
            // Set up the global function only after permissions are loaded
            window.PermissionSystem.hasActionPermission = checkActionPermission;
            window.PermissionSystem.moduleLoaded = true;
            
            console.log('Module permission system initialized:', {
                permissionsLoaded,
                userPermissions,
                permissionActions
            });
            
            // Apply UI changes
            applyPermissionBasedUI();
            
            // Dispatch event
            document.dispatchEvent(new CustomEvent('permissionsLoaded', {
                detail: { userPermissions, permissionActions }
            }));
            
            return true;
            
        } catch (error) {
            console.error('Failed to initialize permission system:', error);
            return false;
        }
    }

    /**
     * Fetch user permissions from the server
     */
    function fetchUserPermissions() {
        if (permissionsPromise) {
            return permissionsPromise;
        }
        
        console.log('Fetching permissions from server...');
        
        permissionsPromise = fetch('/Web2/FrontEnd/AdminUI/includes/fetch_role_permission.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Raw server response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed permission data:', data);
                    
                    if (data.success && Array.isArray(data.permissions)) {
                        userPermissions = data.permissions.map(Number);
                        permissionActions = data.permissionActions || {};
                        permissionsLoaded = true;
                        console.log('Processed permissions:', {
                            userPermissions,
                            permissionActions,
                            permissionsLoaded
                        });
                        return true;
                    } else {
                        throw new Error('Invalid permission data structure');
                    }
                } catch (e) {
                    console.error('Failed to parse or process permissions:', e);
                    throw e;
                }
            })
            .catch(error => {
                console.error('Error fetching permissions:', error);
                permissionsLoaded = false;
                permissionsPromise = null;
                throw error;
            });
        
        return permissionsPromise;
    }

    /**
     * Check if the current user has a specific permission
     */
    function hasPermission(permissionId) {
        if (!permissionsLoaded || !userPermissions) {
            console.warn('Permissions not loaded yet');
            return false;
        }
        return userPermissions.includes(Number(permissionId));
    }

    /**
     * Check if the current user has a specific permission and action
     */
    function checkActionPermission(permissionId, action) {
        if (!permissionsLoaded) {
            console.warn('Permissions not loaded yet for check:', {
                permissionId,
                action,
                permissionsLoaded
            });
            return false;
        }

        // Convert to number for comparison
        permissionId = Number(permissionId);
        
        // If no action specified, just check base permission
        if (!action) {
            return hasPermission(permissionId);
        }
        
        // Check base permission first
        if (!hasPermission(permissionId)) {
            console.log('No base permission for:', permissionId);
            return false;
        }
        
        // If we have actions for this permission, check them
        if (permissionActions && permissionActions[permissionId]) {
            const hasAction = permissionActions[permissionId].includes(action);
            console.log('Checking action permission:', {
                permissionId,
                action,
                allowedActions: permissionActions[permissionId],
                hasAction
            });
            return hasAction;
        }
        
        console.log('No actions defined for permission:', permissionId);
        // If no actions defined, assume no permission
        return false;
    }

    /**
     * Apply permission-based UI adjustments
     */
    function applyPermissionBasedUI() {
        if (!permissionsLoaded) {
            console.warn('Permissions not loaded yet, cannot apply UI changes');
            return;
        }

        console.log('Applying UI changes with permissions:', {
            userPermissions,
            permissionActions
        });

        // Hide elements based on permission
        document.querySelectorAll('[data-permission-id]').forEach(element => {
            const permissionId = element.getAttribute('data-permission-id');
            const requiredAction = element.getAttribute('data-action');
            
            // First check if user has the base permission
            const hasBasePermission = hasPermission(Number(permissionId));
            
            // Then check if they have permission for the specific action
            let hasActionPermission = false;
            if (hasBasePermission && requiredAction && permissionActions && permissionActions[permissionId]) {
                hasActionPermission = permissionActions[permissionId].includes(requiredAction);
            } else if (hasBasePermission && !requiredAction) {
                // If no specific action is required but user has base permission, allow it
                hasActionPermission = true;
            }

            console.log('Checking element permission:', {
                element: element.outerHTML,
                permissionId,
                requiredAction,
                hasBasePermission,
                hasActionPermission,
                allowedActions: permissionActions ? permissionActions[permissionId] : null
            });

            if (!hasBasePermission || !hasActionPermission) {
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
        });
    }

    // Create a promise that will resolve when the system is ready
    window.PermissionSystem.ready = new Promise((resolve) => {
        // Start initialization
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                initModulePermissionSystem().then(resolve);
            });
        } else {
            initModulePermissionSystem().then(resolve);
        }
    });
})();

// Log that the script has finished loading
console.log('module_permission.js loaded, PermissionSystem namespace created:', !!window.PermissionSystem); 