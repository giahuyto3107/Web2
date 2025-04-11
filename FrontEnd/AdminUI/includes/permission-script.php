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
?>

<!-- Load the permissions.js file -->
<script src="../js/permissions.js"></script>

<!-- Initialize the permissions object -->
<script>
    // Initialize the permissions object with the user's permissions
    document.addEventListener('DOMContentLoaded', function() {
        initPermissions(<?php echo $permissionsJson; ?>);
    });
</script> 