<?php
    $role_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($role_id == 0) {
        die("Invalid role_id.");
    }

    $sql_role_permission = "  SELECT r.id,
                    r.role_name,
                    r.role_description,
                    p.permission_name,
                    st.status_name
                    from role r
                    join status st on st.id = r.status_id
                    join role_permission rp on rp.role_id = r.id
                    join permission p on p.permission_id = rp.permission_id
                    WHERE r.id = ?";

    $stmt = $conn->prepare($sql_role_permission);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $query_role_permission = $stmt->get_result();

    if (!$query_role_permission) {
        die("Query failed: " . $stmt->error);
    }
?>

<div class="container">
    <div class="checkbox-group">
        <?php
            while ($row = mysqli_fetch_array($query_role_permission)) {

        ?>
        <h3>Phân quyên cho chức vụ <?= $row['role_name'] ?></h3>
            
        <?php
            }
        ?>
    </div>
</div>