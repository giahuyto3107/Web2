<?php
// Kết nối đến cơ sở dữ liệu
include('../../Config/config.php');

// Thiết lập header cho phản hồi JSON
header('Content-Type: application/json');

// Hàm trả về lỗi
function returnError($conn, $message) {
    echo json_encode(["status" => "error", "message" => $message . (mysqli_error($conn) ? " - " . mysqli_error($conn) : "")]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cập nhật quyền
    if (isset($_POST['permission_id'], $_POST['permission_name'], $_POST['permission_description'], $_POST['status_id'])) {
        $id = (int) $_POST['permission_id']; // Ép kiểu thành int
        $name = mysqli_real_escape_string($conn, $_POST['permission_name']);
        $description = mysqli_real_escape_string($conn, $_POST['permission_description']);
        $status = (int) $_POST['status_id']; // Ép kiểu thành int

        // Kiểm tra giá trị hợp lệ
        if ($id <= 0 || !in_array($status, [1, 2, 6])) {
            returnError($conn, "Dữ liệu đầu vào không hợp lệ!");
        }

        // Kiểm tra trùng lặp tên quyền (trừ chính bản ghi đang chỉnh sửa)
        $checkSql = "SELECT COUNT(*) FROM permission WHERE permission_name = ? AND permission_id != ?";
        $stmtCheck = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($stmtCheck, "si", $name, $id);
        mysqli_stmt_execute($stmtCheck);
        mysqli_stmt_bind_result($stmtCheck, $count);
        mysqli_stmt_fetch($stmtCheck);
        mysqli_stmt_close($stmtCheck);

        if ($count > 0) {
            returnError($conn, "Tên quyền đã tồn tại!");
        }

        // Cập nhật quyền
        $updateSql = "UPDATE permission SET permission_name = ?, permission_description = ?, status_id = ? WHERE permission_id = ?";
        $stmtUpdate = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($stmtUpdate, "ssii", $name, $description, $status, $id);

        if (!mysqli_stmt_execute($stmtUpdate)) {
            returnError($conn, "Lỗi khi cập nhật quyền");
        }

        mysqli_stmt_close($stmtUpdate);
        echo json_encode(["status" => "success", "message" => "Quyền đã được cập nhật thành công!"]);
        exit;
    }

    // Cập nhật quyền cho role
    elseif (isset($_POST['updateRolePermission'])) {
        if (isset($_POST['permissions']) && !empty($_POST['permissions'])) {
            $permissions = json_decode($_POST['permissions'], true);
            $role_id = (int) $_POST['role_id']; // Ép kiểu thành int

            if ($role_id <= 0) {
                returnError($conn, "Role ID không hợp lệ!");
            }

            // Xóa các quyền cũ của role
            $deleteSql = "DELETE FROM role_permission WHERE role_id = ?";
            $stmtDelete = mysqli_prepare($conn, $deleteSql);
            mysqli_stmt_bind_param($stmtDelete, "i", $role_id);

            if (!mysqli_stmt_execute($stmtDelete)) {
                returnError($conn, "Lỗi khi xóa quyền cũ");
            }

            mysqli_stmt_close($stmtDelete);

            // Thêm các quyền mới
            $insertSql = "INSERT INTO role_permission (role_id, permission_id, action) VALUES (?, ?, 'default')";
            $stmtInsert = mysqli_prepare($conn, $insertSql);

            foreach ($permissions as $permissionId) {
                $permissionId = (int) $permissionId; // Ép kiểu thành int
                if ($permissionId <= 0) {
                    returnError($conn, "Permission ID không hợp lệ!");
                }
                mysqli_stmt_bind_param($stmtInsert, "iis", $role_id, $permissionId, "default");
                if (!mysqli_stmt_execute($stmtInsert)) {
                    returnError($conn, "Lỗi khi thêm quyền mới");
                }
            }

            mysqli_stmt_close($stmtInsert);
            echo json_encode(["status" => "success", "message" => "Quyền cho role đã được cập nhật thành công!"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Không có quyền nào được chọn!"]);
            exit;
        }
    }

    // Thêm mới quyền
    elseif (isset($_POST['permission_name'], $_POST['permission_description'], $_POST['status_id'])) {
        $permission_name = mysqli_real_escape_string($conn, $_POST['permission_name']);
        $permission_description = mysqli_real_escape_string($conn, $_POST['permission_description']);
        $status_id = (int) $_POST['status_id']; // Ép kiểu thành int

        if (!in_array($status_id, [1, 2])) {
            returnError($conn, "Status ID không hợp lệ!");
        }

        // Kiểm tra trùng lặp tên quyền
        $checkSql = "SELECT COUNT(*) FROM permission WHERE permission_name = ?";
        $stmtCheck = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($stmtCheck, "s", $permission_name);
        mysqli_stmt_execute($stmtCheck);
        mysqli_stmt_bind_result($stmtCheck, $count);
        mysqli_stmt_fetch($stmtCheck);
        mysqli_stmt_close($stmtCheck);

        if ($count > 0) {
            echo json_encode(["status" => "error", "message" => "Tên quyền đã tồn tại!"]);
            exit;
        }

        // Thêm mới quyền
        $insertSql = "INSERT INTO permission (permission_name, permission_description, status_id) VALUES (?, ?, ?)";
        $stmtInsert = mysqli_prepare($conn, $insertSql);
        mysqli_stmt_bind_param($stmtInsert, "ssi", $permission_name, $permission_description, $status_id);

        if (mysqli_stmt_execute($stmtInsert)) {
            echo json_encode(["status" => "success", "message" => "Quyền đã được thêm thành công!"]);
        } else {
            returnError($conn, "Lỗi khi thêm quyền");
        }

        mysqli_stmt_close($stmtInsert);
        exit;
    }

    // Xóa quyền (đặt status_id = 6)
    elseif (isset($_POST['permission_id']) && isset($_POST['status_id']) && (int)$_POST['status_id'] === 6) {
        $permission_id = (int) $_POST['permission_id']; // Ép kiểu thành int
        $status_id = 6;

        if ($permission_id <= 0) {
            returnError($conn, "Permission ID không hợp lệ!");
        }

        $updateSql = "UPDATE permission SET status_id = ? WHERE permission_id = ?";
        $stmtUpdate = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($stmtUpdate, "ii", $status_id, $permission_id);

        if (mysqli_stmt_execute($stmtUpdate)) {
            echo json_encode(["status" => "success", "message" => "Quyền đã được đánh dấu xóa!"]);
        } else {
            returnError($conn, "Lỗi khi xóa quyền");
        }

        mysqli_stmt_close($stmtUpdate);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Yêu cầu không hợp lệ. Thiếu tham số cần thiết."]);
        exit;
    }
}

// Lấy danh sách quyền được gán cho role
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['role_id'])) {
    $role_id = (int) $_GET['role_id']; // Ép kiểu thành int
    if ($role_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Role ID không hợp lệ!"]);
        exit;
    }

    $query = "SELECT permission_id FROM role_permission WHERE role_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $role_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $permissions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $permissions[] = $row['permission_id'];
    }

    echo json_encode($permissions);
    mysqli_stmt_close($stmt);
    exit;
}

// Trường hợp mặc định
echo json_encode(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
exit;
?>