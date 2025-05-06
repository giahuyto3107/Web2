<?php
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        error_log("User ID not set in lietkephieunhap.php, redirecting to login");
        header("Location: /Web2/FrontEnd/AdminUI/login signup/login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    echo $user_id;
    error_log("User ID in lietkephieunhap.php: " . $user_id);
?>

<body>
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
        <h1 class="heading"> Quản lý <span>PHÂN QUYỀN</span></h1>
        <div class="toolbar">
            <div class="filters">
                <div class="filter-options-wrapper">
                    <label for="filter-options" class="filter-label">Bộ lọc </label>
                    <select id="filter-options">
                        <option value="permission_name">Tên quyền</option>
                        <option value="permission_description">Mô tả</option>
                        <option value="status_id">Trạng thái</option>
                    </select>
                </div>
                <div class="search">
                    <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
                </div>
            </div>
            <div class="toolbar-button-wrapper">
                <button class="toolbar-button add-product-button" id="add-product-toolbar" data-permission-id="11" data-action="Thêm">
                    <span>Thêm phân quyền</span>
                    <i class="bx bx-plus-medical"></i>
                </button>
            </div>
        </div>

        <div id="selected-products"></div>

        <div class="table-container">
            <div class="no-products">
                <p>Có vẻ như bạn chưa có phân quyền nào hết.</p>
            </div>

            <table class="table" id="data-table">
                <thead>
                    <tr>
                        <th data-id="permission_id">ID</th>
                        <th data-id="permission_name">Tên quyền</th>
                        <th data-id="permission_description">Mô tả</th>
                        <th data-id="status_id">Trạng thái</th>
                        <th class="actionsTH">Hành động</th>
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>
        </div>

        <!-- Script để fetch và hiển thị dữ liệu -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Biến toàn cục
            let permissions = []; // Dữ liệu gốc, không thay đổi

            // Hàm chuyển status_id thành văn bản
            function getStatusText(statusId) {
                switch (statusId) {
                    case "1": return 'Hoạt động';
                    case "2": return 'Không hoạt động';
                    case "6": return 'Deleted';
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

                    let filteredData = permissions;

                    if (searchValue !== "") {
                        filteredData = permissions.filter((permission) => {
                            if (typeof permission[filterBy] === "string") {
                                return permission[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                            } else {
                                return permission[filterBy].toString().includes(searchValue);
                            }
                        });
                    }

                    renderTable(filteredData);
                });

                filterOptionsEl.addEventListener("change", () => {
                    searchEl.value = "";
                    renderTable(permissions);
                });
            }

            // Hàm render bảng
            function renderTable(displayedPermissions) {
                const tableBody = document.getElementById('table-body');
                const noProductsEl = document.querySelector('.no-products');

                if (!tableBody || !noProductsEl) {
                    console.error('Required elements not found: #table-body or .no-products');
                    return;
                }

                tableBody.innerHTML = '';
                const activePermissions = displayedPermissions.filter(permission => permission.status_id !== 6);

                if (activePermissions.length > 0) {
                    noProductsEl.style.display = 'none';
                    activePermissions.forEach((permission, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${permission.permission_name || 'N/A'}</td>
                            <td>${permission.permission_description || 'N/A'}</td>
                            <td>${getStatusText(permission.status_id)}</td>
                            <td class="actions">
                                <div class="dropdown">
                                    <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                                    <div class="dropdown-content">
                                        <a href="#" class="viewPermission" data-permission-id="11" data-action="Xem" data-permission-id="${permission.permission_id}">Xem Phân Quyền <i class="fa fa-eye"></i></a>
                                        <a href="#" class="editPermission" data-permission-id="11" data-action="Sửa" data-permission-id="${permission.permission_id}">Sửa Phân Quyền <i class="fa fa-edit"></i></a>
                                        <a href="#" class="deletePermission" data-permission-id="11" data-action="Xóa" data-permission-id="${permission.permission_id}">Xóa Phân Quyền <i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    noProductsEl.style.display = 'flex';
                    tableBody.innerHTML = '<tr><td colspan="5">Không tìm thấy quyền nào.</td></tr>';
                }
            }

            // Fetch dữ liệu ban đầu từ server
            fetch('quanliphanquyen/fetch_phanquyen.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        permissions = data.data;
                        console.log('Initial permissions:', permissions);
                        renderTable(permissions);
                        addFilterEventListener();
                    } else {
                        console.error('Error:', data.message);
                        document.getElementById('table-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải danh sách quyền.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('table-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải danh sách quyền.</td></tr>';
                });

            // Sử dụng event delegation để xử lý các hành động
            document.getElementById('table-body').addEventListener('click', (e) => {
                const target = e.target.closest('a');
                if (!target) return;

                e.preventDefault();
                const permissionId = target.getAttribute('data-permission-id');
                const permission = permissions.find(per => per.permission_id === permissionId);

                if (!permission) {
                    console.error('Permission not found:', permissionId);
                    return;
                }

                if (target.classList.contains('viewPermission')) {
                    const viewModalEl = document.getElementById("view-modal");
                    addModalData(viewModalEl, permission, "innerHTML");
                    viewModalEl.showModal();
                } else if (target.classList.contains('editPermission')) {
                    const editModalEl = document.getElementById("edit-modal");
                    openEditModal(permission);
                } else if (target.classList.contains('deletePermission')) {
                    const deleteModalEl = document.getElementById("delete-modal");
                    deleteModalEl.setAttribute("data-permission-id", permissionId);
                    deleteModalEl.showModal();
                }
            });

            // Hàm mở modal chỉnh sửa
            function openEditModal(permission) {
                const editModal = document.getElementById('edit-modal');
                const form = document.getElementById('modal-edit-form');

                document.getElementById('modal-edit-permission-id').value = permission.permission_id;
                document.getElementById('modal-edit-name').value = permission.permission_name || '';
                document.getElementById('modal-edit-desc').value = permission.permission_description || '';
                document.getElementById('modal-edit-status').value = permission.status_id;

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
                    errorContainer.textContent = 'Vui lòng sửa các lỗi trên form trước khi submit.';
                    errorContainer.style.display = 'block';
                    errorContainer.style.color = 'var(--clr-error)';
                    editModal.scrollTop = 0;
                    return;
                }

                updatePermission(form);
            }

            // Hàm cập nhật quyền
            function updatePermission(form) {
                const formData = new FormData(form);
                fetch('../../BackEnd/Model/quanliphanquyen/xuliphanquyen.php', {
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
                        fetch('quanliphanquyen/fetch_phanquyen.php')
                            .then(response => response.json())
                            .then(data => {
                                permissions = data.data;
                                renderTable(permissions);
                                editModal.close();
                                const successMessage = document.getElementById('success-message');
                                successMessage.querySelector('.success-text p').textContent = result.message || 'Quyền đã được cập nhật';
                                successMessage.style.display = 'block';
                                setTimeout(() => {
                                    successMessage.style.display = 'none';
                                }, 3000);
                            })
                            .catch(error => console.error('Có lỗi khi lấy dữ liệu quyền:', error));
                    } else {
                        const errorContainer = editModal.querySelector('.modal-error');
                        errorContainer.textContent = result.message || 'Có lỗi khi cập nhật quyền';
                        errorContainer.style.display = 'block';
                        errorContainer.style.color = 'var(--clr-error)';
                        editModal.scrollTop = 0;
                    }
                })
                .catch(error => {
                    console.error('Cập nhật quyền thất bại:', error);
                    const editModal = document.getElementById('edit-modal');
                    editModal.scrollTop = 0;
                });
            }

            // Hàm thêm quyền
            function addProduct(formEl) {
                const formData = new FormData(formEl);
                fetch('../../BackEnd/Model/quanliphanquyen/xuliphanquyen.php', {
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
                        fetch('quanliphanquyen/fetch_phanquyen.php')
                            .then(response => response.json())
                            .then(data => {
                                permissions = data.data;
                                renderTable(permissions);
                                addProductModal.close();
                                const successMessage = document.getElementById('success-message');
                                successMessage.querySelector('.success-text p').textContent = result.message || 'Quyền thêm thành công';
                                successMessage.style.display = 'block';
                                setTimeout(() => {
                                    successMessage.style.display = 'none';
                                }, 3000);
                            })
                            .catch(error => console.error('Có lỗi khi lấy dữ liệu quyền:', error));
                    } else {
                        const errorContainer = addProductModal.querySelector('.modal-error');
                        errorContainer.textContent = result.message || 'Có lỗi khi thêm quyền';
                        errorContainer.style.display = 'block';
                        errorContainer.style.color = 'var(--clr-error)';
                        addProductModal.scrollTop = 0;
                    }
                })
                .catch(error => {
                    console.error('Thêm quyền thất bại:', error);
                    const addProductModal = document.getElementById("add-modal");
                    addProductModal.scrollTop = 0;
                });
            }

            // Hàm xóa quyền (cập nhật status_id thành 6)
            function deleteProduct(permissionId) {
                const formData = new FormData();
                formData.append('permission_id', permissionId);
                formData.append('status_id', 6);

                fetch('../../BackEnd/Model/quanliphanquyen/xuliphanquyen.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        fetch('quanliphanquyen/fetch_phanquyen.php')
                            .then(response => response.json())
                            .then(data => {
                                permissions = data.data;
                                renderTable(permissions);
                                const deleteModalEl = document.getElementById('delete-modal');
                                deleteModalEl.close();
                                const successMessage = document.getElementById('success-message');
                                successMessage.querySelector('.success-text p').textContent = result.message || 'Quyền đã được đánh dấu xóa';
                                successMessage.style.display = 'block';
                                setTimeout(() => {
                                    successMessage.style.display = 'none';
                                }, 3000);
                            })
                            .catch(error => console.error('Có lỗi khi lấy dữ liệu quyền:', error));
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
                const permissionId = parseInt(deleteModalEl.getAttribute('data-permission-id'));
                deleteProduct(permissionId);
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
                        if (!/^[a-zA-Z\s-]+$/.test(value)) {
                            isError = true;
                            input.style.border = '1px solid var(--clr-error)';
                            if (errorEl) errorEl.textContent = 'Tên quyền chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
                        } else if (value.length > 50) {
                            isError = true;
                            input.style.border = '1px solid var(--clr-error)';
                            if (errorEl) errorEl.textContent = 'Tên quyền không được vượt quá 50 ký tự';
                        }
                    }

                    if (input.id === 'modal-edit-desc') {
                        if (value.length > 200) {
                            isError = true;
                            input.style.border = '1px solid var(--clr-error)';
                            if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 200 ký tự';
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
                        if (!/^[a-zA-Z\s-]+$/.test(value)) {
                            isError = true;
                            input.style.border = '1px solid var(--clr-error)';
                            if (errorEl) errorEl.textContent = 'Tên quyền chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
                        } else if (value.length > 50) {
                            isError = true;
                            input.style.border = '1px solid var(--clr-error)';
                            if (errorEl) errorEl.textContent = 'Tên quyền không được vượt quá 50 ký tự';
                        }
                    }

                    if (input.id === 'modal-add-desc') {
                        if (value.length > 200) {
                            isError = true;
                            input.style.border = '1px solid var(--clr-error)';
                            if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 200 ký tự';
                        }
                    }
                });

                return isError;
            }

            // Hàm thêm dữ liệu vào modal
            function addModalData(modalEl, permission, type) {
                if (type === "innerHTML") {
                    modalEl.querySelector("#modal-view-permission-id").textContent = permission.permission_id || 'N/A';
                    modalEl.querySelector("#modal-view-name").textContent = permission.permission_name || 'N/A';
                    modalEl.querySelector("#modal-view-desc").textContent = permission.permission_description || 'N/A';
                    modalEl.querySelector("#modal-view-status").textContent = getStatusText(permission.status_id);
                } else if (type === "value") {
                    modalEl.querySelector("#modal-edit-permission-id").value = permission.permission_id;
                    modalEl.querySelector("#modal-edit-name").value = permission.permission_name || '';
                    modalEl.querySelector("#modal-edit-desc").value = permission.permission_description || '';
                    modalEl.querySelector("#modal-edit-status").value = permission.status_id;
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
    </div>

    <!-- Modal -->
    <dialog data-modal id="add-modal">
        <div class="modal-header">
            <h2>Thêm Quyền</h2>
            <button class="modal-close" data-id="add-modal">
                <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
            </button>
        </div>
        <div class="modal-content">
            <form id="modal-add-form" class="modal-form">
                <div class="modal-input">
                    <span>Tên quyền</span>
                    <input type="text" id="modal-add-name" name="permission_name" required />
                    <p class="modal-error" id="modal-add-name-error"></p>
                </div>
                <div class="modal-input">
                    <span>Mô tả</span>
                    <textarea id="modal-add-desc" name="permission_description" required></textarea>
                    <p class="modal-error" id="modal-add-desc-error"></p>
                </div>
                <div class="modal-input">
                    <span>Trạng thái</span>
                    <select id="modal-add-status" name="status_id" required>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
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

    <dialog data-modal id="edit-modal">
        <div class="modal-header">
            <h2>Chỉnh sửa Quyền</h2>
            <button class="modal-close" data-id="edit-modal">
                <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
            </button>
        </div>
        <div class="modal-content">
            <form id="modal-edit-form" class="modal-form">
                <input type="hidden" id="modal-edit-permission-id" name="permission_id" />
                <div class="modal-input">
                    <span>Tên quyền</span>
                    <input type="text" id="modal-edit-name" name="permission_name" required />
                    <p class="modal-error" id="modal-edit-name-error"></p>
                </div>
                <div class="modal-input">
                    <span>Mô tả</span>
                    <textarea id="modal-edit-desc" name="permission_description" required></textarea>
                    <p class="modal-error" id="modal-edit-desc-error"></p>
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

    <dialog data-modal id="view-modal">
        <div class="modal-header">
            <h2>Chi tiết Quyền</h2>
            <button class="modal-close" data-id="view-modal">
                <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
            </button>
        </div>
        <div class="view-container">
            <div class="view-content">
                <span>ID</span>
                <p id="modal-view-permission-id">N/A</p>
            </div>
            <div class="view-content">
                <span>Tên quyền</span>
                <p id="modal-view-name">N/A</p>
            </div>
            <div class="view-content">
                <span>Mô tả</span>
                <p id="modal-view-desc">N/A</p>
            </div>
            <div class="view-content">
                <span>Trạng thái</span>
                <p id="modal-view-status">N/A</p>
            </div>
            <div class="modal-buttons">
                <button class="close" id="view-close-button">Đóng</button>
            </div>
        </div>
    </dialog>

    <dialog data-modal id="delete-modal">
        <div class="delete-modal-wrapper">
            <h2>Cảnh báo!</h2>
            <div class="delete-modal-text">
                <p>Bạn có muốn xóa quyền này?</p>
                <div class="modal-buttons">
                    <button class="cancel" id="delete-close-button">Hủy bỏ</button>
                    <button class="delete" id="delete-delete-button">Xóa quyền</button>
                </div>
            </div>
        </div>
    </dialog>
</body>
</html>