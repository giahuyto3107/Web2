/**
 * Permissions.js
 * This file contains functions for handling permissions on the client side
 */

// Global permissions object
let userPermissions = {};

/**
 * Initialize the permissions object
 * This function is called from permission-script.php
 */
function initPermissions(permissions) {
    userPermissions = permissions;
    console.log('Permissions initialized:', userPermissions);
}

/**
 * Check if the user has permission for a specific action in a module
 * 
 * @param {string} moduleName - The name of the module
 * @param {string} actionName - The name of the action
 * @returns {boolean} - True if the user has permission, false otherwise
 */
function hasPermission(moduleName, actionName) {
    // Check if the module exists in the permissions
    if (!userPermissions[moduleName]) {
        return false;
    }
    
    // Check if the action exists in the module
    return userPermissions[moduleName].includes(actionName);
}

/**
 * Hide elements that the user doesn't have permission to access
 * 
 * @param {string} moduleName - The name of the module
 * @param {string} actionName - The name of the action
 * @param {string} selector - The CSS selector for the elements to hide
 */
function hideIfNoPermission(moduleName, actionName, selector) {
    if (!hasPermission(moduleName, actionName)) {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            element.style.display = 'none';
        });
    }
}

/**
 * Disable elements that the user doesn't have permission to access
 * 
 * @param {string} moduleName - The name of the module
 * @param {string} actionName - The name of the action
 * @param {string} selector - The CSS selector for the elements to disable
 */
function disableIfNoPermission(moduleName, actionName, selector) {
    if (!hasPermission(moduleName, actionName)) {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            element.disabled = true;
            element.classList.add('disabled');
        });
    }
}

/**
 * Add click event listeners to elements that require permission
 * 
 * @param {string} moduleName - The name of the module
 * @param {string} actionName - The name of the action
 * @param {string} selector - The CSS selector for the elements
 * @param {Function} callback - The function to call if the user has permission
 */
function addPermissionClickListener(moduleName, actionName, selector, callback) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
        element.addEventListener('click', (event) => {
            if (hasPermission(moduleName, actionName)) {
                callback(event);
            } else {
                event.preventDefault();
                showPermissionDeniedToast();
            }
        });
    });
}

/**
 * Show a toast notification when permission is denied
 */
function showPermissionDeniedToast() {
    // Check if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        
        const toastElement = document.createElement('div');
        toastElement.className = 'toast';
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');
        
        toastElement.innerHTML = `
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">Permission Denied</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                You don't have permission to perform this action.
            </div>
        `;
        
        toastContainer.appendChild(toastElement);
        
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });
        
        toast.show();
        
        // Remove the toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    } else {
        alert('You don\'t have permission to perform this action.');
    }
}

/**
 * Create a toast container if it doesn't exist
 * 
 * @returns {HTMLElement} - The toast container element
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1050';
    document.body.appendChild(container);
    return container;
}

/**
 * Initialize permission-based UI elements
 * This function should be called after the DOM is loaded
 */
function initPermissionUI() {
    // Example: Hide edit buttons if the user doesn't have edit permission
    hideIfNoPermission('roles', 'edit', '.edit-role-btn');
    
    // Example: Disable delete buttons if the user doesn't have delete permission
    disableIfNoPermission('roles', 'delete', '.delete-role-btn');
    
    // Example: Add click event listeners to buttons that require permission
    addPermissionClickListener('roles', 'create', '.create-role-btn', (event) => {
        // Show create role modal
        const modal = new bootstrap.Modal(document.getElementById('createRoleModal'));
        modal.show();
    });
}

// Initialize the permission UI when the DOM is loaded
document.addEventListener('DOMContentLoaded', initPermissionUI); 