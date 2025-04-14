<dialog data-modal id="edit-modal">
    <div class="modal-header">
        <h2>Chỉnh Sửa Sản Phẩm</h2>
        <button class="modal-close" data-id="edit-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form class="modal-form" id="modal-edit-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="modal-edit-product-id" name="product_id">
            <div class="modal-input-wrapper">
                <label for="modal-edit-name">Tên Sản Phẩm</label>
                <input type="text" id="modal-edit-name" name="product_name" required>
                <p class="modal-error"></p>
            </div>
            <div class="modal-input-wrapper">
                <label for="modal-edit-description">Mô Tả</label>
                <textarea id="modal-edit-description" name="product_description" required></textarea>
                <p class="modal-error"></p>
            </div>
            <div class="modal-input-wrapper">
                <label for="modal-edit-categories">Thể Loại</label>
                <p style="font-style: italic; font-size: 0.7rem;">Nhấn và giữ nút "Ctrl" để chọn nhiều thể loại</p>
                <select id="modal-edit-categories" name="categories[]" multiple required>
                    <!-- Tải danh sách thể loại bằng JavaScript -->
                </select>
                <p class="modal-error"></p>
            </div>
            <div class="modal-input-wrapper">
                <label for="modal-edit-status">Trạng Thái</label>
                <select id="modal-edit-status" name="status_id" required>
                    <option value="1">Hoạt Động</option>
                    <option value="2">Không Hoạt Động</option>
                </select>
                <p class="modal-error"></p>
            </div>
            <div class="modal-input-wrapper">
                <label for="modal-edit-image">Hình Ảnh</label>
                <input type="file" id="modal-edit-image" name="image" accept="image/*">
                <p class="modal-error"></p>
                <!-- Thêm phần preview ảnh -->
                <div class="image-preview" style="margin-top: 10px;">
                    <img id="edit-image-preview" src="" alt="Preview" style="max-width: 200px; display: none;">
                </div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="close" id="edit-close-button">Hủy</button>
                <button type="submit" class="edit">Cập Nhật</button>
            </div>
        </form>
    </div>
</dialog>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tải danh sách thể loại vào select
    fetch('quanlisanpham/fetch_loaisp_sp.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const categorySelect = document.getElementById('modal-edit-categories');
                data.data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.category_id;
                    option.textContent = category.category_name;
                    categorySelect.appendChild(option);
                });
            } else {
                console.error('Lỗi khi tải danh sách thể loại:', data.message);
            }
        })
        .catch(error => console.error('Lỗi khi tải danh sách thể loại:', error));

    // Preview ảnh khi chọn file mới
    const imageInput = document.getElementById('modal-edit-image');
    const imagePreview = document.getElementById('edit-image-preview');

    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            // Nếu không chọn file mới, giữ nguyên ảnh cũ (sẽ được xử lý trong openEditModal)
            const currentSrc = imagePreview.getAttribute('data-current-src');
            if (currentSrc) {
                imagePreview.src = currentSrc;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
        }
    });
});
</script>