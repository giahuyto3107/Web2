<dialog data-modal id="permission-modal">
    <div class="modal-header">
        <h2>Phân quyền cho chức vụ: <span id="modal-role-name">N/A</span></h2>
        <button class="modal-close" data-id="permission-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form id="modal-permission-form" class="modal-form">
            <input type="hidden" id="modal-role-id" name="role_id">
            
            
            <div class="checkbox-group" id="permissions-list">
                <!-- Danh sách quyền sẽ được thêm bằng JavaScript -->
            </div>
            <div class="modal-buttons">
                <button type="button" class="close" id="permission-close-button">Hủy</button>
                <button type="submit" class="save" data-permission-id="10" data-action="Cập nhật phân quyền">Lưu</button>
            </div>
        </form>
    </div>
</dialog>

<style>
.current-permissions-section {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #e9ecef;
}

.current-permissions-section h3 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 16px;
    color: #495057;
}

.current-permissions-list {
    max-height: 200px;
    overflow-y: auto;
}

.permission-item {
    margin-bottom: 8px;
    padding: 8px;
    background-color: #fff;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.permission-name {
    font-weight: bold;
    margin-bottom: 5px;
}

.permission-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.action-tag {
    display: inline-block;
    padding: 2px 8px;
    background-color: #e9ecef;
    border-radius: 12px;
    font-size: 12px;
    color: #495057;
}

/* Styles for checkboxes */
.permission-checkbox {
    margin-right: 5px;
}

.permission-checkbox:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.permission-checkbox:disabled + label {
    color: #adb5bd;
}
</style>

<script>
// Function to display current permissions
function displayCurrentPermissions(roleId) {
    const currentPermissionsList = document.getElementById('current-permissions-list');
    currentPermissionsList.innerHTML = '<p>Đang tải quyền hiện tại...</p>';
    
    // Fetch current permissions for the role
    fetch(`quanlichucvu/fetch_chucvu_phanquyen.php?role_id=${roleId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.data && data.data.length > 0) {
                // Group permissions by permission_id
                const groupedPermissions = {};
                data.data.forEach(item => {
                    if (!groupedPermissions[item.permission_id]) {
                        groupedPermissions[item.permission_id] = {
                            name: item.permission_name,
                            actions: []
                        };
                    }
                    if (item.action) {
                        groupedPermissions[item.permission_id].actions.push(item.action);
                    }
                });
                
                // Display grouped permissions
                currentPermissionsList.innerHTML = '';
                Object.keys(groupedPermissions).forEach(permissionId => {
                    const permission = groupedPermissions[permissionId];
                    const permissionItem = document.createElement('div');
                    permissionItem.className = 'permission-item';
                    
                    const permissionName = document.createElement('div');
                    permissionName.className = 'permission-name';
                    permissionName.textContent = permission.name;
                    
                    const actionsContainer = document.createElement('div');
                    actionsContainer.className = 'permission-actions';
                    
                    permission.actions.forEach(action => {
                        const actionTag = document.createElement('span');
                        actionTag.className = 'action-tag';
                        actionTag.textContent = action;
                        actionsContainer.appendChild(actionTag);
                    });
                    
                    permissionItem.appendChild(permissionName);
                    permissionItem.appendChild(actionsContainer);
                    currentPermissionsList.appendChild(permissionItem);
                });
            } else {
                currentPermissionsList.innerHTML = '<p>Chưa có quyền nào được gán cho chức vụ này.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching current permissions:', error);
            currentPermissionsList.innerHTML = '<p>Có lỗi khi tải quyền hiện tại. Vui lòng thử lại.</p>';
        });
}

// Function to check if the current user has permission to perform an action
function checkUserPermission(permissionId, action = null) {
    // Get the current user's permissions from the window.PermissionSystem
    if (window.PermissionSystem && window.PermissionSystem.hasActionPermission) {
        return window.PermissionSystem.hasActionPermission(permissionId, action);
    }
    return false;
}

// Function to update the permission checkboxes based on user permissions
function updatePermissionCheckboxes() {
    // Get all checkboxes in the permissions list
    const checkboxes = document.querySelectorAll('#permissions-list input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        // Extract permission ID and action from the checkbox name
        const nameParts = checkbox.name.match(/permissions\[(\d+)\]\[([^\]]+)\]/);
        if (nameParts && nameParts.length >= 3) {
            const permissionId = parseInt(nameParts[1]);
            const action = nameParts[2];
            
            // Check if the current user has permission to perform this action
            const hasPermission = checkUserPermission(permissionId, action);
            
            // If the user has permission, check the checkbox
            if (hasPermission) {
                checkbox.checked = true;
            }
        }
    });
}

// Update the existing event listener for the permission modal
document.addEventListener('DOMContentLoaded', function() {
    const permissionModal = document.getElementById('permission-modal');
    if (permissionModal) {
        // When the modal is opened, display current permissions and update checkboxes
        permissionModal.addEventListener('show', function() {
            const roleId = document.getElementById('modal-role-id').value;
            if (roleId) {
                displayCurrentPermissions(roleId);
                // Wait for permissions to be loaded
                window.PermissionSystem.ready.then(() => {
                    updatePermissionCheckboxes();
                });
            }
        });
    }
});
</script>