<?php
include('../../Config/config.php');

// URL chuyển hướng
$redirect_url = "../../../Frontend/AdminUI/index.php?action=quanlitaikhoan&query=them";

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        // Xử lý thêm mới tài khoản
        if ($_POST['action'] == 'add') {
            // Lấy dữ liệu từ form
            $account_name = mysqli_real_escape_string($conn, $_POST['account_name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
            $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : NULL;
            $status_id = $_POST['status_id'];
            $role_id = $_POST['role_id'];
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            // Xử lý upload ảnh
            $profile_picture = NULL;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                $target_dir = "../../Uploads/Profile Picture/";
                $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;

                // Kiểm tra loại file và kích thước (ví dụ: chỉ cho phép ảnh dưới 5MB)
                $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
                if (in_array($file_extension, $allowed_types) && $_FILES["profile_picture"]["size"] <= 5000000) {
                    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                        $profile_picture = $new_filename;
                    }
                }
            }

            // Kiểm tra email đã tồn tại chưa
            $check_email = "SELECT * FROM account WHERE email = '$email'";
            $result = mysqli_query($conn, $check_email);

            if (mysqli_num_rows($result) > 0) {
                echo "<script>alert('Email đã tồn tại!'); window.location='$redirect_url';</script>";
            } else {
                // Thêm vào bảng account trước
                $sql_account = "INSERT INTO account (account_name, email, password_hash, status_id, role_id, created_at, updated_at) 
                               VALUES ('$account_name', '$email', '$password', '$status_id', '$role_id', '$created_at', '$updated_at')";

                if (mysqli_query($conn, $sql_account)) {
                    $account_id = mysqli_insert_id($conn); // Lấy ID vừa tạo

                    // Thêm vào bảng user
                    $sql_user = "INSERT INTO user (full_name, account_id, profile_picture, date_of_birth, created_at, updated_at) 
                                 VALUES ('$full_name', '$account_id', " . ($profile_picture ? "'$profile_picture'" : "NULL") . ", 
                                 " . ($date_of_birth ? "'$date_of_birth'" : "NULL") . ", '$created_at', '$updated_at')";

                    if (mysqli_query($conn, $sql_user)) {
                        echo "<script>alert('Thêm tài khoản thành công!'); window.location='$redirect_url';</script>";
                    } else {
                        // Nếu thêm user thất bại, xóa account vừa tạo để đảm bảo đồng bộ
                        mysqli_query($conn, "DELETE FROM account WHERE account_id = '$account_id'");
                        echo "<script>alert('Có lỗi khi thêm thông tin user: " . mysqli_error($conn) . "'); window.location='$redirect_url';</script>";
                    }
                } else {
                    echo "<script>alert('Có lỗi khi thêm tài khoản: " . mysqli_error($conn) . "'); window.location='$redirect_url';</script>";
                }
            }
        }
        // Xử lý cập nhật thông tin tài khoản
        elseif ($_POST['action'] == 'update') {
            // Lấy dữ liệu từ form
            $id = mysqli_real_escape_string($conn, $_POST['id']);
            $acc = mysqli_real_escape_string($conn, $_POST['acc']);
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $dob = mysqli_real_escape_string($conn, $_POST['dob']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $role = mysqli_real_escape_string($conn, $_POST['role']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);

            // Xử lý upload ảnh
            $pro5Image = $_FILES['pro5Image']['name'];
            $pro5Image_tmp = $_FILES['pro5Image']['tmp_name'];
            $imageUpdate = "";

            if (!empty($pro5Image)) {
                move_uploaded_file($pro5Image_tmp, '../../Uploads/Profile Picture/' . $pro5Image);
                $imageUpdate = ", profile_picture = '$pro5Image'";

                // Xóa hình ảnh cũ
                $sql = "SELECT * FROM user WHERE user_id = '$id' LIMIT 1";
                $query = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($query)) {
                    unlink('../../Uploads/Profile Picture/' . $row['profile_picture']);
                }
            }

            // Query để lấy role_id từ role_name
            $roleQuery = "SELECT id FROM role WHERE role_name = '$role'";
            $roleResult = mysqli_query($conn, $roleQuery);

            if ($roleRow = mysqli_fetch_assoc($roleResult)) {
                $role_id = $roleRow['id']; // Lấy role_id

                // Cập nhật bảng account
                $sql_updateAcc = "UPDATE account 
                                 SET account_name = '$acc', email = '$email', status_id = $status, role_id = $role_id 
                                 WHERE account_id = $id";

                // Cập nhật bảng user
                $sql_updateUser = "UPDATE user 
                                  SET full_name = '$name', date_of_birth = '$dob' $imageUpdate
                                  WHERE account_id = $id";

                // Thực thi các câu lệnh SQL
                if (mysqli_query($conn, $sql_updateAcc) && mysqli_query($conn, $sql_updateUser)) {
                    echo "<script>alert('Cập nhật thông tin thành công!'); window.location='$redirect_url';</script>";
                } else {
                    echo "<script>alert('Có lỗi khi cập nhật thông tin: " . mysqli_error($conn) . "'); window.location='$redirect_url';</script>";
                }
            } else {
                echo "<script>alert('Không tìm thấy role!'); window.location='$redirect_url';</script>";
            }
        }
    } else {
        echo "<script>alert('Hành động không hợp lệ!'); window.location='$redirect_url';</script>";
    }
} else {
    header("Location: $redirect_url");
    exit();
}
?>