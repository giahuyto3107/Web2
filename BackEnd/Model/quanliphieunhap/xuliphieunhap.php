<?php
include('../../Config/config.php');
    $id=$_GET['purchase_order_id'];
        if($_GET['status'] == 2)
            $sql_approve = "UPDATE purchase_order SET status_id = 1 WHERE purchase_order_id=".$id;
        mysqli_query($conn,$sql_approve);
        header('Location: ../../../FrontEnd/AdminUI/quanliphieunhap/lietkephieunhap.php');
?>