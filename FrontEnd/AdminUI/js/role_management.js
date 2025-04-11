/**
 * Role Management System - Client Side
 * This file handles role management operations in the admin UI
 */

// Cache for roles data
let rolesCache = null;
let permissionsCache = null;

// Initialize the role management system
async function initRoleManagement() {
    try {
        // Load roles data
        await loadRoles();
        
        // Set up event listeners
        setupEventListeners();
        
        // Display roles in the table
        displayRoles();
    } catch (error) {
        console.error('Error initializing role management:', error);
        showNotification('Error initializing role management system', 'error');
    }
}

// Load roles data from the server
async function loadRoles() {
    try {
        const response = await fetch('../../BackEnd/Model/quanlitaikhoan/role_management.php?action=get_all_roles');
        const data = await response.json();
        
        if (data.success) {
            rolesCache = data.roles;
        } else {
            throw new Error(data.message || 'Failed to load roles');
        }
    } catch (error) {
        console.error('Error loading roles:', error);
        throw error;
    }
}

// Load permissions data from the server
async function loadPermissions() {
    try {
        const response = await fetch('../../BackEnd/Model/quanlitaikhoan/permission_management.php?action=get_all_permissions');
        const data = await response.json();
        
        if (data.success) {
            permissionsCache = data.permissions;
            return data.permissions;
        } else {
            throw new Error(data.message || 'Failed to load permissions');
        }
    } catch (error) {
        console.error('Error loading permissions:', error);
        throw error;
    }
}

// Display roles in the table
function displayRoles() {
    const rolesTableBody = document.getElementById('rolesTableBody');
    if (!rolesTableBody) return;
    
    // Clear existing rows
    rolesTableBody.innerHTML = '';
    
    if (!rolesCache || rolesCache.length === 0) {
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = '<td colspan="4" class="text-center">No roles found</td>';
        rolesTableBody.appendChild(emptyRow);
        return;
    }
    
    // Add rows for each role
    rolesCache.forEach(role => {
        const row = document.createElement('tr');
        row.dataset.roleId = role.role_id;
        
        row.innerHTML = `
            <td>${role.role_id}</td>
            <td>${escapeHtml(role.role_name)}</td>
            <td>${escapeHtml(role.description || '')}</td>
            <td>
                <button class="btn btn-sm btn-primary edit-role-btn" data-role-id="${role.role_id}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info view-permissions-btn" data-role-id="${role.role_id}">
                    <i class="fas fa-key"></i> Permissions
                </button>
                <button class="btn btn-sm btn-danger delete-role-btn" data-role-id="${role.role_id}">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        `;
        
        rolesTableBody.appendChild(row);
    });
}

// Set up event listeners
function setupEventListeners() {
    // Add role button
    const addRoleBtn = document.getElementById('addRoleBtn');
    if (addRoleBtn) {
        addRoleBtn.addEventListener('click', showAddRoleModal);
    }
    
    // Edit role buttons
    document.querySelectorAll('.edit-role-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const roleId = e.currentTarget.dataset.roleId;
            showEditRoleModal(roleId);
        });
    });
    
    // View permissions buttons
    document.querySelectorAll('.view-permissions-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const roleId = e.currentTarget.dataset.roleId;
            showPermissionsModal(roleId);
        });
    });
    
    // Delete role buttons
    document.querySelectorAll('.delete-role-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const roleId = e.currentTarget.dataset.roleId;
            confirmDeleteRole(roleId);
        });
    });
    
    // Form submission handlers
    const addRoleForm = document.getElementById('addRoleForm');
    if (addRoleForm) {
        addRoleForm.addEventListener('submit', handleAddRole);
    }
    
    const editRoleForm = document.getElementById('editRoleForm');
    if (editRoleForm) {
        editRoleForm.addEventListener('submit', handleEditRole);
    }
    
    const permissionsForm = document.getElementById('permissionsForm');
    if (permissionsForm) {
        permissionsForm.addEventListener('submit', handleUpdatePermissions);
    }
}

// Show add role modal
function showAddRoleModal() {
    const modal = document.getElementById('addRoleModal');
    if (!modal) return;
    
    // Reset form
    const form = document.getElementById('addRoleForm');
    if (form) {
        form.reset();
    }
    
    // Show modal
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

// Show edit role modal
async function showEditRoleModal(roleId) {
    const modal = document.getElementById('editRoleModal');
    if (!modal) return;
    
    try {
        // Get role data
        const response = await fetch(`../../BackEnd/Model/quanlitaikhoan/role_management.php?action=get_role&role_id=${roleId}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load role data');
        }
        
        const role = data.role;
        
        // Fill form with role data
        const form = document.getElementById('editRoleForm');
        if (form) {
            form.elements['role_id'].value = role.role_id;
            form.elements['role_name'].value = role.role_name;
            form.elements['description'].value = role.description || '';
        }
        
        // Show modal
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    } catch (error) {
        console.error('Error showing edit role modal:', error);
        showNotification('Error loading role data', 'error');
    }
}

// Show permissions modal
async function showPermissionsModal(roleId) {
    const modal = document.getElementById('permissionsModal');
    if (!modal) return;
    
    try {
        // Load permissions if not cached
        if (!permissionsCache) {
            await loadPermissions();
        }
        
        // Get role permissions
        const response = await fetch(`../../BackEnd/Model/quanlitaikhoan/role_management.php?action=get_role_permissions&role_id=${roleId}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load role permissions');
        }
        
        const rolePermissions = data.permissions;
        const rolePermissionIds = rolePermissions.map(p => p.permission_id);
        
        // Fill form with permissions data
        const form = document.getElementById('permissionsForm');
        if (form) {
            form.elements['role_id'].value = roleId;
            
            // Check appropriate checkboxes
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = rolePermissionIds.includes(parseInt(checkbox.value));
            });
        }
        
        // Show modal
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    } catch (error) {
        console.error('Error showing permissions modal:', error);
        showNotification('Error loading permissions data', 'error');
    }
}

// Handle add role form submission
async function handleAddRole(event) {
    event.preventDefault();
    
    const form = event.target;
    const roleName = form.elements['role_name'].value.trim();
    const description = form.elements['description'].value.trim();
    
    if (!roleName) {
        showNotification('Role name is required', 'error');
        return;
    }
    
    try {
        const response = await fetch('../../BackEnd/Model/quanlitaikhoan/role_management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'create_role',
                role_name: roleName,
                description: description
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addRoleModal'));
            modal.hide();
            
            // Reload roles and refresh display
            await loadRoles();
            displayRoles();
            
            showNotification('Role created successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to create role');
        }
    } catch (error) {
        console.error('Error creating role:', error);
        showNotification(error.message || 'Error creating role', 'error');
    }
}

// Handle edit role form submission
async function handleEditRole(event) {
    event.preventDefault();
    
    const form = event.target;
    const roleId = form.elements['role_id'].value;
    const roleName = form.elements['role_name'].value.trim();
    const description = form.elements['description'].value.trim();
    
    if (!roleName) {
        showNotification('Role name is required', 'error');
        return;
    }
    
    try {
        const response = await fetch('../../BackEnd/Model/quanlitaikhoan/role_management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'update_role',
                role_id: roleId,
                role_name: roleName,
                description: description
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editRoleModal'));
            modal.hide();
            
            // Reload roles and refresh display
            await loadRoles();
            displayRoles();
            
            showNotification('Role updated successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to update role');
        }
    } catch (error) {
        console.error('Error updating role:', error);
        showNotification(error.message || 'Error updating role', 'error');
    }
}

// Handle update permissions form submission
async function handleUpdatePermissions(event) {
    event.preventDefault();
    
    const form = event.target;
    const roleId = form.elements['role_id'].value;
    
    // Get selected permission IDs
    const selectedPermissions = Array.from(form.querySelectorAll('input[type="checkbox"]:checked'))
        .map(checkbox => parseInt(checkbox.value));
    
    try {
        const response = await fetch('../../BackEnd/Model/quanlitaikhoan/role_management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'assign_permissions',
                role_id: roleId,
                permission_ids: selectedPermissions
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('permissionsModal'));
            modal.hide();
            
            showNotification('Permissions updated successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to update permissions');
        }
    } catch (error) {
        console.error('Error updating permissions:', error);
        showNotification(error.message || 'Error updating permissions', 'error');
    }
}

// Confirm delete role
function confirmDeleteRole(roleId) {
    if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
        deleteRole(roleId);
    }
}

// Delete role
async function deleteRole(roleId) {
    try {
        const response = await fetch('../../BackEnd/Model/quanlitaikhoan/role_management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'delete_role',
                role_id: roleId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload roles and refresh display
            await loadRoles();
            displayRoles();
            
            showNotification('Role deleted successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to delete role');
        }
    } catch (error) {
        console.error('Error deleting role:', error);
        showNotification(error.message || 'Error deleting role', 'error');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Check if toast container exists, create if not
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    // Add toast to container
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

// Escape HTML to prevent XSS
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initRoleManagement); 