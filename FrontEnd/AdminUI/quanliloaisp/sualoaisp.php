<dialog data-modal id="edit-modal">
    <div class="modal-header">
        <h2>Edit Category</h2>
        <a class="modal-close" data-id="edit-modal" id="edit-close-button-icon">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </a>
    </div>
    <div>
        <form class="modal-form" id="modal-edit-form" method="post">
            <input type="hidden" id="modal-edit-category-id" name="category_id" /> <!-- Ẩn để lưu category_id -->
            <label for="modal-edit-name">
                <span>Name</span>
                <input type="text" id="modal-edit-name" name="category_name" required />
                <p class="modal-error" id="modal-edit-name-error"></p>
            </label>
            <label for="modal-edit-desc" class="modal-desc-label">
                <span>Description</span>
                <textarea id="modal-edit-desc" name="category_description" rows="5"></textarea>
                <p class="modal-error" id="modal-edit-desc-error"></p>
            </label>
            <label for="modal-edit-status">
                <span>Status</span>
                <select id="modal-edit-status" name="status_id" required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error" id="modal-edit-status-error"></p>
            </label>
            <div class="modal-buttons">
                <button type="button" class="cancel" id="edit-close-button">Cancel</button>
                <button type="submit" class="submit">Submit</button>
            </div>
        </form>
    </div>
</dialog>