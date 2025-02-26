<?php
    include('../../Config/config.php');

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Update user table
    $sql_updatePermission = "UPDATE permission 
                      SET permission_name = '$name', permission_description = '$description'
                      WHERE permission_id = $id";

    // Execute queries
    // Execute queries and check for errors
    if (!mysqli_query($conn, $sql_updatePermission)) {
        die("Error updating permission: " . mysqli_error($conn));
    }

    // Redirect
    // header('Location: ../../../FrontEnd/AdminUI/index.php?action=quanlitaikhoan&query=them');
?>
