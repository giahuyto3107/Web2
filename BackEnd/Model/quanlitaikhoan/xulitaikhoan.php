<?php
    include('../../Config/config.php');

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $acc = mysqli_real_escape_string($conn, $_POST['acc']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Handle file upload
    $pro5Image = $_FILES['pro5Image']['name'];
    $pro5Image_tmp = $_FILES['pro5Image']['tmp_name'];


    if (!empty($pro5Image)) {
        move_uploaded_file($pro5Image_tmp, '../../Uploads/Profile Picture/' . $pro5Image);
        $imageUpdate = ", profile_picture = '$pro5Image'";

        //xoa hinh anh cu
		$sql = "SELECT * FROM user WHERE user_id = '$_GET[id]' LIMIT 1";
		$query = mysqli_query($conn, $sql);
		while ($row = mysqli_fetch_array($query)) {
			unlink('../../Uploads/Profile Picture/' . $row['profile_picture']);
		}
    }

    // Query to get role_id from role_name
    $roleQuery = "SELECT id FROM role WHERE role_name = '$role'";
    $roleResult = mysqli_query($conn, $roleQuery);

    if ($roleRow = mysqli_fetch_assoc($roleResult)) {
        $role_id = $roleRow['id']; // Extract the role_id

        // Now update the account table with the retrieved role_id
        $sql_updateAcc = "UPDATE account 
                        SET account_name = '$acc', email = '$email', status_id = $status, role_id = $role_id 
                        WHERE account_id = $id";
    }

    // Update user table
    $sql_updateUser = "UPDATE user 
                      SET full_name = '$name', date_of_birth = '$dob' $imageUpdate
                      WHERE account_id = $id";

    // Execute queries
    // Execute queries and check for errors
    if (!mysqli_query($conn, $sql_updateAcc)) {
        die("Error updating account: " . mysqli_error($conn));
    }

    if (!mysqli_query($conn, $sql_updateUser)) {
        die("Error updating user: " . mysqli_error($conn));
    }

    // Redirect
    // header('Location: ../../../FrontEnd/AdminUI/index.php?action=quanlitaikhoan&query=them');
?>
