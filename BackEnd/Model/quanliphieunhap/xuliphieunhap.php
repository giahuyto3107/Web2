<?php
include('../../Config/config.php');
    $id=$_GET['purchase_order_id'];
        if($_GET['status'] == 2)
            $sql_approve = "UPDATE purchase_order SET status_id = 1 WHERE purchase_order_id=".$id;
        mysqli_query($conn,$sql_approve);
        if (headers_sent()) {
            die("Headers already sent.");
        }
        header('Location: ../../../FrontEnd/AdminUI/index.php?action=quanliphieunhap&query=them');
?>