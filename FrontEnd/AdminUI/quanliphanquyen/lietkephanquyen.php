<?php
    $sql_permission = "SELECT p.permission_id,
                    p.permission_name,
                    p.permission_description,
                    st.status_name
                from permission p
                join status st on st.id = p.status_id
                ORDER BY p.permission_id ASC";

    $query_permission = mysqli_query($conn, $sql_permission);
    if (!$query_permission) {
        die("Query failed: " . mysqli_error($conn));
    }
?>

<div class="form">
    <div class="form-title">
        <h2>Quản lý phân quyền</h2>
    </div>

    <div class="form-content">
        <table>
            <tr>
                <th>STT</th>
                <th>Tên phân quyền</th>
                <th>Mô tả</th>
                <th>Trạng thái  </th>
                <th>Quản lý</th>
            </tr>

            <?php
                while($row = mysqli_fetch_array($query_permission)) {
                    ;
            ?>

            <tr>
                <td><?= $row['permission_id'] ?></td>
                <td><?= $row['permission_name'] ?></td>
                <td><?= $row['permission_description'] ?></td>
                <td><?= $row['status_name'] ?></td>
                <td>
                    <?php 
                        // if ($row['status_id'] == 1) {
                        //     echo '<a class="active" href="../../BackEnd/Model/quanlitaikhoan/xulitaikhoan.php?account_id=' . $row['account_id'] . '&status=' . $row['status_id'] . '">Vô hiệu hóa</a>';                        
                        // } else if ($row['status_id'] == 2) {
                        //     echo '<a class="inactive" href="../../BackEnd/Model/quanlitaikhoan/xulitaikhoan.php?account_id=' . $row['account_id'] . '&status=' . $row['status_id'] . '">Khôi phục</a>';
                        // }
                    ?> 
                    <a class ="edit" href="index.php?action=quanliphanquyen&query=sua&id=<?= $row['permission_id'] ?>">Sửa</a>      
                </td>
            </tr>

            <?php
                }
            ?>
        </table>
    </div>

</div>


<style>

    body {
            background-color: #f8f9fa;
            display: flex;
            /* min-height: 100vh; */
            padding: 20px;
        }
    
    .form {
        width: 100%;
        background: white;
        padding: 50px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .form-title {
        width: auto;
        text-align: center;
    }

    .form-content {
        background-color: white;
        border-radius: 20px;
        width: 100%;
        margin: 10px;
        padding: 20px;
    }

    table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
    }
    tr, th {
        border: 1px solid #ccc;
        
    }

    th {
        background-color: #f2f2f2;
    }

    td {
        background-color: white;
        text-align: center;
    }

    .profile_picture {
        width: 200px;
        height: 200px;
    }

    .active, .inactive, .edit {
        display: inline-block;        /* Để có thể áp dụng padding và border */
        padding: 5px 10px;           /* Khoảng cách bên trong */
        text-decoration: none;        /* Bỏ gạch chân */
        color: white;                 /* Màu chữ trắng */
        border: 1px solid black;      /* Khung bên ngoài màu đen */
        border-radius: 15px;          /* Bo góc nhẹ */
        transition: background-color 0.3s; /* Hiệu ứng chuyển màu nền */
        padding: 6px 12px;
    }

    .inactive, .active, .edit {
        cursor: pointer;
    }

    .active:hover, .inactive:hover {
        opacity: 0.8;                /* Hiệu ứng giảm độ trong suốt khi hover */
    }

    .active {
        background: #28a745; /* Green for approved */
        color: white;
    }

    .active:hover {
        background: #218838;
    }

    .inactive {
        background-color: #008cba;   /* Màu xanh lam cho khôi phục */
    }

    .edit {
        /* border: 1px black solid; */
        background: #007bff; /* Gray */
        color: white;
        font-weight: bold;
    }

    .edit:hover {
        background: #0056b3;
    }
</style>