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
        <button type="button" class ="save" id="savePermissions" name="updateRolePermission">Lưu</button>          
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
    });
});


</script>

<style>
    body {
        background-color: #f8f9fa;
        display: flex;
        /* min-height: 100vh; */
        padding: 20px;
        }
    
    .container {
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

    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        /* justify-content: space-between; */
        width: 100%;
        height: auto;
        align-items: flex-start;
        gap: 10px; /* Space between items */
    }

    .item {
        display: flex;
        gap: 5px;
        width: calc(100% / 4 - 10px); /* 4 columns per row */
        align-items: center;
    }

    input, label {
        cursor: pointer;
    }

    input[type="checkbox"] {
        width: 16px;
        height: 16px;
        border: 2px solid teal;
        border-radius: 4px;
    }

    .button-container{
        width: 100%;
    display: flex;
    justify-content: center; /* Centers the button */
    margin-top: 20px;
    }

    .save {
        display: inline-block;        /* Để có thể áp dụng padding và border */
        padding: 5px 10px;           /* Khoảng cách bên trong */
        text-decoration: none;        /* Bỏ gạch chân */
        color: white;                 /* Màu chữ trắng */
        border: 1px solid black;      /* Khung bên ngoài màu đen */
        border-radius: 15px;          /* Bo góc nhẹ */
        transition: background-color 0.3s; /* Hiệu ứng chuyển màu nền */
        padding: 12px 20px;
        background: #007bff; /* Gray */
        color: white;
        font-weight: bold;
        cursor: pointer;
        text-align: center;
        border: none;
    }

    .save:hover {
        background: #0056b3;
    }
</style>