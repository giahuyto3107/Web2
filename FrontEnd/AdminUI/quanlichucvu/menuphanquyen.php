<dialog data-modal id="permission-modal">
    <div class="modal-header">
        <h2>Phân quyền cho chức vụ: <span id="modal-role-name">N/A</span></h2>
        <button class="modal-close" data-id="permission-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form id="modal-permission-form" class="modal-form">
            <input type="hidden" id="modal-role-id" name="role_id">
            <div class="checkbox-group" id="permissions-list">
                <!-- Danh sách quyền sẽ được thêm bằng JavaScript -->
            </div>
            <div class="modal-buttons">
                <button type="button" class="close" id="permission-close-button">Hủy</button>
                <button type="submit" class="save">Lưu</button>
            </div>
        </form>
    </div>
</dialog>
