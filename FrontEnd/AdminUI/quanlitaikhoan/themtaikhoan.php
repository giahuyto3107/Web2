<dialog data-modal id="add-modal">
    <div class="modal-header">
        <h2>Thêm Tài Khoản</h2>
        <button class="modal-close" data-id="add-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form id="modal-add-form" class="modal-form" enctype="multipart/form-data">
            <div class="modal-input">
                <span>Tên tài khoản</span>
                <input type="text" id="modal-add-account-name" name="account_name" required />
                <p class="modal-error" id="modal-add-account-name-error"></p>
            </div>
            <div class="modal-input">
                <span>Email</span>
                <input type="email" id="modal-add-email" name="email" required />
                <p class="modal-error" id="modal-add-email-error"></p>
            </div>
            <div class="modal-input">
                <span>Mật khẩu</span>
                <input type="password" id="modal-add-password" name="password" required />
                <p class="modal-error" id="modal-add-password-error"></p>
            </div>
            <div class="modal-input">
                <span>Họ tên</span>
                <input type="text" id="modal-add-full-name" name="full_name" required />
                <p class="modal-error" id="modal-add-full-name-error"></p>
            </div>
            <div class="modal-input">
                <span>Chức vụ</span>
                <select id="modal-add-role-id" name="role_id" required>
                    <?php
                    // Lấy danh sách chức vụ từ bảng role
                    include("../../../BackEnd/Config/config.php");
                    $sql_roles = "SELECT id, role_name FROM role WHERE status_id != 6";
                    $query_roles = mysqli_query($conn, $sql_roles);
                    while ($role = mysqli_fetch_assoc($query_roles)) {
                        echo "<option value='{$role['id']}'>{$role['role_name']}</option>";
                    }
                    mysqli_close($conn);
                    ?>
                </select>
                <p class="modal-error" id="modal-add-role-id-error"></p>
            </div>
            <div class="modal-input">
                <span>Trạng thái</span>
                <select id="modal-add-status" name="status_id" required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error" id="modal-add-status-error"></p>
            </div>
            <div class="modal-input">
                <span>Ngày sinh</span>
                <input type="date" id="modal-add-date-of-birth" name="date_of_birth" />
                <p class="modal-error" id="modal-add-date-of-birth-error"></p>
            </div>
            <div class="modal-input">
                <span>Ảnh đại diện</span>
                <input type="file" id="modal-add-profile-picture" name="profile_picture" accept="image/*" />
                <p class="modal-error" id="modal-add-profile-picture-error"></p>
            </div>
            <div class="modal-buttons">
                <button class="close" id="add-close-button">Hủy</button>
                <button type="submit" class="save">Lưu</button>
            </div>
        </form>
    </div>
</dialog>