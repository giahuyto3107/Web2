<dialog data-modal id="edit-modal">
    <div class="modal-header">
        <h2>Edit Product</h2>
        <a class="modal-close" data-id="edit-modal" id="edit-close-button-icon">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </a>
    </div>
    <div>
        <form class="modal-form" id="modal-edit-form" method="post">
            <input type="hidden" id="modal-edit-product-id" name="product_id" /> <!-- Ẩn để lưu product_id -->
            <label for="modal-edit-name">
                <span>Name</span>
                <input type="text" id="modal-edit-name" name="product_name" required />
                <p class="modal-error" id="modal-edit-name-error"></p>
            </label>
            <label for="modal-edit-desc" class="modal-desc-label">
                <span>Description</span>
                <textarea id="modal-edit-desc" name="product_description" rows="5"></textarea>
                <p class="modal-error" id="modal-edit-desc-error"></p>
            </label>
            <label for="modal-edit-category">
                <span>Category ID</span>
                <input type="number" id="modal-edit-category" name="category_id" min="1" required />
                <p class="modal-error" id="modal-edit-category-error"></p>
            </label>
            <label for="modal-edit-status">
                <span>Status ID</span>
                <input type="number" id="modal-edit-status" name="status_id" min="0" required />
                <p class="modal-error" id="modal-edit-status-error"></p>
            </label>
            <label for="modal-edit-image">
                <span>Image URL</span>
                <input type="text" id="modal-edit-image" name="image_url" />
                <p class="modal-error" id="modal-edit-image-error"></p>
            </label>
            <div class="modal-buttons">
                <button type="button" class="cancel" id="edit-close-button">Cancel</button>
                <button type="submit" class="submit">Submit</button>
            </div>
        </form>
    </div>
</dialog>