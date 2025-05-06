<dialog data-modal id="add-modal">
    <div class="modal-header">
        <h2>Thêm Thể Loại</h2>
        <a class="modal-close" data-id="add-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </a>
    </div>
    <div>
        <form class="modal-form" id="modal-add-form" method="post">
            <label for="modal-add-name">
                <span>Tên Thể Loại</span>
                <input type="text" id="modal-add-name" name="category_name" required />
                <p class="modal-error" id="modal-add-name-error"></p>
            </label>
            <label for="modal-add-desc" class="modal-desc-label">
                <span>Mô tả</span>
                <textarea id="modal-add-desc" name="category_description" rows="5" maxlength="400">Nhập mô tả ở đây...</textarea>
                <p class="modal-error" id="modal-add-desc-error"></p>
            </label>
            <label for="modal-add-category-type">
                <span>Chủng Loại</span>
                <select id="modal-add-category-type" name="category_type_id" required>
                    <option value="">Chọn chủng loại</option>
                    <!-- Danh sách chủng loại sẽ được điền bằng JavaScript -->
                </select>
                <p class="modal-error" id="modal-add-category-type-error"></p>
            </label>
            <label for="modal-add-status">
                <span>Trạng Thái</span>
                <select id="modal-add-status" name="status_id" required>
                    <option value="">Chọn trạng thái</option>
                    <option value="1">Hoạt động</option>
                    <option value="2">Không hoạt động</option>
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