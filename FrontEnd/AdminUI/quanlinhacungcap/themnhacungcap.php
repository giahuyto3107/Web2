<dialog data-modal id="add-modal">
    <div class="modal-header">
        <h2>Thêm Nhà Cung Cấp</h2>
        <button class="modal-close" data-id="add-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form id="modal-add-form" class="modal-form">
            <div class="modal-input">
                <span>Tên nhà cung cấp</span>
                <input type="text" id="modal-add-name" name="supplier_name" required />
                <p class="modal-error" id="modal-add-name-error"></p>
            </div>
            <div class="modal-input">
                <span>Số điện thoại</span>
                <input type="text" id="modal-add-contact-phone" name="contact_phone" required />
                <p class="modal-error" id="modal-add-contact-phone-error"></p>
            </div>
            <div class="modal-input">
                <span>Địa chỉ</span>
                <input type="text" id="modal-add-address" name="address" required />
                <p class="modal-error" id="modal-add-address-error"></p>
            </div>
            <div class="modal-input">
                <span>Nhà xuất bản</span>
                <input type="text" id="modal-add-publisher" name="publisher" required />
                <p class="modal-error" id="modal-add-publisher-error"></p>
            </div>
            <div class="modal-input">
                <span>Trạng thái</span>
                <select id="modal-add-status" name="status_id" required>
                    <option value="1">Hoạt động</option>
                    <option value="2">Không hoạt động</option>
                </select>
                <p class="modal-error" id="modal-add-status-error"></p>
            </div>
            <div class="modal-buttons">
                <button class="close" id="add-close-button">Hủy</button>
                <button type="submit" class="save">Lưu</button>
            </div>
        </form>
    </div>
</dialog>