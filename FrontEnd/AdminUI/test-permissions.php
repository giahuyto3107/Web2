<?php
/**
 * Test Permissions Page
 * This page demonstrates both permission systems in action
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
    <title>Test Permissions</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="container">
        <h1>Test Permissions</h1>
        
        <div class="card">
            <h2>Module/Action Permission System</h2>
            
            <!-- Element that should be hidden if user doesn't have 'view_users' permission -->
            <div class="permission-test" data-permission="view_users">
                <h3>View Users Section</h3>
                <p>This section should only be visible to users with the 'view_users' permission.</p>
            </div>
            
            <!-- Element that should be disabled if user doesn't have 'edit_users' permission -->
            <div class="permission-test">
                <h3>Edit Users Section</h3>
                <button class="btn btn-primary" data-permission="edit_users">Edit User</button>
            </div>
            
            <!-- Element that should be hidden if user doesn't have 'delete_users' permission -->
            <div class="permission-test" data-permission="delete_users">
                <h3>Delete Users Section</h3>
                <p>This section should only be visible to users with the 'delete_users' permission.</p>
            </div>
            
            <!-- Element that should be hidden if user doesn't have 'manage_roles' permission -->
            <div class="permission-test" data-permission="manage_roles">
                <h3>Manage Roles Section</h3>
                <p>This section should only be visible to users with the 'manage_roles' permission.</p>
            </div>
        </div>
        
        <div class="card">
            <h2>Permission ID System</h2>
            
            <!-- Elements that should be hidden based on permission IDs -->
            <div class="permission-test" data-permission-id="1">
                <h3>Quản lý sản phẩm</h3>
                <p>This section should only be visible to users with permission ID 1.</p>
                <p>Action: <span class="permission-action">Loading...</span></p>
            </div>
            
            <div class="permission-test" data-permission-id="4">
                <h3>Quản lý tài khoản</h3>
                <p>This section should only be visible to users with permission ID 4.</p>
                <p>Action: <span class="permission-action">Loading...</span></p>
            </div>
            
            <div class="permission-test" data-permission-id="12">
                <h3>Quản lý phân quyền</h3>
                <p>This section should only be visible to users with permission ID 12.</p>
                <p>Action: <span class="permission-action">Loading...</span></p>
            </div>
        </div>
        
        <div class="card">
            <h2>Current User Permissions</h2>
            <pre id="permissions-display">Loading permissions...</pre>
        </div>
        
        <div class="card">
            <h2>Debug Information</h2>
            <p>If you're seeing errors with the permission system, check the following:</p>
            <ol>
                <li>Make sure the fetch_role_permission.php file exists and is accessible</li>
                <li>Check that the file returns valid JSON</li>
                <li>Verify that the user is logged in and has a valid role_id</li>
            </ol>
            <p>Current session information:</p>
            <pre><?php echo json_encode($_SESSION, JSON_PRETTY_PRINT); ?></pre>
        </div>
    </div>
    
    <!-- Include both permission scripts -->
    <script src="js/permissions.js"></script>
    <script src="js/module_permission.js"></script>
    <script src="js/init-permissions.js"></script>
    
    <script>
        // Display the current user's permissions
        document.addEventListener('DOMContentLoaded', function() {
            // Function to update the permissions display
            function updatePermissionsDisplay() {
                const permissionsDisplay = document.getElementById('permissions-display');
                
                // Display module/action permissions
                if (typeof window.userPermissions !== 'undefined') {
                    permissionsDisplay.textContent = JSON.stringify(window.userPermissions, null, 2);
                } else {
                    permissionsDisplay.textContent = 'Module/Action permissions not loaded yet';
                }
                
                // Add permission ID information if available
                if (typeof window.permissionsLoaded !== 'undefined' && window.permissionsLoaded) {
                    permissionsDisplay.textContent += '\n\nPermission IDs loaded: ' + 
                        (window.userPermissions ? window.userPermissions.join(', ') : 'None');
                    
                    // Add permission actions if available
                    if (typeof window.permissionActions !== 'undefined' && window.permissionActions) {
                        permissionsDisplay.textContent += '\n\nPermission Actions:';
                        for (const [id, action] of Object.entries(window.permissionActions)) {
                            permissionsDisplay.textContent += `\n  ${id}: ${action}`;
                        }
                    }
                } else {
                    permissionsDisplay.textContent += '\n\nPermission IDs not loaded yet';
                }
                
                // Update the permission action spans
                const permissionActionSpans = document.querySelectorAll('.permission-action');
                permissionActionSpans.forEach(span => {
                    const permissionId = span.closest('.permission-test').getAttribute('data-permission-id');
                    if (permissionId && typeof window.permissionActions !== 'undefined' && window.permissionActions) {
                        span.textContent = window.permissionActions[permissionId] || 'None';
                    }
                });
            }
            
            // Update the display immediately
            updatePermissionsDisplay();
            
            // Set up a timeout to check again after a short delay
            setTimeout(updatePermissionsDisplay, 1000);
            
            // Set up another timeout to check again after a longer delay
            setTimeout(updatePermissionsDisplay, 3000);
        });
    </script>
</body>
</html> 