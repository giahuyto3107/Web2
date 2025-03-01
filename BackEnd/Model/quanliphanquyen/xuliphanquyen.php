
<?php
// Kết nối đến cơ sở dữ liệu
include('../../Config/config.php');
// Xử lý khi form được submit
if (isset($_POST['them_phanquyen'])) {
    // Lấy dữ liệu từ form
    $permission_name = $_POST['permission_name'];
    $permission_description = $_POST['permission_description'];
    $status_id = $_POST['status_id'];

    // Kiểm tra xem các trường có được điền đầy đủ không
    if (empty($permission_name) || empty($permission_description) || empty($status_id)) {
        echo "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Thêm quyền mới vào bảng permission
        $sql_them = "INSERT INTO permission (permission_name, permission_description, status_id) 
                     VALUES ('$permission_name', '$permission_description', '$status_id')";

        if (mysqli_query($conn, $sql_them)) {
            // Thành công, chuyển hướng về trang quản lý phân quyền
            header('Location: ../../../Frontend/AdminUI/index.php?action=quanlyphanquyen&query=them');
            exit;
        } else {
            // Lỗi khi thêm quyền
            echo "Lỗi: " . mysqli_error($conn);
        }
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['updateRolePermission'])) {
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
    }
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

