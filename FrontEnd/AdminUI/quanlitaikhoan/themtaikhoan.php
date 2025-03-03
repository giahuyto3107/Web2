<!DOCTYPE html>
<html>
<head>
    <title>Thêm tài khoản</title>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Thêm mới tài khoản</h2>
    <form action="../../BackEnd/Model/quanlitaikhoan/xulitaikhoan.php" method="POST" enctype="multipart/form-data">
        <div>
            <label>Tên tài khoản:</label><br>
            <input type="text" name="account_name" required>
        </div>
        <div>
            <label>Email:</label><br>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Mật khẩu:</label><br>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Họ tên:</label><br>
            <input type="text" name="full_name" required>
        </div>
        <div>
            <label>Ngày sinh:</label><br>
            <input type="date" name="date_of_birth">
        </div>
        <div>
            <label>Ảnh đại diện:</label><br>
            <input type="file" name="profile_picture" accept="image/">
        </div>
        <div>
            <label>Trạng thái:</label><br>
            <select name="status_id" required>
                <option value="1">Hoạt động</option>
                <option value="0">Khóa</option>
            </select>
        </div>
        <div>
            <label>Vai trò:</label><br>
            <select name="role_id" required>
                <?php
                $sql = "SELECT * FROM role WHERE status_id = 1";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['id']}'>{$row['role_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <input type="hidden" name="action" value="add">
            <input type="submit" value="Thêm tài khoản">
        </div>
    </form>
</body>
</html>