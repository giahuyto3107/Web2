<?php
    include('../../Config/config.php');
    $id=$_GET['account_id'];
        if($_GET['status'] == 2)
            $sql_approve = "UPDATE account SET status_id = 1 WHERE account_id=".$id;
        elseif($_GET['status'] == 1)
            $sql_approve = "UPDATE account SET status_id = 2 WHERE account_id=".$id;
        mysqli_query($conn,$sql_approve);
        if (headers_sent()) {
            die("Headers already sent.");
        }
        header('Location: ../../../FrontEnd/AdminUI/index.php?action=quanlitaikhoan&query=them');
?>