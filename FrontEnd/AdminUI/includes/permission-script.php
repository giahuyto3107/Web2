<?php
/**
 * Permission Script
 * This file loads user permissions and initializes the JavaScript permissions object
 */

// Prevent direct access to this file
if (!defined('ADMIN_ACCESS')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access to this file is not allowed.');
}

// Include the permission functions
require_once __DIR__ . '/permission-functions.php';

// Get the current user ID from the session
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Get the user's permissions
$permissions = getUserPermissions($userId);

// Convert the permissions array to a JSON string for JavaScript
$permissionsJson = json_encode($permissions);

// Debug log the permissions being sent to JavaScript
error_log('Permissions being sent to JavaScript: ' . $permissionsJson);
?>

<!-- Load the permission system scripts -->
<script src="../js/permissions.js"></script>
<script src="../js/module_permission.js"></script>
<script src="../js/action-permission.js"></script>

<!-- Initialize the permissions object -->
<script>
    // Initialize all permission systems with the same data
    document.addEventListener('DOMContentLoaded', function() {
        const permissions = <?php echo $permissionsJson; ?>;
        console.log('Raw server-side permissions:', permissions);
        
        // Format permissions to ensure consistent structure
        const formattedPermissions = {};
        Object.entries(permissions).forEach(([permId, permData]) => {
            console.log('Processing permission:', { permId, permData });
            formattedPermissions[permId] = {
                actions: Array.isArray(permData.actions) ? permData.actions : []
            };
            console.log('Formatted permission:', formattedPermissions[permId]);
        });
        
        console.log('Final formatted permissions:', formattedPermissions);
        
        // Make permissions available globally first
        window.serverPermissions = formattedPermissions;
        
        // Then initialize all permission systems
        if (typeof initPermissions === 'function') {
            console.log('Initializing base permission system');
            initPermissions(formattedPermissions);
        }
        
        if (typeof initModulePermissionSystem === 'function') {
            console.log('Initializing module permission system');
            initModulePermissionSystem();
        }
        
        if (typeof initActionPermissionSystem === 'function') {
            console.log('Initializing action permission system');
            initActionPermissionSystem();
        }
        
        // Finally dispatch the event that permissions are loaded
        console.log('Dispatching permissionsLoaded event');
        document.dispatchEvent(new Event('permissionsLoaded'));
    });
</script> 