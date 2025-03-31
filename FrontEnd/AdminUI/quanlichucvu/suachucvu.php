<dialog data-modal id="edit-modal">
    <div class="modal-header">
        <h2>Chỉnh sửa Chức Vụ</h2>
        <button class="modal-close" data-id="edit-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form id="modal-edit-form" class="modal-form">
            <input type="hidden" id="modal-edit-role-id" name="id" />
            <div class="modal-input">
                <span>Tên chức vụ</span>
                <input type="text" id="modal-edit-name" name="role_name" required />
                <p class="modal-error" id="modal-edit-name-error"></p>
            </div>
            <div class="modal-input">
                <span>Mô tả</span>
                <textarea id="modal-edit-description" name="role_description" required></textarea>
                <p class="modal-error" id="modal-edit-description-error"></p>
            </div>
            <div class="modal-input">
                <span>Trạng thái</span>
                <select id="modal-edit-status" name="status_id" required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error" id="modal-edit-status-error"></p>
            </div>
            <div class="modal-buttons">
                <button class="close" id="edit-close-button">Hủy</button>
                <button type="submit" class="save">Lưu</button>
            </div>
        </form>
    </div>
</dialog>