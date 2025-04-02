<dialog data-modal id="add-modal">
    <div class="modal-header">
        <h2>Thêm Sản Phẩm</h2>
        <button class="modal-close" data-id="add-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="modal-content">
        <form class="modal-form" id="modal-add-form">
            <input type="hidden" name="action" value="add">
            <div class="modal-input-wrapper">
                <label for="modal-add-name">Tên Sản Phẩm</label>
                <input type="text" id="modal-add-name" name="product_name" required>
                <p class="modal-error"></p>
            </div>
            <div class="modal-input-wrapper">
                <label for="modal-add-description">Mô Tả</label>
                <textarea id="modal-add-description" name="product_description" required></textarea>
                <p class="modal-error"></p>
            </div>
            <div class="modal-input-wrapper">
                <label for="modal-add-categories">Thể Loại</label>
                <p style="font-style: italic; font-size: 0.7rem;">Nhấn và giữ nút "Ctrl" để chọn nhiều thể loại</p>
                <select id="modal-add-categories" name="categories[]" multiple required>
                    <!-- Tải danh sách thể loại bằng JavaScript -->
                </select>
                <p class="modal-error"></p>
            </div>
            <div class="modal-input-wrapper">
                <label for="modal-add-status">Trạng Thái</label>
                <select id="modal-add-status" name="status_id" required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <p class="modal-error"></p>
            </div>
            <div class="modal-input-wrapper">
                <label for="modal-add-image">Hình Ảnh</label>
                <input type="file" id="modal-add-image" name="image" accept="image/*">
                <p class="modal-error"></p>
                <!-- Thêm phần preview ảnh -->
                <div class="image-preview" style="margin-top: 10px;">
                    <img id="add-image-preview" src="" alt="Preview" style="max-width: 200px; display: none;">
                </div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="close" id="add-close-button">Hủy</button>
                <button type="submit" class="add">Thêm</button>
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
                const categorySelect = document.getElementById('modal-add-categories');
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

    // Preview ảnh khi chọn file
    const imageInput = document.getElementById('modal-add-image');
    const imagePreview = document.getElementById('add-image-preview');

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
            imagePreview.src = '';
            imagePreview.style.display = 'none';
        }
    });
});
</script>