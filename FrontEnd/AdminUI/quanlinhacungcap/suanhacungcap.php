<dialog data-modal id="edit-modal">
    <div class="modal-header">
        <h2>Chỉnh sửa Nhà Cung Cấp</h2>
        <button class="modal-close" data-id="edit-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form id="modal-edit-form" class="modal-form">
            <input type="hidden" id="modal-edit-supplier-id" name="supplier_id" />
            <div class="modal-input">
                <span>Tên nhà cung cấp</span>
                <input type="text" id="modal-edit-name" name="supplier_name" required />
                <p class="modal-error" id="modal-edit-name-error"></p>
            </div>
            <div class="modal-input">
                <span>Số điện thoại</span>
                <input type="text" id="modal-edit-contact-phone" name="contact_phone" required />
                <p class="modal-error" id="modal-edit-contact-phone-error"></p>
            </div>
            <div class="modal-input">
                <span>Địa chỉ</span>
                <input type="text" id="modal-edit-address" name="address" required />
                <p class="modal-error" id="modal-edit-address-error"></p>
            </div>
            <div class="modal-input">
                <span>Nhà xuất bản</span>
                <input type="text" id="modal-edit-publisher" name="publisher" required />
                <p class="modal-error" id="modal-edit-publisher-error"></p>
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