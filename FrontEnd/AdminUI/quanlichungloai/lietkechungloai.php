<div class="header"></div>
<div class="data-table">
  <div class="success-message" id="success-message" style="display: none">
    <div class="success-text">
      <p>Dummy Text</p>
      <a id="success-message-cross" style="cursor: pointer">
        <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
      </a>
    </div>
    <div class="progress-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>
  </div>
  <h1 class="heading"> Quản lý <span>CHỦNG LOẠI</span></h1>
  <div class="toolbar">
    <div class="filters">
      <div class="filter-options-wrapper">
        <label for="filter-options" class="filter-label">Bộ lọc </label>
        <select id="filter-options">
          <option value="type_name">Tên chủng loại</option>
          <option value="type_description">Mô tả</option>
          <option value="status_id">Trạng thái</option>
        </select>
      </div>
      <div class="search">
        <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
      </div>
    </div>
    <div class="toolbar-button-wrapper">
      <button class="toolbar-button add-product-button" id="add-product-toolbar" data-permission-id="12" data-action="Thêm">
        <span>Thêm chủng loại</span>
        <i class="bx bx-plus-medical"></i>
      </button>
    </div>
  </div>

  <div id="selected-products"></div>

  <div class="table-container">
    <div class="no-products">
      <p>Có vẻ hiện tại bạn chưa có chủng loại nào?</p>
    </div>

    <table class="table" id="data-table">
      <thead>
        <tr>
          <th data-id="category_type_id">ID</th>
          <th data-id="type_name">Tên chủng loại</th>
          <th data-id="type_description">Mô tả</th>
          <th data-id="status_id">Trạng thái</th>
          <th class="actionsTH">Hành động</th>
        </tr>
      </thead>
      <tbody id="table-body"></tbody>
    </table>
  </div>
</div>

<!-- Script để fetch và hiển thị dữ liệu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Biến toàn cục
    let categoryTypes = []; // Dữ liệu gốc, không thay đổi

    // Hàm chuyển status_id thành văn bản
    function getStatusText(statusId) {
        switch (statusId) {
            case "1": return 'Hoạt động';
            case "2": return 'Không hoạt động';
            default: return 'N/A';
        }
    }

    // Hàm thêm sự kiện lọc và tìm kiếm
    function addFilterEventListener() {
        const searchEl = document.getElementById("search-text");
        const filterOptionsEl = document.getElementById("filter-options");

        if (!searchEl || !filterOptionsEl) {
            console.error('Required elements not found: #search-text or #filter-options');
            return;
        }

        searchEl.addEventListener("input", () => {
            const filterBy = filterOptionsEl.value;
            const searchValue = searchEl.value.trim();

            let filteredData = categoryTypes;

            if (searchValue !== "") {
                filteredData = categoryTypes.filter((categoryType) => {
                    if (typeof categoryType[filterBy] === "string") {
                        return categoryType[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                    } else {
                        return categoryType[filterBy].toString().includes(searchValue);
                    }
                });
            }

            renderTable(filteredData);
        });

        filterOptionsEl.addEventListener("change", () => {
            searchEl.value = "";
            renderTable(categoryTypes);
        });
    }

    // Hàm render bảng
    function renderTable(displayedCategoryTypes) {
        const tableBody = document.getElementById('table-body');
        const noProductsEl = document.querySelector('.no-products');

        if (!tableBody || !noProductsEl) {
            console.error('Required elements not found: #table-body or .no-products');
            return;
        }

        tableBody.innerHTML = '';
        const activeCategoryTypes = displayedCategoryTypes.filter(categoryType => categoryType.status_id !== 6);

        if (activeCategoryTypes.length > 0) {
            noProductsEl.style.display = 'none';
            activeCategoryTypes.forEach((categoryType, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${categoryType.type_name || 'N/A'}</td>
                    <td>${categoryType.type_description || 'N/A'}</td>
                    <td>${getStatusText(categoryType.status_id)}</td>
                    <td class="actions">
                        <div class="dropdown">
                            <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                            <div class="dropdown-content">
                                <a href="#" class="viewCategoryType" data-permission-id="12" data-action="Xem" data-category-type-id="${categoryType.category_type_id}">Xem chủng loại <i class="fa fa-eye"></i></a>
                                <a href="#" class="editCategoryType" data-permission-id="12" data-action="Sửa" data-category-type-id="${categoryType.category_type_id}">Sửa chủng loại <i class="fa fa-edit"></i></a>
                                <a href="#" class="deleteCategoryType" data-permission-id="12" data-action="Xóa" data-category-type-id="${categoryType.category_type_id}">Xóa chủng loại <i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            noProductsEl.style.display = 'flex';
            tableBody.innerHTML = '<tr><td colspan="5">Không tìm thấy chủng loại nào.</td></tr>';
        }
    }

    // Fetch dữ liệu ban đầu từ server
    fetch('quanlichungloai/fetch_chungloai.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                categoryTypes = data.data;
                console.log('Initial category types:', categoryTypes);
                renderTable(categoryTypes);
                addFilterEventListener();
            } else {
                console.error('Lỗi:', data.message);
                document.getElementById('table-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải danh sách chủng loại.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Lỗi tải dữ liệu:', error);
            document.getElementById('table-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải danh sách chủng loại.</td></tr>';
        });

    // Sử dụng event delegation để xử lý các hành động
    document.getElementById('table-body').addEventListener('click', (e) => {
        const target = e.target.closest('a');
        if (!target) return;

        e.preventDefault();
        const categoryTypeId = target.getAttribute('data-category-type-id');
        const categoryType = categoryTypes.find(cat => cat.category_type_id === categoryTypeId);

        if (!categoryType) {
            console.error('Không tìm thấy chủng loại:', categoryTypeId);
            return;
        }

        if (target.classList.contains('viewCategoryType')) {
            const viewModalEl = document.getElementById("view-modal");
            addModalData(viewModalEl, categoryType, "innerHTML");
            viewModalEl.showModal();
        } else if (target.classList.contains('editCategoryType')) {
            const editModalEl = document.getElementById("edit-modal");
            openEditModal(categoryType);
        } else if (target.classList.contains('deleteCategoryType')) {
            const deleteModalEl = document.getElementById("delete-modal");
            deleteModalEl.setAttribute("data-category-type-id", categoryTypeId);
            deleteModalEl.showModal();
        }
    });

    // Hàm mở modal chỉnh sửa
    function openEditModal(categoryType) {
        const editModal = document.getElementById('edit-modal');
        const form = document.getElementById('modal-edit-form');

        document.getElementById('modal-edit-category-type-id').value = categoryType.category_type_id;
        document.getElementById('modal-edit-name').value = categoryType.type_name || '';
        document.getElementById('modal-edit-desc').value = categoryType.type_description || '';
        document.getElementById('modal-edit-status').value = categoryType.status_id;

        clearFormErrors(form);
        form.removeEventListener('submit', handleEditSubmit);
        form.addEventListener('submit', handleEditSubmit);
        editModal.showModal();
    }

    // Hàm xử lý submit form chỉnh sửa
    function handleEditSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('modal-edit-form');
        const editModal = document.getElementById('edit-modal');
        const errorContainer = editModal.querySelector('.modal-error') || document.createElement('p');

        clearFormErrors(form);
        if (!errorContainer.parentElement) {
            editModal.querySelector('.modal-buttons').insertAdjacentElement('beforebegin', errorContainer);
        }
        errorContainer.textContent = '';
        errorContainer.style.display = 'none';

        const isError = validateModalFormInputs(form);
        if (isError) {
            errorContainer.style.display = 'block';
            errorContainer.style.color = 'var(--clr-error)';
            editModal.scrollTop = 0;
            return;
        }

        updateCategoryType(form);
    }

    // Hàm cập nhật chủng loại
    function updateCategoryType(form) {
        const formData = new FormData(form);
        fetch('../../BackEnd/Model/quanlichungloai/xulichungloai.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Phản hồi mạng không ổn');
            }
            return response.json();
        })
        .then(result => {
            const editModal = document.getElementById('edit-modal');
            if (result.status === 'success') {
                fetch('quanlichungloai/fetch_chungloai.php')
                    .then(response => response.json())
                    .then(data => {
                        categoryTypes = data.data;
                        renderTable(categoryTypes);
                        editModal.close();
                        const successMessage = document.getElementById('success-message');
                        successMessage.querySelector('.success-text p').textContent = result.message || 'Chủng loại đã được cập nhật';
                        successMessage.style.display = 'block';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 3000);
                    })
                    .catch(error => console.error('Có lỗi khi lấy dữ liệu chủng loại:', error));
            } else {
                const errorContainer = editModal.querySelector('.modal-error');
                errorContainer.textContent = result.message || 'Có lỗi khi cập nhật chủng loại';
                errorContainer.style.display = 'block';
                errorContainer.style.color = 'var(--clr-error)';
                editModal.scrollTop = 0;
            }
        })
        .catch(error => {
            console.error('Cập nhật chủng loại thất bại:', error);
            const editModal = document.getElementById('edit-modal');
            editModal.scrollTop = 0;
        });
    }

    // Hàm thêm chủng loại
    function addCategoryType(formEl) {
        const formData = new FormData(formEl);
        fetch('../../BackEnd/Model/quanlichungloai/xulichungloai.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Phản hồi mạng không ổn');
            }
            return response.json();
        })
        .then(result => {
            const addCategoryTypeModal = document.getElementById("add-modal");
            if (result.status === 'success') {
                fetch('quanlichungloai/fetch_chungloai.php')
                    .then(response => response.json())
                    .then(data => {
                        categoryTypes = data.data;
                        renderTable(categoryTypes);
                        addCategoryTypeModal.close();
                        const successMessage = document.getElementById('success-message');
                        successMessage.querySelector('.success-text p').textContent = result.message || 'Thêm chủng loại thành công';
                        successMessage.style.display = 'block';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 3000);
                    })
                    .catch(error => console.error('Có lỗi khi lấy dữ liệu chủng loại:', error));
            } else {
                const errorContainer = addCategoryTypeModal.querySelector('.modal-error');
                errorContainer.textContent = result.message || 'Có lỗi khi thêm chủng loại';
                errorContainer.style.display = 'block';
                errorContainer.style.color = 'var(--clr-error)';
                addCategoryTypeModal.scrollTop = 0;
            }
        })
        .catch(error => {
            console.error('Thêm chủng loại thất bại:', error);
            const addCategoryTypeModal = document.getElementById("add-modal");
            addCategoryTypeModal.scrollTop = 0;
        });
    }

    // Hàm xóa chủng loại (cập nhật status_id thành 6)
    function deleteCategoryType(categoryTypeId) {
        const formData = new FormData();
        formData.append('category_type_id', categoryTypeId);
        formData.append('status_id', 6);

        fetch('../../BackEnd/Model/quanlichungloai/xulichungloai.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                fetch('quanlichungloai/fetch_chungloai.php')
                    .then(response => response.json())
                    .then(data => {
                        categoryTypes = data.data;
                        renderTable(categoryTypes);
                        const deleteModalEl = document.getElementById('delete-modal');
                        deleteModalEl.close();
                        const successMessage = document.getElementById('success-message');
                        successMessage.querySelector('.success-text p').textContent = result.message || 'Chủng loại đã được đánh dấu xóa';
                        successMessage.style.display = 'block';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 3000);
                    })
                    .catch(error => console.error('Có lỗi khi lấy dữ liệu chủng loại:', error));
            } else {
                const successMessage = document.getElementById('success-message');
                successMessage.querySelector('.success-text p').textContent = result.message || 'Xóa thất bại';
                successMessage.style.display = 'block';
                successMessage.style.backgroundColor = 'var(--clr-error)';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                    successMessage.style.backgroundColor = '';
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Lỗi khi gửi yêu cầu xóa:', error);
            const successMessage = document.getElementById('success-message');
            successMessage.querySelector('.success-text p').textContent = 'Lỗi khi gửi yêu cầu xóa';
            successMessage.style.display = 'block';
            successMessage.style.backgroundColor = 'var(--clr-error)';
            setTimeout(() => {
                successMessage.style.display = 'none';
                successMessage.style.backgroundColor = '';
            }, 3000);
        });
    }

    // Event listener cho nút xóa trong delete-modal
    const deleteModalEl = document.getElementById('delete-modal');
    const deleteDeleteButton = deleteModalEl.querySelector('#delete-delete-button');
    deleteDeleteButton.addEventListener('click', () => {
        const categoryTypeId = parseInt(deleteModalEl.getAttribute('data-category-type-id'));
        deleteCategoryType(categoryTypeId);
    });

    // Hàm xử lý modal Add
    function addViewCategoryTypeModalEventListener() {
        const addCategoryTypeModal = document.getElementById("add-modal");
        const formEl = document.getElementById("modal-add-form");
        const addCloseButton = addCategoryTypeModal.querySelector("#add-close-button");
        const addCategoryTypeToolbar = document.querySelector("#add-product-toolbar");

        addCategoryTypeToolbar.addEventListener("click", () => {
            addCategoryTypeModal.showModal();
        });

        addCloseButton.addEventListener("click", () => {
            addCategoryTypeModal.close();
        });

        formEl.addEventListener("submit", (e) => {
            e.preventDefault();
            const isError = validateAddModalFormInputs(formEl);
            if (!isError) {
                addCategoryType(formEl);
            } else {
                addCategoryTypeModal.scrollTop = 0;
            }
        });
    }

    // Hàm xóa lỗi form
    function clearFormErrors(form) {
        const errorEls = form.querySelectorAll('.modal-error');
        errorEls.forEach(errorEl => errorEl.textContent = '');
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => input.style.border = '');
    }

    // Hàm hiển thị lỗi form
    function displayFormErrors(errors) {
        if (!errors) return;
        Object.keys(errors).forEach(key => {
            const errorEl = document.getElementById(`modal-edit-${key}-error`);
            if (errorEl) {
                errorEl.textContent = errors[key];
                const input = document.getElementById(`modal-edit-${key}`);
                if (input) input.style.border = '1px solid var(--clr-error)';
            }
        });
    }

    // Hàm validate form cho edit-modal
    function validateModalFormInputs(form) {
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isError = false;

        inputs.forEach(input => {
            const value = input.value.trim();
            const errorEl = input.parentElement.querySelector('.modal-error');
            input.style.border = '';
            if (errorEl) errorEl.textContent = '';

            if (!value) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Trường này không được để trống!';
                return;
            }

            if (input.id === 'modal-edit-name') {
                if (!/^[\p{L}\s-]+$/u.test(value)) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Tên chủng loại chỉ chứa chữ cái, khoảng trắng và dấu gạch ngang';
                } else if (value.length > 100) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Tên chủng loại không được vượt quá 100 ký tự';
                }
            }

            if (input.id === 'modal-edit-desc') {
                if (value.length > 400) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 400 ký tự';
                }
            }
        });

        return isError;
    }

    // Hàm validate form cho add-modal
    function validateAddModalFormInputs(form) {
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isError = false;

        inputs.forEach(input => {
            const value = input.value.trim();
            const errorEl = input.parentElement.querySelector('.modal-error');
            input.style.border = '';
            if (errorEl) errorEl.textContent = '';

            if (!value) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Trường này không được để trống!';
                return;
            }

            if (input.id === 'modal-add-name') {
                if (!/^[\p{L}\s-]+$/u.test(value)) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Tên chủng loại chỉ chứa chữ cái, khoảng trắng và dấu gạch ngang';
                } else if (value.length > 100) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Tên chủng loại không được vượt quá 100 ký tự';
                }
            }

            if (input.id === 'modal-add-desc') {
                if (value.length > 400) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 400 ký tự';
                }
            }
        });

        return isError;
    }

    // Hàm thêm dữ liệu vào modal
    function addModalData(modalEl, categoryType, type) {
        if (type === "innerHTML") {
            modalEl.querySelector("#modal-view-category-type-id").textContent = categoryType.category_type_id || 'N/A';
            modalEl.querySelector("#modal-view-name").textContent = categoryType.type_name || 'N/A';
            modalEl.querySelector("#modal-view-desc").textContent = categoryType.type_description || 'N/A';
            modalEl.querySelector("#modal-view-status").textContent = getStatusText(categoryType.status_id);
        } else if (type === "value") {
            modalEl.querySelector("#modal-edit-category-type-id").value = categoryType.category_type_id;
            modalEl.querySelector("#modal-edit-name").value = categoryType.type_name || '';
            modalEl.querySelector("#modal-edit-desc").value = categoryType.type_description || '';
            modalEl.querySelector("#modal-edit-status").value = categoryType.status_id;
        }
    }

    // Hàm xử lý modal
    function addModalCloseButtonEventListeners() {
        document.addEventListener('click', (e) => {
            const closeEl = e.target.closest('.modal-close');
            if (closeEl) {
                const modalId = closeEl.dataset.id;
                const modalEl = document.getElementById(modalId);
                if (modalEl) {
                    modalEl.close();
                    const formEl = modalEl.querySelector('form.modal-form');
                    if (formEl) {
                        clearFormErrors(formEl);
                    }
                }
            }
        });
    }

    function addModalCancelButtonEventListener(modalEl) {
        const cancelButton = modalEl.querySelector('[id$="-close-button"]');
        if (!cancelButton) {
            console.error('Nút hủy với id kết thúc bằng "-close-button" không được tìm thấy trong modal!');
            return;
        }

        cancelButton.addEventListener("click", () => {
            modalEl.close();
            const formEl = modalEl.querySelector('form.modal-form');
            if (formEl) {
                clearFormErrors(formEl);
            }
        });
    }

    // Gọi hàm để thêm sự kiện
    addViewCategoryTypeModalEventListener();
    addModalCloseButtonEventListeners();
    const addModal = document.getElementById('add-modal');
    if (addModal) {
        addModalCancelButtonEventListener(addModal);
    }
    const editModal = document.getElementById('edit-modal');
    if (editModal) {
        addModalCancelButtonEventListener(editModal);
    }
    const viewModal = document.getElementById('view-modal');
    if (viewModal) {
        addModalCancelButtonEventListener(viewModal);
    }
    const deleteModal = document.getElementById('delete-modal');
    if (deleteModal) {
        addModalCancelButtonEventListener(deleteModal);
    }
});
</script>

<?php
    include 'quanlichungloai/themchungloai.php'; // Modal thêm
    include 'quanlichungloai/suachungloai.php'; // Modal sửa
    include 'quanlichungloai/xemchungloai.php'; // Modal xem
    include 'quanlichungloai/xoachungloai.php'; // Modal xóa
?>
</div>