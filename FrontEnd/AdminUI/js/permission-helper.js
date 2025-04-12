/**
 * Permission Helper
 * This file contains functions for client-side permission checks
 * It relies on the permissions data provided by permission-script.php
 */

// Global permissions object that will be populated by permission-script.php
let userPermissions = {};

/**
 * Initialize the permissions object
 * This function should be called after the permissions data is loaded
 * 
 * @param {Object} permissions The permissions object from the server
 */
function initPermissions(permissions) {
    userPermissions = permissions;
    console.log('Permissions initialized:', userPermissions);
}

/**
 * Check if the user has permission for a specific action in a module
 * 
 * @param {string} moduleName The module name
 * @param {string} actionName The action name
 * @returns {boolean} True if the user has permission, false otherwise
 */
function hasPermission(moduleName, actionName) {
    // Check if the module exists in the permissions
    if (!userPermissions[moduleName]) {
        return false;
    }
    
    // Check if the action is allowed for the module
    return userPermissions[moduleName].includes(actionName);
}

/**
 * Check if the user has access to a specific module
 * 
 * @param {string} moduleName The module name
 * @returns {boolean} True if the user has access to the module, false otherwise
 */
function hasModuleAccess(moduleName) {
    return !!userPermissions[moduleName];
}

/**
 * Get all actions the user has permission for in a specific module
 * 
 * @param {string} moduleName The module name
 * @returns {Array} An array of action names
 */
function getActionsForModule(moduleName) {
    return userPermissions[moduleName] || [];
}

/**
 * Get all modules the user has access to
 * 
 * @returns {Array} An array of module names
 */
function getAccessibleModules() {
    return Object.keys(userPermissions);
}

/**
 * Hide elements that require permissions the user doesn't have
 * 
 * @param {string} moduleName The module name
 * @param {string} actionName The action name
 * @param {string} selector The CSS selector for the elements to hide
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
 * Disable elements that require permissions the user doesn't have
 * 
 * @param {string} moduleName The module name
 * @param {string} actionName The action name
 * @param {string} selector The CSS selector for the elements to disable
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
 * Add a permission check to a button or link
 * 
 * @param {string} moduleName The module name
 * @param {string} actionName The action name
 * @param {string} selector The CSS selector for the button or link
 * @param {Function} callback The function to call if the user has permission
 */
function addPermissionCheck(moduleName, actionName, selector, callback) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
        element.addEventListener('click', (event) => {
            if (hasPermission(moduleName, actionName)) {
                callback(event);
            } else {
                event.preventDefault();
                alert('You do not have permission to perform this action.');
            }
        });
    });
}

/**
 * Initialize permission checks for all elements with data-permission attributes
 * Elements should have data-permission attributes in the format:
 * data-permission-module="moduleName" data-permission-action="actionName"
 */
function initPermissionChecks() {
    // Find all elements with permission attributes
    const elements = document.querySelectorAll('[data-permission-module][data-permission-action]');
    
    elements.forEach(element => {
        const moduleName = element.getAttribute('data-permission-module');
        const actionName = element.getAttribute('data-permission-action');
        
        // Check if the element should be hidden or disabled
        if (element.hasAttribute('data-permission-hide') && element.getAttribute('data-permission-hide') === 'true') {
            hideIfNoPermission(moduleName, actionName, `[data-permission-module="${moduleName}"][data-permission-action="${actionName}"]`);
        } else {
            disableIfNoPermission(moduleName, actionName, `[data-permission-module="${moduleName}"][data-permission-action="${actionName}"]`);
        }
        
        // Add click handler if it's a button or link
        if (element.tagName === 'BUTTON' || element.tagName === 'A') {
            addPermissionCheck(moduleName, actionName, `[data-permission-module="${moduleName}"][data-permission-action="${actionName}"]`, (event) => {
                // Default behavior is to allow the click
                return true;
            });
        }
    });
}

// Initialize permission checks when the DOM is loaded
document.addEventListener('DOMContentLoaded', initPermissionChecks); 