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
  <h1 class="heading"> Quản lý <span>THỂ LOẠI</span></h1>
  <div class="toolbar">
    <div class="filters">
      <div class="filter-options-wrapper">
        <label for="filter-options" class="filter-label">Bộ lọc </label>
        <select id="filter-options">
          <option value="category_name">Tên thể loại</option>
          <option value="category_description">Mô tả</option>
          <option value="type_name">Chủng loại</option>
          <option value="status_id">Trạng thái</option>
        </select>
      </div>
      <div class="search">
        <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
      </div>
    </div>
    <div class="toolbar-button-wrapper">
      <button class="toolbar-button add-product-button" id="add-product-toolbar" data-permission-id="8" data-action="Thêm">
        <span>Thêm thể loại</span>
        <i class="bx bx-plus-medical"></i>
      </button>
    </div>
  </div>

  <div id="selected-products"></div>

  <div class="table-container">
    <div class="no-products">
      <p>Có vẻ hiện tại bạn chưa có thể loại nào?</p>
    </div>

    <table class="table" id="data-table">
      <thead>
        <tr>
          <th data-id="category_id">ID</th>
          <th data-id="category_name">Tên thể loại</th>
          <th data-id="category_description">Mô tả</th>
          <th data-id="type_name">Chủng loại</th>
          <th data-id="status_id">Trạng thái</th>
          <th class="actionsTH">Actions</th>
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
    let categories = []; // Dữ liệu gốc, không thay đổi
    let categoryTypes = []; // Danh sách chủng loại từ fetch_chungloai.php

    // Hàm chuyển status_id thành văn bản
    function getStatusText(statusId) {
        switch (statusId) {
            case "1": return 'Active';
            case "2": return 'Inactive';
            default: return 'N/A';
        }
    }

    // Hàm lấy danh sách chủng loại từ fetch_chungloai.php
    function fetchCategoryTypes() {
        fetch('quanlichungloai/fetch_chungloai.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    categoryTypes = data.data;
                    console.log('Category Types:', categoryTypes);
                    // Điền danh sách chủng loại vào dropdown trong modal
                    populateCategoryTypeDropdown();
                } else {
                    console.error('Error fetching category types:', data.message);
                }
            })
            .catch(error => {
                console.error('Fetch category types error:', error);
            });
    }

    // Hàm điền danh sách chủng loại vào dropdown
    function populateCategoryTypeDropdown() {
        const addCategoryTypeSelect = document.getElementById('modal-add-category-type');
        const editCategoryTypeSelect = document.getElementById('modal-edit-category-type');

        if (addCategoryTypeSelect) {
            addCategoryTypeSelect.innerHTML = '<option value="">Chọn chủng loại</option>';
            categoryTypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type.category_type_id;
                option.textContent = type.type_name;
                addCategoryTypeSelect.appendChild(option);
            });
        }

        if (editCategoryTypeSelect) {
            editCategoryTypeSelect.innerHTML = '<option value="">Chọn chủng loại</option>';
            categoryTypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type.category_type_id;
                option.textContent = type.type_name;
                editCategoryTypeSelect.appendChild(option);
            });
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

            let filteredData = categories;

            if (searchValue !== "") {
                filteredData = categories.filter((category) => {
                    if (typeof category[filterBy] === "string") {
                        return category[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                    } else {
                        return category[filterBy].toString().includes(searchValue);
                    }
                });
            }

            renderTable(filteredData);
        });

        filterOptionsEl.addEventListener("change", () => {
            searchEl.value = "";
            renderTable(categories);
        });
    }

    // Hàm render bảng
    function renderTable(displayedCategories) {
        const tableBody = document.getElementById('table-body');
        const noProductsEl = document.querySelector('.no-products');

        if (!tableBody || !noProductsEl) {
            console.error('Required elements not found: #table-body or .no-products');
            return;
        }

        tableBody.innerHTML = '';
        const activeCategories = displayedCategories.filter(category => category.status_id !== 6);

        if (activeCategories.length > 0) {
            noProductsEl.style.display = 'none';
            activeCategories.forEach((category, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${category.category_name || 'N/A'}</td>
                    <td>${category.category_description || 'N/A'}</td>
                    <td>${category.type_name || 'N/A'}</td>
                    <td>${getStatusText(category.status_id)}</td>
                    <td class="actions">
                        <div class="dropdown">
                            <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                            <div class="dropdown-content">
                                <a href="#" class="viewCategory" data-permission-id="8" data-action="Xem" data-category-id="${category.category_id}">Xem Thể Loại <i class="fa fa-eye"></i></a>
                                <a href="#" class="editCategory" data-permission-id="8" data-action="Sửa" data-category-id="${category.category_id}">Sửa Thể Loại <i class="fa fa-edit"></i></a>
                                <a href="#" class="deleteCategory" data-permission-id="8" data-action="Xóa" data-category-id="${category.category_id}">Xóa Thể Loại <i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            noProductsEl.style.display = 'flex';
            tableBody.innerHTML = '<tr><td colspan="6">No categories found.</td></tr>';
        }
    }

    // Fetch dữ liệu ban đầu từ server
    fetch('quanliloaisp/fetch_categories.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                categories = data.data;
                console.log('Initial categories:', categories);
                renderTable(categories);
                addFilterEventListener();
                // Lấy danh sách chủng loại sau khi lấy danh mục
                fetchCategoryTypes();
            } else {
                console.error('Error:', data.message);
                document.getElementById('table-body').innerHTML = '<tr><td colspan="6">Error loading categories.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('table-body').innerHTML = '<tr><td colspan="6">Error loading categories.</td></tr>';
        });

    // Sử dụng event delegation để xử lý các hành động
    document.getElementById('table-body').addEventListener('click', (e) => {
        const target = e.target.closest('a');
        if (!target) return;

        e.preventDefault();
        const categoryId = target.getAttribute('data-category-id');
        const category = categories.find(cat => cat.category_id === categoryId);

        if (!category) {
            console.error('Category not found:', categoryId);
            return;
        }

        if (target.classList.contains('viewCategory')) {
            const viewModalEl = document.getElementById("view-modal");
            addModalData(viewModalEl, category, "innerHTML");
            viewModalEl.showModal();
        } else if (target.classList.contains('editCategory')) {
            const editModalEl = document.getElementById("edit-modal");
            openEditModal(category);
        } else if (target.classList.contains('deleteCategory')) {
            const deleteModalEl = document.getElementById("delete-modal");
            deleteModalEl.setAttribute("data-category-id", categoryId);
            deleteModalEl.showModal();
        }
    });

    // Hàm mở modal chỉnh sửa
    function openEditModal(category) {
        const editModal = document.getElementById('edit-modal');
        const form = document.getElementById('modal-edit-form');

        document.getElementById('modal-edit-category-id').value = category.category_id;
        document.getElementById('modal-edit-name').value = category.category_name || '';
        document.getElementById('modal-edit-desc').value = category.category_description || '';
        document.getElementById('modal-edit-category-type').value = category.category_type_id || '';
        document.getElementById('modal-edit-status').value = category.status_id;

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

        updateCategory(form);
    }

    // Hàm cập nhật danh mục
    function updateCategory(form) {
        const formData = new FormData(form);
        fetch('../../BackEnd/Model/quanliloaisanpham/xuliloaisanpham.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(result => {
            const editModal = document.getElementById('edit-modal');
            if (result.status === 'success') {
                fetch('quanliloaisp/fetch_categories.php')
                    .then(response => response.json())
                    .then(data => {
                        categories = data.data;
                        renderTable(categories);
                        editModal.close();
                        const successMessage = document.getElementById('success-message');
                        successMessage.querySelector('.success-text p').textContent = result.message || 'Thể loại đã được cập nhật';
                        successMessage.style.display = 'block';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 3000);
                    })
                    .catch(error => console.error('Có lỗi khi lấy dữ liệu thể loại:', error));
            } else {
                const errorContainer = editModal.querySelector('.modal-error');
                errorContainer.textContent = result.message || 'Có lỗi khi cập nhật thể loại';
                errorContainer.style.display = 'block';
                errorContainer.style.color = 'var(--clr-error)';
                editModal.scrollTop = 0;
            }
        })
        .catch(error => {
            console.error('Cập nhật thể loại thất bại:', error);
            const editModal = document.getElementById('edit-modal');
            editModal.scrollTop = 0;
        });
    }

    // Hàm thêm danh mục
    function addProduct(formEl) {
        const formData = new FormData(formEl);
        fetch('../../BackEnd/Model/quanliloaisanpham/xuliloaisanpham.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(result => {
            const addProductModal = document.getElementById("add-modal");
            if (result.status === 'success') {
                fetch('quanliloaisp/fetch_categories.php')
                    .then(response => response.json())
                    .then(data => {
                        categories = data.data;
                        renderTable(categories);
                        addProductModal.close();
                        const successMessage = document.getElementById('success-message');
                        successMessage.querySelector('.success-text p').textContent = result.message || 'Thể loại thêm thành công';
                        successMessage.style.display = 'block';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 3000);
                    })
                    .catch(error => console.error('Có lỗi khi lấy dữ liệu thể loại:', error));
            } else {
                const errorContainer = addProductModal.querySelector('.modal-error');
                errorContainer.textContent = result.message || 'Có lỗi khi thêm thể loại';
                errorContainer.style.display = 'block';
                errorContainer.style.color = 'var(--clr-error)';
                addProductModal.scrollTop = 0;
            }
        })
        .catch(error => {
            console.error('Thêm thể loại thất bại:', error);
            const addProductModal = document.getElementById("add-modal");
            addProductModal.scrollTop = 0;
        });
    }

    // Hàm xóa danh mục (cập nhật status_id thành 6)
    function deleteProduct(categoryId) {
        const formData = new FormData();
        formData.append('category_id', categoryId);
        formData.append('status_id', 6);

        fetch('../../BackEnd/Model/quanliloaisanpham/xuliloaisanpham.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                fetch('quanliloaisp/fetch_categories.php')
                    .then(response => response.json())
                    .then(data => {
                        categories = data.data;
                        renderTable(categories);
                        const deleteModalEl = document.getElementById('delete-modal');
                        deleteModalEl.close();
                        const successMessage = document.getElementById('success-message');
                        successMessage.querySelector('.success-text p').textContent = result.message || 'Thể loại đã được đánh dấu xóa';
                        successMessage.style.display = 'block';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 3000);
                    })
                    .catch(error => console.error('Có lỗi khi lấy dữ liệu thể loại:', error));
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
        const categoryId = parseInt(deleteModalEl.getAttribute('data-category-id'));
        deleteProduct(categoryId);
    });

    // Hàm xử lý modal Add
    function addViewProductModalEventListener() {
        const addProductModal = document.getElementById("add-modal");
        const formEl = document.getElementById("modal-add-form");
        const addCloseButton = addProductModal.querySelector("#add-close-button");
        const addProductToolbar = document.querySelector("#add-product-toolbar");

        addProductToolbar.addEventListener("click", () => {
            addProductModal.showModal();
        });

        addCloseButton.addEventListener("click", () => {
            addProductModal.close();
        });

        formEl.addEventListener("submit", (e) => {
            e.preventDefault();
            const isError = validateAddModalFormInputs(formEl);
            if (!isError) {
                addProduct(formEl);
            } else {
                addProductModal.scrollTop = 0;
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
            // Sử dụng biểu thức chính quy hỗ trợ ký tự Unicode (bao gồm tiếng Việt)
            if (!/^[\p{L}\s-]+$/u.test(value)) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Tên thể loại chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
            } else if (value.length > 100) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Tên thể loại không được vượt quá 100 ký tự';
            }
        }

        if (input.id === 'modal-edit-desc') {
            if (value.length > 400) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 400 ký tự';
            }
        }

        if (input.id === 'modal-edit-category-type') {
            if (!value) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Vui lòng chọn chủng loại!';
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
            // Sử dụng biểu thức chính quy hỗ trợ ký tự Unicode (bao gồm tiếng Việt)
            if (!/^[\p{L}\s-]+$/u.test(value)) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Tên thể loại chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
            } else if (value.length > 100) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Tên thể loại không được vượt quá 100 ký tự';
            }
        }

        if (input.id === 'modal-add-desc') {
            if (value.length > 400) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 400 ký tự';
            }
        }

        if (input.id === 'modal-add-category-type') {
            if (!value) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Vui lòng chọn chủng loại!';
            }
        }
    });

    return isError;
}

    // Hàm thêm dữ liệu vào modal
    function addModalData(modalEl, category, type) {
        if (type === "innerHTML") {
            modalEl.querySelector("#modal-view-category-id").textContent = category.category_id || 'N/A';
            modalEl.querySelector("#modal-view-name").textContent = category.category_name || 'N/A';
            modalEl.querySelector("#modal-view-desc").textContent = category.category_description || 'N/A';
            modalEl.querySelector("#modal-view-category-type").textContent = category.type_name || 'N/A';
            modalEl.querySelector("#modal-view-status").textContent = getStatusText(category.status_id);
        } else if (type === "value") {
            modalEl.querySelector("#modal-edit-category-id").value = category.category_id;
            modalEl.querySelector("#modal-edit-name").value = category.category_name || '';
            modalEl.querySelector("#modal-edit-desc").value = category.category_description || '';
            modalEl.querySelector("#modal-edit-category-type").value = category.category_type_id || '';
            modalEl.querySelector("#modal-edit-status").value = category.status_id;
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
            console.error('Cancel button with id ending in "-close-button" not found in modal!');
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
    addViewProductModalEventListener();
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
    include 'quanliloaisp/themloaisp.php'; // Add Modal
    include 'quanliloaisp/sualoaisp.php'; // Edit Modal
    include 'quanliloaisp/xemloaisp.php';
    include 'quanliloaisp/xoaloaisp.php';
?>