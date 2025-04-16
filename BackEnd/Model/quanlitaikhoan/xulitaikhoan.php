<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

// Lấy dữ liệu từ POST
$account_id = $_POST['account_id'] ?? null;
$account_name = trim($_POST['account_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$address = trim($_POST['address'] ?? ''); // Thêm trường address
$role_id = (int)($_POST['role_id'] ?? 0);
$status_id = (int)($_POST['status_id'] ?? 1);
$password = $_POST['password'] ?? null;
$date_of_birth = trim($_POST['date_of_birth'] ?? '');

// Xử lý khi có account_id (cập nhật hoặc xóa)
if ($account_id) {
    // Kiểm tra xem tài khoản có tồn tại không
    $check_id_sql = "SELECT a.account_name, a.email, u.profile_picture 
                     FROM account a 
                     LEFT JOIN user u ON a.account_id = u.account_id 
                     WHERE a.account_id = ?";
    $stmt = $conn->prepare($check_id_sql);
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Tài khoản không tồn tại!']);
        exit;
    }

    // Xử lý xóa tài khoản (cập nhật status_id thành 6)
    if (isset($_POST['status_id']) && $_POST['status_id'] == 6) {
        $sql = "UPDATE account SET status_id = ?, updated_at = NOW() WHERE account_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $status_id, $account_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Tài khoản đã được đánh dấu xóa!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi đánh dấu xóa: ' . $conn->error]);
        }
        $stmt->close();
    } else {
        // Xử lý cập nhật tài khoản
        if (empty($account_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên tài khoản không được để trống!']);
            exit;
        }

        if (empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Email không được để trống!']);
            exit;
        }

        if (empty($full_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Họ tên không được để trống!']);
            exit;
        }

        if ($role_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Chức vụ không hợp lệ!']);
            exit;
        }

        if ($status_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Trạng thái không hợp lệ!']);
            exit;
        }

        // Kiểm tra trùng lặp account_name và email
        $current_data = $result->fetch_assoc();
        $current_account_name = $current_data['account_name'];
        $current_email = $current_data['email'];

        if ($account_name !== $current_account_name || $email !== $current_email) {
            $check_sql = "SELECT account_name, email FROM account WHERE (account_name = ? OR email = ?) AND account_id != ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("ssi", $account_name, $email, $account_id);
            $stmt->execute();
            $check_result = $stmt->get_result();

            if ($check_result->num_rows > 0) {
                $row = $check_result->fetch_assoc();
                if ($row['account_name'] === $account_name) {
                    echo json_encode(['status' => 'error', 'message' => 'Tên tài khoản đã tồn tại!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Email đã tồn tại!']);
                }
                exit;
            }
        }

        // Xử lý upload ảnh đại diện (nếu có)
        $profile_picture = null;
        $current_image = $current_data['profile_picture'];

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../../BackEnd/Uploads/Profile Picture/';
            $file_name = time() . '_' . basename($_FILES['profile_picture']['name']);
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
                $profile_picture = $file_name;
                // Xóa ảnh cũ nếu có
                if ($current_image && file_exists($upload_dir . $current_image)) {
                    unlink($upload_dir . $current_image);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không thể upload ảnh đại diện!']);
                exit;
            }
        } else {
            $profile_picture = $current_image; // Giữ ảnh cũ nếu không upload ảnh mới
        }

        // Cập nhật bảng account
        $sql_account = "UPDATE account SET account_name = ?, email = ?, role_id = ?, status_id = ?, updated_at = NOW() WHERE account_id = ?";
        $stmt_account = $conn->prepare($sql_account);
        $stmt_account->bind_param("ssiii", $account_name, $email, $role_id, $status_id, $account_id);

        // Cập nhật bảng user (bao gồm address)
        $sql_user = "UPDATE user SET full_name = ?, address = ?, date_of_birth = ?, profile_picture = ?, updated_at = NOW() WHERE account_id = ?";
        $stmt_user = $conn->prepare($sql_user);
        $date_of_birth = $date_of_birth ?: null; // Nếu không có ngày sinh, đặt là NULL
        $stmt_user->bind_param("ssssi", $full_name, $address, $date_of_birth, $profile_picture, $account_id);

        // Thực thi cả hai truy vấn
        $conn->begin_transaction();
        try {
            if ($stmt_account->execute() && $stmt_user->execute()) {
                $conn->commit();
                echo json_encode(['status' => 'success', 'message' => 'Tài khoản đã được cập nhật thành công!']);
            } else {
                $conn->rollback();
                echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi cập nhật tài khoản: ' . $conn->error]);
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi cập nhật tài khoản: ' . $e->getMessage()]);
        }

        $stmt_account->close();
        $stmt_user->close();
    }
} else {
    // Xử lý thêm tài khoản mới
    if (empty($account_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Tên tài khoản không được để trống!']);
        exit;
    }

    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email không được để trống!']);
        exit;
    }

    if (empty($full_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Họ tên không được để trống!']);
        exit;
    }

    if ($role_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Chức vụ không hợp lệ!']);
        exit;
    }

    if ($status_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Trạng thái không hợp lệ!']);
        exit;
    }

    if (empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Mật khẩu không được để trống!']);
        exit;
    }

    // Kiểm tra trùng lặp account_name và email
    $check_sql = "SELECT account_name, email FROM account WHERE account_name = ? OR email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $account_name, $email);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        if ($row['account_name'] === $account_name) {
            echo json_encode(['status' => 'error', 'message' => 'Tên tài khoản đã tồn tại!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email đã tồn tại!']);
        }
        exit;
    }

    // Xử lý upload ảnh đại diện (nếu có)
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../BackEnd/Uploads/Profile Picture/';
        $file_name = time() . '_' . basename($_FILES['profile_picture']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
            $profile_picture = $file_name;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể upload ảnh đại diện!']);
            exit;
        }
    }

    // Mã hóa mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Thêm tài khoản mới vào bảng account
    $sql_account = "INSERT INTO account (account_name, email, password_hash, role_id, status_id, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt_account = $conn->prepare($sql_account);
    $stmt_account->bind_param("sssii", $account_name, $email, $hashed_password, $role_id, $status_id);

    // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
    $conn->begin_transaction();
    try {
        if ($stmt_account->execute()) {
            // Lấy account_id vừa thêm
            $new_account_id = $conn->insert_id;

            // Thêm thông tin vào bảng user (bao gồm address)
            $sql_user = "INSERT INTO user (full_name, address, account_id, profile_picture, date_of_birth, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt_user = $conn->prepare($sql_user);
            $date_of_birth = $date_of_birth ?: null; // Nếu không có ngày sinh, đặt là NULL
            $stmt_user->bind_param("ssiss", $full_name, $address, $new_account_id, $profile_picture, $date_of_birth);

            if ($stmt_user->execute()) {
                $conn->commit();
                echo json_encode(['status' => 'success', 'message' => 'Tài khoản đã được thêm thành công!']);
            } else {
                $conn->rollback();
                echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi thêm thông tin người dùng: ' . $conn->error]);
            }

            $stmt_user->close();
        } else {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi thêm tài khoản: ' . $conn->error]);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi thêm tài khoản: ' . $e->getMessage()]);
    }

    $stmt_account->close();
}

// Đóng kết nối
$conn->close();
?>