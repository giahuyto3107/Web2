<?php
include ('../../../BackEnd/Config/config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id    = $_SESSION['user_id'];
    $account_id = $_SESSION['user_id']; // <- Bị thiếu trong bản của bạn

    $fullName = $_POST['fullName'];
    $email    = $_POST['email'];
    $dob      = $_POST['dob'];
    $address  = $_POST['address'];

    // Cập nhật email trong bảng account
    $sql1 = "UPDATE account SET email=? WHERE account_id=?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("si", $email, $account_id);
    $stmt1->execute();
    $stmt1->close();

    // Cập nhật thông tin user (bao gồm address)
    $sql = "UPDATE user SET full_name=?, date_of_birth=?, address=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $fullName, $dob, $address, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi cập nhật dữ liệu"]);
    }

    $stmt->close();
    $conn->close();
}
?>
