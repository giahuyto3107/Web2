<dialog data-modal id="edit-modal">
    <div class="modal-header">
        <h2>Chỉnh Sửa Chủng Loại</h2>
        <a class="modal-close" data-id="edit-modal" id="edit-close-button-icon">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </a>
    </div>
    <div>
        <form class="modal-form" id="modal-edit-form" method="post">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" id="modal-edit-category-type-id" name="category_type_id" />
            <label for="modal-edit-name">
                <span>Tên Chủng Loại</span>
                <input type="text" id="modal-edit-name" name="type_name" required />
                <p class="modal-error" id="modal-edit-name-error"></p>
            </label>
            <label for="modal-edit-desc" class="modal-desc-label">
                <span>Mô tả</span>
                <textarea id="modal-edit-desc" name="type_description" rows="5"></textarea>
                <p class="modal-error" id="modal-edit-desc-error"></p>
            </label>
            <label for="modal-edit-status">
                <span>Trạng thái</span>
                <select id="modal-edit-status" name="status_id" required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error" id="modal-edit-status-error"></p>
            </label>
            <div class="modal-buttons">
                <button type="button" class="cancel" id="edit-close-button">Hủy</button>
                <button type="submit" class="submit">Cập nhật</button>
            </div>
        </form>
    </div>
</dialog>