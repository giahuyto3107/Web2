<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

// Debug logging function
function debug_log($message, $data = null) {
    error_log("PHANQUYEN DEBUG: " . $message . ($data !== null ? " - Data: " . print_r($data, true) : ""));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get role_id and permissions from POST data
$role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : 0;
$permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

debug_log("Received role_id", $role_id);
debug_log("Received permissions", $permissions);

if ($role_id <= 0) {
    debug_log("Invalid role ID: " . $role_id);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid role ID'
    ]);
    exit;
}

// Start transaction for data integrity
$conn->begin_transaction();
debug_log("Started transaction");

try {
    // Delete existing permissions for the role
    $sql_delete = "DELETE FROM role_permission WHERE role_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $role_id);
    $stmt_delete->execute();
    debug_log("Deleted existing permissions for role", $role_id);
    $stmt_delete->close();

    // Insert new permissions
    if (!empty($permissions)) {
        $sql_insert = "INSERT INTO role_permission (role_id, permission_id, action) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        
        foreach ($permissions as $permission_id => $actions) {
            $permission_id = intval($permission_id);
            debug_log("Processing permission_id", $permission_id);
            debug_log("Actions for permission", $actions);
            
            foreach ($actions as $action => $value) {
                if ($value === 'on') { // Checkbox checked
                    debug_log("Inserting permission", [
                        'role_id' => $role_id,
                        'permission_id' => $permission_id,
                        'action' => $action
                    ]);
                    $stmt_insert->bind_param("iis", $role_id, $permission_id, $action);
                    $stmt_insert->execute();
                }
            }
        }
        $stmt_insert->close();
    }

    // Commit transaction
    $conn->commit();
    debug_log("Transaction committed successfully");

    // Verify the inserted permissions
    $verify_sql = "SELECT permission_id, action FROM role_permission WHERE role_id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("i", $role_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    $verified_permissions = [];
    while ($row = $verify_result->fetch_assoc()) {
        $verified_permissions[] = $row;
    }
    debug_log("Verified inserted permissions", $verified_permissions);
    $verify_stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => 'Phân quyền thành công',
        'debug_info' => [
            'role_id' => $role_id,
            'permissions' => $permissions,
            'verified_permissions' => $verified_permissions
        ]
    ]);

} catch (Exception $e) {
    $conn->rollback();
    debug_log("Error occurred", $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>