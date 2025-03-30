<dialog data-modal id="add-modal">
    <div class="modal-header">
        <h2>Add Category</h2>
        <a class="modal-close" data-id="add-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </a>
    </div>
    <div>
        <form class="modal-form" id="modal-add-form" method="post">
            <label for="modal-add-name">
                <span>Category Name</span>
                <input type="text" id="modal-add-name" name="category_name" required />
                <p class="modal-error" id="modal-add-name-error"></p>
            </label>
            <label for="modal-add-desc" class="modal-desc-label">
                <span>Description</span>
                <textarea id="modal-add-desc" name="category_description" rows="5" maxlength="400">Enter description here...</textarea>
                <p class="modal-error" id="modal-add-desc-error"></p>
            </label>
            <label for="modal-add-status">
                <span>Status</span>
                <select id="modal-add-status" name="status_id" required>
                    <option value="">Select status</option>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error" id="modal-add-status-error"></p>
            </label>
            <div class="modal-buttons">
                <button type="button" class="cancel" formmethod="dialog" id="add-close-button">Cancel</button>
                <button type="submit" class="submit">Submit</button>
            </div>
        </form>
    </div>
</dialog>