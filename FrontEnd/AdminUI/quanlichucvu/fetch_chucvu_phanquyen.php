<?php
    include("../../../BackEnd/Config/config.php"); // Include your database connection

    $role_id = isset($_GET['role_id']) ? intval($_GET['role_id']) : 0;

    if ($role_id == 0) {
        die(json_encode([])); // Return empty array if role_id is invalid
    }

    // Query to fetch permission IDs assigned to this role
    $sql = "SELECT permission_id FROM role_permission WHERE role_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $role_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $assignedPermissions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $assignedPermissions[] = $row['permission_id'];
    }

    echo json_encode($assignedPermissions); // Return permission IDs as JSON
?>
