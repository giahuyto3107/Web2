<?php
include ('../../../BackEnd/Config/config.php');

if (isset($_POST['capnhat_feedback'])) {
    $review_id = $_POST['review_id']; 
    $feedback = $_POST['feedback']; 
    if (empty($review_id)) {
        echo "Lỗi: Không có ID bình luận!";
        exit;
    }
    $sql_update = "UPDATE review SET feedback = '$feedback' WHERE review_id = '$review_id'";
    if (mysqli_query($conn, $sql_update)) {
        header('Location: ../../../Frontend/AdminUI/index.php?action=quanlibinhluan&query=them');
        exit;
    } else {

        echo "Lỗi: " . mysqli_error($conn);
    }
} else {
    $review_id = $_GET['review_id']; 
    $status_id = $_GET['status_id']; 
    $sql_update_status = "UPDATE review SET status_id='$status_id' WHERE review_id='$review_id'";
    mysqli_query($conn, $sql_update_status);
    header('Location: ../../../Frontend/AdminUI/index.php?action=quanlibinhluan&query=them');
    exit;
}