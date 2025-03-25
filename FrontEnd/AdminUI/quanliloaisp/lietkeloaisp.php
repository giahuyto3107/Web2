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
  <h1 class="heading"> Quản lí <span>THỂ LOẠI</span></h1>
  <div class="toolbar">
    <div class="filters">
      <div class="filter-options-wrapper">
        <label for="filter-options" class="filter-label">Bộ lọc </label>
        <select id="filter-options">
        <option value="category_name">Tên thể loại</option>
        <option value="category_description">Mô tả</option>
        <option value="status_id">Trạng thái</option>
    </select>
      </div>
      <div class="search">
        <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
      </div>
    </div>
    <div class="toolbar-button-wrapper">

      <button class="toolbar-button add-product-button" id="add-product-toolbar">
        <span>Thêm thể loại</span>
        <i class="bx bx-plus-medical"></i>
      </button>
    </div>
  </div>

  <div id="selected-products"></div>


  <div class="table-container">
    <div class="no-products">
      <p>Looks like you do not have any categories.</p>
    </div>

    <table class="table" id="data-table">
      <thead>
        <tr>
        <th data-id="category_id">ID</th>
        <th data-id="category_name">Tên thể loại</th>
        <th data-id="category_description">Mô tả</th>
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

    // Hàm chuyển status_id thành văn bản
    function getStatusText(statusId) {
        switch (statusId) {
            case "1": return 'Active';
            case "2": return 'Inactive';
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

            console.log('Search value:', searchValue);
            console.log('Filter by:', filterBy);

            let filteredData = categories; // Luôn bắt đầu từ dữ liệu gốc

            if (searchValue !== "") {
                filteredData = categories.filter((category) => {
                    if (typeof category[filterBy] === "string") {
                        return category[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                    } else {
                        return category[filterBy].toString().includes(searchValue);
                    }
                });
            }

            console.log('Filtered categories:', filteredData);
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
        if (displayedCategories.length > 0) {
            noProductsEl.style.display = 'none';
            displayedCategories.forEach((category, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td> <!-- Số thứ tự -->
                    <td>${category.category_name || 'N/A'}</td>
                    <td>${category.category_description || 'N/A'}</td>
                    <td>${getStatusText(category.status_id)}</td>
                    <td class="actions">
                        <div class="dropdown">
                            <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                            <div class="dropdown-content">
                                <a href="#" class="viewCategory" data-category-id="${category.category_id}">View Category <i class="fa fa-eye"></i></a>
                                <a href="#" class="editCategory" data-category-id="${category.category_id}">Edit Category <i class="fa fa-edit"></i></a>
                                <a href="#" class="deleteCategory" data-category-id="${category.category_id}">Delete Category <i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            noProductsEl.style.display = 'flex';
            tableBody.innerHTML = '<tr><td colspan="5">No categories found.</td></tr>';
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
                // Không cần gọi addEventListeners ở đây nữa vì chúng ta dùng event delegation
            } else {
                console.error('Error:', data.message);
                document.getElementById('table-body').innerHTML = '<tr><td colspan="5">Error loading categories.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('table-body').innerHTML = '<tr><td colspan="5">Error loading categories.</td></tr>';
        });

    // Hàm kiểm tra danh mục rỗng
    function checkNoProducts(categories) {
        const tableContainer = document.querySelector(".table-container");
        const noProductsEl = document.querySelector(".no-products");
        if (categories.length === 0) {
            tableContainer.scrollLeft = 0;
            tableContainer.style.overflow = "hidden";
            noProductsEl.style.display = "flex";
        } else {
            noProductsEl.style.display = "none";
            tableContainer.style.overflow = "auto";
        }
    }

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

        // Điền dữ liệu
        document.getElementById('modal-edit-category-id').value = category.category_id;
        document.getElementById('modal-edit-name').value = category.category_name || '';
        document.getElementById('modal-edit-desc').value = category.category_description || '';
        document.getElementById('modal-edit-status').value = category.status_id;

        // Xóa lỗi cũ
        clearFormErrors(form);

        // Gắn sự kiện submit (không dùng { once: true })
        form.removeEventListener('submit', handleEditSubmit); // Loại bỏ sự kiện cũ nếu có
        form.addEventListener('submit', handleEditSubmit); // Gắn lại sự kiện

        editModal.showModal();
    }

    // Hàm xử lý submit form chỉnh sửa
    function handleEditSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('modal-edit-form');
        const isError = validateModalFormInputs(form);

        if (!isError) {
            updateCategory(form);
        } else {
            const editModal = document.getElementById('edit-modal');
            editModal.scrollTop = 0;
        }
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
                    .catch(error => {
                        console.error('Có lỗi khi lấy dữ liệu thể loại:', error);
                    });
            } else {
                displayFormErrors(result.errors);
                editModal.scrollTop = 0;
            }
        })
        .catch(error => {
            console.error('Cập nhật thể loại thất bại:', error);
            const editModal = document.getElementById('edit-modal');
            editModal.scrollTop = 0;
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
    function validateAddModalFormInputs(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isError = false;

    inputs.forEach(input => {
        const value = input.value.trim();
        const errorEl = input.parentElement.querySelector('.modal-error');
        input.style.border = '';
        if (errorEl) errorEl.textContent = '';

        // Kiểm tra trường bắt buộc
        if (!value) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Trường này không được để trống!';
            return;
        }

        // Kiểm tra trường tên thể loại (modal-add-name)
        if (input.id === 'modal-add-name') {
            if (!/^[a-zA-Z\s-]+$/.test(value)) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Tên thể loại chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
            } else if (value.length > 20) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Tên thể loại không được vượt quá 20 ký tự';
            }
        }

        // Kiểm tra trường mô tả (modal-add-desc)
        if (input.id === 'modal-add-desc') {
            if (value.length > 400) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 400 ký tự';
            }
        }

        // Kiểm tra trường trạng thái (modal-add-status) - chỉ cần không rỗng, do select đã có option mặc định
        if (input.id === 'modal-add-status' && !value) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Vui lòng chọn trạng thái!';
        }
    });

    return isError;
}
    // Hàm validate form
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
                if (errorEl) errorEl.textContent = 'This field is required';
                return;
            }

            if (input.id === 'modal-edit-name') {
                if (!/^[a-zA-Z\s-]+$/.test(value)) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Tên thể loại chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
                } else if (value.length > 20) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Tên thể loại không được vượt quá 20 ký tự';
                }
            }

            if (input.id === 'modal-edit-desc') {
                if (value.length > 400) {
                    isError = true;
                    input.style.border = '1px solid var(--clr-error)';
                    if (errorEl) errorEl.textContent = 'Description must not exceed 400 characters';
                }
            }
        });

        return isError;
    }

    // Hàm tạo dropdown actions (không cần nữa vì đã dùng event delegation)
    function createActions(customEl, category) {
        const td = document.createElement("td");
        td.classList.add("actions");

        const dropdownDiv = document.createElement("div");
        dropdownDiv.classList.add("dropdown");

        const buttonEl = createActionsButton();

        const dropdownContentDiv = document.createElement("div");
        dropdownContentDiv.classList.add("dropdown-content");

        const { viewAnchor, editAnchor, deleteAnchor } = createActionAnchors(category);

        dropdownContentDiv.appendChild(viewAnchor);
        dropdownContentDiv.appendChild(editAnchor);
        dropdownContentDiv.appendChild(deleteAnchor);

        dropdownDiv.appendChild(buttonEl);
        dropdownDiv.appendChild(dropdownContentDiv);
        td.appendChild(dropdownDiv);
        return td;
    }

    function createActionsButton() {
        const buttonEl = document.createElement("button");
        buttonEl.classList.add("dropdownButton");
        const buttonI = document.createElement("i");
        buttonI.classList.add("fa", "fa-ellipsis-v", "dropIcon");
        buttonEl.appendChild(buttonI);
        return buttonEl;
    }

    function createActionAnchors(category) {
        const viewAnchor = createActionAnchorEl("View Category", "viewCategory", ["fa", "fa-eye"]);
        const editAnchor = createActionAnchorEl("Edit Category", "editCategory", ["fa", "fa-edit"]);
        const deleteAnchor = createActionAnchorEl("Delete Category", "deleteCategory", ["fa", "fa-trash"]);
        return { viewAnchor, editAnchor, deleteAnchor };
    }

    function createActionAnchorEl(text, className, iconClasses = []) {
        const anchorEl = document.createElement("a");
        anchorEl.classList.add(className);
        anchorEl.setAttribute("data-open-modal", true);
        anchorEl.setAttribute("data-category-id", category.category_id);

        const spanEl = document.createElement("span");
        spanEl.textContent = text;

        const anchorI = document.createElement("i");
        anchorI.classList.add(...iconClasses);

        anchorEl.appendChild(spanEl);
        anchorEl.appendChild(anchorI);
        return anchorEl;
    }

    // Trong hàm addModalData
function addModalData(modalEl, category, type) {
    if (type === "innerHTML") {
        modalEl.querySelector("#modal-view-category-id").textContent = category.category_id || 'N/A';
        modalEl.querySelector("#modal-view-name").textContent = category.category_name || 'N/A';
        modalEl.querySelector("#modal-view-desc").textContent = category.category_description || 'N/A';
        modalEl.querySelector("#modal-view-status").textContent = getStatusText(category.status_id);
    } else if (type === "value") {
        modalEl.querySelector("#modal-edit-category-id").value = category.category_id;
        modalEl.querySelector("#modal-edit-name").value = category.category_name || '';
        modalEl.querySelector("#modal-edit-desc").value = category.category_description || '';
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

    // Hàm xóa danh mục
    function deleteProduct() {
        const deleteModalEl = document.getElementById("delete-modal");
        const categoryId = deleteModalEl.getAttribute("data-category-id");
        fetch(`quanliloaisp/delete_category.php?id=${categoryId}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    fetch('quanliloaisp/fetch_categories.php')
                        .then(response => response.json())
                        .then(data => {
                            categories = data.data;
                            renderTable(categories);
                            deleteModalEl.close();
                        });
                }
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
                    .catch(error => {
                        console.error('Có lỗi khi lấy dữ liệu thể loại:', error);
                    });
            } else {
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
        formData.append('status_id', 6); // Đặt status_id thành 6

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
                console.error('Xóa thất bại:', result.message);
            }
        })
        .catch(error => console.error('Lỗi khi gửi yêu cầu xóa:', error));
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
</div>
    

    <?php
        include 'quanliloaisp/themloaisp.php'; // Add Modal
        include 'quanliloaisp/sualoaisp.php'; // Edieg Modal
        include 'quanliloaisp/xemloaisp.php';
        include 'quanliloaisp/xoaloaisp.php';
    ?>

</div>