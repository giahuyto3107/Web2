<?php
include('../../../BackEnd/Config/config.php');
session_start();


if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Phiên đăng nhập không hợp lệ"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $account_id = $_SESSION['user_id'];


    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = $_POST['address'] ?? '';


    $target_dir = "../../../BackEnd/Uploads/Profile Picture/";
    $profile_picture_path = null; 


    if (!is_dir($target_dir) || !is_writable($target_dir)) {
        echo json_encode(["status" => "error", "message" => "Thư mục upload không tồn tại hoặc không có quyền ghi"]);
        exit();
    }

    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $profile_picture = $_FILES['profilePicture'];
        

        if ($profile_picture['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(["status" => "error", "message" => "Lỗi upload file: " . $profile_picture['error']]);
            exit();
        }

        $max_file_size = 5 * 1024 * 1024; // 5MB
        if ($profile_picture['size'] > $max_file_size) {
            echo json_encode(["status" => "error", "message" => "File quá lớn, kích thước tối đa là 5MB"]);
            exit();
        }


        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($profile_picture['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            echo json_encode(["status" => "error", "message" => "Chỉ cho phép các định dạng: " . implode(', ', $allowed_extensions)]);
            exit();
        }


        $file_name = "avatar" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;


        if (move_uploaded_file($profile_picture['tmp_name'], $target_file)) {
            $profile_picture_path = $file_name;
        } else {
            echo json_encode(["status" => "error", "message" => "Không thể di chuyển file upload"]);
            exit();
        }
    }

    $conn->begin_transaction();

    try {

        $sql1 = "UPDATE account SET email=? WHERE account_id=?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("si", $email, $account_id);
        $stmt1->execute();
        $stmt1->close();


        if ($profile_picture_path) {
            $sql = "UPDATE user SET full_name=?, date_of_birth=?, address=?, profile_picture=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $fullName, $dob, $address, $profile_picture_path, $user_id);
        } else {
            $sql = "UPDATE user SET full_name=?, date_of_birth=?, address=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $fullName, $dob, $address, $user_id);
        }

        if ($stmt->execute()) {
            $conn->commit();
            echo json_encode(["status" => "success", "message" => "Cập nhật thành công"]);
        } else {
            throw new Exception("Lỗi khi cập nhật dữ liệu");
        }

        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

    $conn->close();
}
?>