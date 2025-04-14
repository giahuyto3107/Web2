<?php
// Include database connection
require_once __DIR__ . '/../../../BackEnd/Config/config.php';
global $conn;

// Query to get all role permissions
$query = "SELECT rp.role_id, rp.permission_id, rp.action 
          FROM role_permission rp 
          ORDER BY rp.role_id, rp.permission_id, rp.action";

$result = $conn->query($query);

echo "<pre>";
echo "Role Permissions Table:\n";
echo "=====================\n";
echo sprintf("%-8s %-13s %-10s\n", "Role ID", "Permission ID", "Action");
echo "------------------------------\n";

while ($row = $result->fetch_assoc()) {
    echo sprintf("%-8s %-13s %-10s\n", 
        $row['role_id'], 
        $row['permission_id'], 
        $row['action']
    );
}

echo "</pre>";
?> 