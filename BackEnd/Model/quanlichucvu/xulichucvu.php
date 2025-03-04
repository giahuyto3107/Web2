<?php
include('../../Config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['updateRole'])) {
    $role_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $role_name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $role_description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $status_id = isset($_POST['status']) ? intval($_POST['status']) : 0;

    if ($role_id > 0 && !empty($role_name) && !empty($role_description) && ($status_id == 1 || $status_id == 2)) {
        $sql_check = "SELECT * FROM role WHERE role_name = ? AND id != ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("si", $role_name, $role_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "Tên vai trò đã tồn tại.";
        } else {
            $sql_update = "UPDATE role SET role_name = ?, role_description = ?, status_id = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssii", $role_name, $role_description, $status_id, $role_id);

            if ($stmt_update->execute()) {
                header('Location: ../../../Frontend/AdminUI/index.php?action=quanlichucvu&query=them&thanhcong=0');
                exit;
            } else {
                echo "Có lỗi xảy ra khi cập nhật: " . $stmt_update->error;
            }
        }
    } 
}elseif (isset($_POST['themchucvu'])) {
    // Lấy dữ liệu từ form
    $tenchucvu = $_POST['role_name'];
    $mota = $_POST['role_description'];
    $trangthai = $_POST['status_id'];
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : array();

    // Kiểm tra xem tên chức vụ đã tồn tại chưa
    $sql_check = "SELECT * FROM role WHERE role_name = '$tenchucvu'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanlichucvu&query=them&thanhcong=1');
        exit;
    } else {
        $sql_them = "INSERT INTO role (role_name, role_description, status_id) 
                     VALUES ('$tenchucvu', '$mota', '$trangthai')";
        
        if (mysqli_query($conn, $sql_them)) {
            $role_id = mysqli_insert_id($conn);

            if (!empty($permissions)) {
                foreach ($permissions as $permission_id) {
                    $sql_insert_permission = "INSERT INTO role_permission (role_id, permission_id) 
                                             VALUES ('$role_id', '$permission_id')";
                    mysqli_query($conn, $sql_insert_permission);
                }
            }

            header('Location: ../../../Frontend/AdminUI/index.php?action=quanlichucvu&query=them&thanhcong=2');
            exit;
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}
}
$conn->close();
?>