<?php
include('../../Config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ POST
    $role_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $role_name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $role_description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $status_id = isset($_POST['status']) ? intval($_POST['status']) : 0;

    if ($role_id > 0 && !empty($role_name) && !empty($role_description) && ($status_id == 1 || $status_id == 2)) {
        // Kiểm tra xem tên vai trò đã tồn tại chưa
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
                header('Location: ../../../Frontend/AdminUI/index.php?action=quanlichucvu&query=sua&id=' . $role_id . '&thanhcong=0');
                exit;
            } else {
                echo "Có lỗi xảy ra khi cập nhật: " . $stmt_update->error;
            }
        }
    } else {
        echo "Dữ liệu không hợp lệ.";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}

$conn->close();
?>