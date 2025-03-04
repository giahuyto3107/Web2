<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm chức vụ</title>
</head>
<body>
    <h2>Thêm chức vụ</h2>
    <form method="POST" action="../../BackEnd/Model/quanlichucvu/xulichucvu.php">
        <div>
            <label for="role_name">Tên chức vụ:</label>
            <input type="text" id="role_name" name="role_name" required>
        </div>
        <div>
            <label for="role_description">Mô tả:</label>
            <textarea id="role_description" name="role_description" required></textarea>
        </div>
        <div>
            <label for="status_id">Trạng thái:</label>
            <select id="status_id" name="status_id" required>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
            </select>
        </div>
        <div>
            <h3>Chọn Permission:</h3>
            <?php
            // Lấy danh sách các quyền có status_id = 1
            $result = $conn->query("SELECT permission_id, permission_name FROM permission WHERE status_id = 1");
            while ($row = $result->fetch_assoc()) {
                echo '<input type="checkbox" name="permissions[]" value="' . $row['permission_id'] . '"> ' . $row['permission_name'] . '<br>';
            }
            ?>
        </div>
        <div>
            <button type="submit" name="themchucvu">Thêm chức vụ</button>
        </div>
    </form>
</body>
</html>