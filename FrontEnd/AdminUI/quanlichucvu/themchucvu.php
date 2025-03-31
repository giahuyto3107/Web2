<dialog data-modal id="add-modal">
    <div class="modal-header">
        <h2>Thêm Chức Vụ</h2>
        <button class="modal-close" data-id="add-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form id="modal-add-form" class="modal-form">
            <div class="modal-input">
                <span>Tên chức vụ</span>
                <input type="text" id="modal-add-name" name="role_name" required />
                <p class="modal-error" id="modal-add-name-error"></p>
            </div>
            <div class="modal-input">
                <span>Mô tả</span>
                <textarea id="modal-add-description" name="role_description" required></textarea>
                <p class="modal-error" id="modal-add-description-error"></p>
            </div>
            <div class="modal-input">
                <span>Trạng thái</span>
                <select id="modal-add-status" name="status_id" required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error" id="modal-add-status-error"></p>
            </div>
            <div class="modal-input">
                <span>Chọn Quyền</span>
                <div class="permissions-list">
                    <?php
                    // Lấy danh sách các quyền có status_id = 1
                    $result = $conn->query("SELECT permission_id, permission_name FROM permission WHERE status_id = 1");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<label>';
                            echo '<input type="checkbox" name="permissions[]" value="' . $row['permission_id'] . '"> ';
                            echo htmlspecialchars($row['permission_name']);
                            echo '</label><br>';
                        }
                    } else {
                        echo '<p>Không có quyền nào khả dụng.</p>';
                    }
                    $conn->close();
                    ?>
                </div>
                <p class="modal-error" id="modal-add-permissions-error"></p>
            </div>
            <div class="modal-buttons">
                <button class="close" id="add-close-button">Hủy</button>
                <button type="submit" class="save">Lưu</button>
            </div>
        </form>
    </div>
</dialog>