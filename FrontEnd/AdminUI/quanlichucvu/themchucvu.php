<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Chức Vụ</title>
</head>
<body>
    <h1>Thêm Chức Vụ</h1>
    <form method="post" action="">
        <label for="role_name">Tên Chức Vụ:</label>
        <input type="text" name="role_name" required><br>

        <label for="role_description">Mô Tả:</label>
        <textarea name="role_description" required></textarea><br>

        <label for="status_id">Trạng Thái:</label>
        <select name="status_id">
            <option value="1">Hoạt Động</option>
            <option value="0">Không Hoạt Động</option>
        </select><br>

        <h3>Chọn Permission:</h3>
        <?php
        // Lấy danh sách permission từ bảng permission
        $result = $conn->query("SELECT permission_id, permission_name FROM permission WHERE status_id = 1");
        while ($row = $result->fetch_assoc()) {
            echo '<input type="checkbox" name="permissions[]" value="' . $row['permission_id'] . '"> ' . $row['permission_name'] . '<br>';
        }
        ?>

        <input type="submit" value="Thêm Chức Vụ">
    </form>
</body>
</html>
