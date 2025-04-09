<dialog data-modal id="add-modal">
    <div class="modal-header">
        <h2>Thêm Chủng Loại</h2>
        <a class="modal-close" data-id="add-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </a>
    </div>
    <div>
        <form class="modal-form" id="modal-add-form" method="post">
            <input type="hidden" name="action" value="add" />
            <label for="modal-add-name">
                <span>Tên Chủng Loại</span>
                <input type="text" id="modal-add-name" name="type_name" required />
                <p class="modal-error" id="modal-add-name-error"></p>
            </label>
            <label for="modal-add-desc" class="modal-desc-label">
                <span>Mô tả</span>
                <textarea id="modal-add-desc" name="type_description" rows="5" maxlength="400">Nhập mô tả ở đây...</textarea>
                <p class="modal-error" id="modal-add-desc-error"></p>
            </label>
            <label for="modal-add-status">
                <span>Trạng thái</span>
                <select id="modal-add-status" name="status_id" required>
                    <option value="">Chọn trạng thái</option>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error" id="modal-add-status-error"></p>
            </label>
            <div class="modal-buttons">
                <button type="button" class="cancel" formmethod="dialog" id="add-close-button">Hủy</button>
                <button type="submit" class="submit">Thêm</button>
            </div>
        </form>
    </div>
</dialog>