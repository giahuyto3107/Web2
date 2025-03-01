
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm phân quyền</title>
</head>
<body>
    <div class="container">
        <h2>Thêm phân quyền</h2>
        <form method="POST" action="../../BackEnd/Model/quanliphanquyen/xuliphanquyen.php">
            <div class="form-group">
                <label for="permission_name">Tên quyền</label>
                <input type="text" id="permission_name" name="permission_name" required>
            </div>
            <div class="form-group">
                <label for="permission_description">Mô tả quyền</label>
                <textarea id="permission_description" name="permission_description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="status_id">Trạng thái</label>
                <select id="status_id" name="status_id" required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="them_phanquyen">Thêm quyền</button>
            </div>
        </form>
    </div>
</body>
</html>