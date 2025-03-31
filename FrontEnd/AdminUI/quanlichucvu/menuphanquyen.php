<?php
    $role_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $role_name = isset($_GET['name']) ? $_GET['name'] : '';

    if ($role_id == 0) {
        die("Invalid role_id.");
    }

    $sql_role_permission = "  SELECT permission_id, permission_name FROM permission";

    $query_role_permission = mysqli_query($conn, $sql_role_permission);

    if (!$query_role_permission) {
        die("Query failed: " . mysqli_error($conn));
    }
    
?>

<div class="container">
    <div class="form-title">
        <h3>Phân quyền cho chức vụ <?= $role_name ?></h3>
    </div> 
    <div class="checkbox-group">        
        <?php
            while ($row = mysqli_fetch_array($query_role_permission)) {
        ?>

        <div class="item">
            <input type="checkbox" name="permissions[]" value="<?= $row['permission_id'] ?>" id="<?= $row['permission_id'] ?>">
            <label for="<?= $row['permission_id'] ?>"><?= $row['permission_name'] ?></label>
        </div>

        <?php
            }
        ?>

        <!-- <a class ="save" href="index.php?action=quanlichucvu&query=sua&id=<?= $row['id'] ?>">Sửa</a>       -->
        
    </div>

    <div class="button-container">
        <button type="button" class ="save" id="savePermissions" name="updateRolePermission" href="../../BackEnd/quanliphieunhap/xuliphieunhap.php">Lưu</button>          
    </div>
    
</div>

<script>

document.addEventListener("DOMContentLoaded", function () {
    let roleId = "<?= $role_id ?>"; // Role ID from PHP

    function fetchPermissions() {
        fetch(`../../BackEnd/Model/quanliphanquyen/xuliphanquyen.php?role_id=${roleId}`)
            .then(response => response.json())
            .then(data => {
                let assignedPermissions = new Set(data.map(Number)); // Convert to numbers

                document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                    checkbox.checked = assignedPermissions.has(Number(checkbox.value));
                });
            })
            .catch(error => console.error('Error fetching permissions:', error));
    }

    // Load permissions when page loads
    fetchPermissions();

    document.getElementById("savePermissions").addEventListener("click", function () {
        let checkedPermissions = [];
        
        document.querySelectorAll('input[name="permissions[]"]:checked').forEach(checkbox => {
            checkedPermissions.push(checkbox.value);
        });

        let formData = new FormData();
        formData.append("updateRolePermission", "1"); // Manually add flag
        formData.append("permissions", JSON.stringify(checkedPermissions));  
        formData.append("role_id", roleId);

        fetch("../../BackEnd/Model/quanliphanquyen/xuliphanquyen.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            fetchPermissions(); // Refresh checkboxes after update
        })
        .catch(error => console.error("Error updating permissions:", error));
        window.location.reload();
    });
});


</script>
