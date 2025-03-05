<?php
// Kết nối đến cơ sở dữ liệu
include('../../Config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && $_POST['name'] && isset($_POST['description']) && isset($_POST['status'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $status = $_POST['status'];
        $updateSql = "UPDATE permission p
                    LEFT JOIN (SELECT permission_name FROM permission WHERE permission_id != '$id') sub
                    ON p.permission_name = sub.permission_name
                    SET p.permission_name = '$name', 
                        p.permission_description = '$description',
                        p.status_id = '$status'
                    WHERE p.permission_id = $id AND sub.permission_name IS NULL;";
        if (!mysqli_query($conn, $updateSql)) {
            die(json_encode(["error" => "Error updating permissions: " . mysqli_error($conn)]));
        }            
    } elseif (isset($_POST['updateRolePermission'])) {
        if (isset($_POST['permissions']) && !empty($_POST['permissions'])) {
            $permissions = json_decode($_POST['permissions'], true);
            $role_id = mysqli_real_escape_string($conn, $_POST['role_id']);

            // Delete old role permissions
            $deleteSql = "DELETE FROM role_permission WHERE role_id = '$role_id'";
            if (!mysqli_query($conn, $deleteSql)) {
                die(json_encode(["error" => "Error deleting old permissions: " . mysqli_error($conn)]));
            }

            // Insert new role permissions
            foreach ($permissions as $permissionId) {
                $permissionId = mysqli_real_escape_string($conn, $permissionId);
                $insertSql = "INSERT INTO role_permission (role_id, permission_id) VALUES ('$role_id', '$permissionId')";
                if (!mysqli_query($conn, $insertSql)) {
                    die(json_encode(["error" => "Error inserting permission: " . mysqli_error($conn)]));
                }
            }

            echo json_encode(["success" => "Permissions updated successfully!"]);
            exit;
        } else {
            echo json_encode(["error" => "No permissions selected!"]);
            exit;
        }
    } elseif (isset($_POST['them_phanquyen'])) {
        $permission_name = $_POST['permission_name'];
        $permission_description = $_POST['permission_description'];
        $status_id = $_POST['status_id'];

        $sql_check = "SELECT * FROM permission WHERE permission_name = '$permission_name'";
        $result_check = mysqli_query($conn, $sql_check);

        if (mysqli_num_rows($result_check) > 0) {
            header('Location: ../../../Frontend/AdminUI/index.php?action=quanliphanquyen&query=them&thanhcong=1');
            exit;
        } else {
            $sql_them = "INSERT INTO permission (permission_name, permission_description, status_id) 
                         VALUES ('$permission_name', '$permission_description', '$status_id')";

            if (mysqli_query($conn, $sql_them)) {
                header('Location: ../../../Frontend/AdminUI/index.php?action=quanliphanquyen&query=them&thanhcong=2');
                exit;
            } else {
                echo "Lỗi: " . mysqli_error($conn);
            }
        }
    } else {
        echo "Yêu cầu không hợp lệ.";
    }
} else {
    header("Location: ../../../Frontend/AdminUI/index.php?action=quanliphanquyen&query=them");
    exit;
}

// Fetch assigned permissions for a role
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['role_id'])) {
    $role_id = mysqli_real_escape_string($conn, $_GET['role_id']);
    $query = "SELECT permission_id FROM role_permission WHERE role_id = '$role_id'";
    $result = mysqli_query($conn, $query);
    
    $permissions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $permissions[] = $row['permission_id'];
    }

    echo json_encode($permissions);
    exit;
}
?>