<?php
    $sql_role = "SELECT r.id,
                    r.role_name,
                    r.role_description,
                    st.status_name
                from role r
                join status st on st.id = r.status_id
                ORDER BY r.id ASC";

    $query_role = mysqli_query($conn, $sql_role);
    if (!$query_role) {
        die("Query failed: " . mysqli_error($conn));
    }
?>

<div class="form">
    <div class="form-title">
        <h2>Quản lý chức vụ</h2>
    </div>

    <div class="form-content">
        <table>
            <tr>
                <th>STT</th>
                <th>Tên chức vụ</th>
                <th>Mô tả</th>
                <th>Trạng thái  </th>
                <th>Quản lý</th>
            </tr>

            <?php
                while($row = mysqli_fetch_array($query_role)) {
                    ;
            ?>

            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['role_name'] ?></td>
                <td><?= $row['role_description'] ?></td>
                <td><?= $row['status_name'] ?></td>
                <td>
                    <a class ="edit" href="index.php?action=quanlichucvu&query=sua&id=<?= $row['id'] ?>">Sửa</a>      
                    <a class ="edit" href="index.php?action=quanlichucvu&query=menu&id=<?= $row['id'] ?> &name=<?= $row['role_name'] ?>">Menu</a>      
                </td>
            </tr>

            <?php
                }
            ?>
        </table>
    </div>

</div>


