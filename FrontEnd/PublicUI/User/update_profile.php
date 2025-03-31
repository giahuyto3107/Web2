<?php
include ('../../../BackEnd/Config/config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //$user_id  = $_SESSION['user_id']; 
    //$account_id  = $_SESSION['account_id']; 
    $user_id = $_SESSION['user_id'];
    // $user_id = 1;
    $fullName = $_POST['fullName'];
    $email    = $_POST['email'];
    $dob      = $_POST['dob'];

    $sql1 = "UPDATE account SET email=? WHERE account_id=?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("si", $email, $account_id);
    $stmt1->execute();
    $stmt1->close();

    $sql = "UPDATE user SET full_name=?, date_of_birth=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullName, $dob, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi cập nhật dữ liệu"]);
    }

    $stmt->close();
    $conn->close();
}
?>
