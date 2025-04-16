<dialog data-modal id="edit-modal">
    <div class="modal-header">
        <h2>Chỉnh sửa Tài Khoản</h2>
        <button class="modal-close" data-id="edit-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form id="modal-edit-form" class="modal-form" enctype="multipart/form-data">
            <input type="hidden" id="modal-edit-account-id" name="account_id" />
            <div class="modal-input">
                <span>Tên tài khoản</span>
                <input type="text" id="modal-edit-account-name" name="account_name" required />
                <p class="modal-error" id="modal-edit-account-name-error"></p>
            </div>
            <div class="modal-input">
                <span>Email</span>
                <input type="email" id="modal-edit-email" name="email" required />
                <p class="modal-error" id="modal-edit-email-error"></p>
            </div>
            <div class="modal-input">
                <span>Họ tên</span>
                <input type="text" id="modal-edit-full-name" name="full_name" required />
                <p class="modal-error" id="modal-edit-full-name-error"></p>
            </div>
            <div class="modal-input">
                <span>Địa chỉ</span>
                <input type="text" id="modal-edit-address" name="address" required />
                <p class="modal-error" id="modal-edit-address-error"></p>
            </div>
            <div class="modal-input">
                <span>Chức vụ</span>
                <select id="modal-edit-role-id" name="role_id" required>
                    <option value="">Chọn chức vụ</option>
                </select>
                <p class="modal-error" id="modal-edit-role-id-error"></p>
            </div>
            <div class="modal-input">
                <span>Trạng thái</span>
                <select id="modal-edit-status" name="status_id" required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error" id="modal-edit-status-error"></p>
            </div>
            <div class="modal-input">
                <span>Ngày sinh</span>
                <input type="date" id="modal-edit-date-of-birth" name="date_of_birth" />
                <p class="modal-error" id="modal-edit-date-of-birth-error"></p>
            </div>
            <div class="modal-input">
                <span>Ảnh đại diện</span>
                <input type="file" id="modal-edit-profile-picture" name="profile_picture" accept="image/*" />
                <p class="modal-error" id="modal-edit-profile-picture-error"></p>
                <img id="modal-edit-profile-picture-preview" style="display: none; max-width: 100px; margin-top: 10px;" alt="Preview">
            </div>
            <div class="modal-buttons">
                <button class="close" id="edit-close-button">Hủy</button>
                <button type="submit" class="save">Lưu</button>
            </div>
        </form>
    </div>
</dialog>