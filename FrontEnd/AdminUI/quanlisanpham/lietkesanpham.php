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
    <h1 class="heading"> Quản lý <span>SẢN PHẨM</span></h1>
    <div class="toolbar">
        <div class="filters">
            <div class="filter-options-wrapper">
                <label for="filter-options" class="filter-label">Bộ lọc </label>
                <select id="filter-options">
                    <option value="product_name">Tên</option>
                    <option value="product_description">Mô Tả</option>
                    <option value="price">Giá</option>
                    <option value="stock_quantity">Số Lượng Tồn</option>
                    <option value="status_id">Trạng Thái</option>
                    <option value="categories">Thể Loại</option>
                </select>
            </div>
            <div class="search">
                <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
            </div>
        </div>
        <div class="toolbar-button-wrapper">
            <button class="toolbar-button add-product-button" id="add-product-toolbar" data-permission-id="1" data-action="Thêm">
                <span>Thêm sản phẩm</span>
                <i class="bx bx-plus-medical"></i>
            </button>
        </div>
    </div>

    <div id="selected-products"></div>

    <div class="table-container">
        <div class="no-products">
            <p>Có vẻ hiện tại bạn chưa có sản phẩm nào?</p>
        </div>

        <table class="table" id="data-table">
            <thead>
                <tr>
                    <th data-id="product_id">ID</th>
                    <th data-id="product_name">Tên Sản Phẩm</th>
                    <th data-id="product_description">Mô Tả</th>
                    <th data-id="image_url">Hình Ảnh</th>
                    <th data-id="categories">Thể Loại</th>
                    <th data-id="price">Giá</th>
                    <th data-id="stock_quantity">Số Lượng Tồn</th>
                    <th data-id="status_id">Trạng Thái</th>
                    <th class="actionsTH">Hành Động</th>
                </tr>
            </thead>
            <tbody id="table-body"></tbody>
        </table>
    </div>

    <!-- Script để fetch và hiển thị dữ liệu -->
    <script src="js/action-permission.js?v=<?php echo time(); ?>"></script>
    <script>
    // Override the getPermissionPath function to use the direct-fetch-permissions.php file
    function getPermissionPath() {
        return new Promise((resolve) => {
            // Use the direct-fetch-permissions.php file
            resolve('../includes/direct-fetch-permissions.php');
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Biến toàn cục
        let products = []; // Dữ liệu gốc, không thay đổi

        // Check and setup the add product button
        const addProductButton = document.querySelector("#add-product-toolbar");
        if (addProductButton) {
            console.log("Add product button found:", addProductButton);
            // Remove the disabled attribute to allow click events
            addProductButton.removeAttribute('disabled');
        } else {
            console.error("Add product button not found");
        }

        // Hàm chuyển status_id thành văn bản
        function getStatusText(statusId) {
            switch (statusId) {
                case 1: return 'Hoạt Động';
                case 2: return 'Không hoạt động';
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

                let filteredData = products;

                if (searchValue !== "") {
                    filteredData = products.filter((product) => {
                        if (filterBy === "categories") {
                            return product[filterBy] && product[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                        }
                        if (typeof product[filterBy] === "string") {
                            return product[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                        } else {
                            return product[filterBy].toString().includes(searchValue);
                        }
                    });
                }

                renderTable(filteredData);
            });

            filterOptionsEl.addEventListener("change", () => {
                searchEl.value = "";
                renderTable(products);
            });
        }
                // Hàm định dạng tiền tệ Việt Nam
        function formatCurrency(amount) {
            if (amount === null || amount === undefined || isNaN(amount)) {
                return '0 VNĐ';
            }
            // Chuyển đổi thành số và làm tròn 2 chữ số thập phân
            const number = parseFloat(amount).toFixed(0);
            // Thêm dấu phân cách hàng nghìn
            const formatted = number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return `${formatted} VNĐ`;
        }
        
        // Hàm render bảng
        function renderTable(displayedProducts) {
            const tableBody = document.getElementById('table-body');
            const noProductsEl = document.querySelector('.no-products');

            if (!tableBody || !noProductsEl) {
                console.error('Required elements not found: #table-body or .no-products');
                return;
            }

            tableBody.innerHTML = '';
            const activeProducts = displayedProducts.filter(product => product.status_id !== 6);

            if (activeProducts.length > 0) {
                noProductsEl.style.display = 'none';
                activeProducts.forEach((product, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${product.product_name || 'N/A'}</td>
                        <td>${product.product_description || 'N/A'}</td>
                        <td>${product.image_url ? `<img src="${product.image_url}" alt="${product.product_name}" style="width: 100px; height: 100px;object-fit: cover; border-radius: 4px;display: block; " />` : 'N/A'}</td>
                        <td>${product.categories || 'N/A'}</td>
                        <td>${formatCurrency(product.price)}</td> <!-- Sử dụng formatCurrency -->
                        <td>${product.stock_quantity || '0'}</td>
                        <td>${getStatusText(product.status_id)}</td>
                        <td class="actions">
                            <div class="dropdown">
                                <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                                <div class="dropdown-content">
                                    <a href="#" class="viewProduct" data-product-id="${product.product_id}" data-permission-id="1" data-action="Xem">Xem Sản Phẩm <i class="fa fa-eye"></i></a>
                                    <a href="#" class="editProduct" data-product-id="${product.product_id}" data-permission-id="1" data-action="Sửa">Sửa Sản Phẩm <i class="fa fa-edit"></i></a>
                                    <a href="#" class="deleteProduct" data-product-id="${product.product_id}" data-permission-id="1" data-action="Xóa">Xóa Sản Phẩm <i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                noProductsEl.style.display = 'flex';
                tableBody.innerHTML = '<tr><td colspan="9">Không tìm thấy sản phẩm.</td></tr>';
            }
        }

        // Fetch dữ liệu ban đầu từ server
        fetch('quanlisanpham/fetch_sanpham.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    products = data.data;
                    console.log('Initial products:', products);
                    renderTable(products);
                    addFilterEventListener();
                } else {
                    console.error('Error:', data.message);
                    document.getElementById('table-body').innerHTML = '<tr><td colspan="9">Lỗi khi tải sản phẩm.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('table-body').innerHTML = '<tr><td colspan="9">Lỗi khi tải sản phẩm.</td></tr>';
            });

        // Sử dụng event delegation để xử lý các hành động
        document.getElementById('table-body').addEventListener('click', (e) => {
            const target = e.target.closest('a');
            if (!target) return;

            e.preventDefault();
            const productId = parseInt(target.getAttribute('data-product-id')); // Chuyển đổi thành số
            const product = products.find(prod => prod.product_id === productId);
            const permissionId = parseInt(target.getAttribute('data-permission-id'));
            const action = target.getAttribute('data-action');

            if (!product) {
                console.error('Product not found:', productId);
                return;
            }

            if (target.classList.contains('viewProduct')) {
                checkActionPermissionBeforeAction(
                    permissionId,
                    action,
                    () => {
                        const viewModalEl = document.getElementById("view-modal");
                        addModalData(viewModalEl, product, "innerHTML");
                        viewModalEl.showModal();
                    },
                    () => {
                        alert("Bạn không có quyền xem sản phẩm.");
                    }
                );
            } else if (target.classList.contains('editProduct')) {
                checkActionPermissionBeforeAction(
                    permissionId,
                    action,
                    () => {
                        const editModalEl = document.getElementById("edit-modal");
                        openEditModal(product);
                    },
                    () => {
                        alert("Bạn không có quyền sửa sản phẩm.");
                    }
                );
            } else if (target.classList.contains('deleteProduct')) {
                checkActionPermissionBeforeAction(
                    permissionId,
                    action,
                    () => {
                        const deleteModalEl = document.getElementById("delete-modal");
                        deleteModalEl.setAttribute("data-product-id", productId);
                        deleteModalEl.showModal();
                    },
                    () => {
                        alert("Bạn không có quyền xóa sản phẩm.");
                    }
                );
            }
        });

        // Hàm mở modal chỉnh sửa
        function openEditModal(product) {
            const editModal = document.getElementById('edit-modal');
            const form = document.getElementById('modal-edit-form');
            const imagePreview = document.getElementById('edit-image-preview');

            document.getElementById('modal-edit-product-id').value = product.product_id;
            document.getElementById('modal-edit-name').value = product.product_name || '';
            document.getElementById('modal-edit-description').value = product.product_description || '';
            document.getElementById('modal-edit-status').value = product.status_id;

            // Điền dữ liệu thể loại
            const categorySelect = document.getElementById('modal-edit-categories');
            const selectedCategories = product.categories ? product.categories.split(', ') : [];
            Array.from(categorySelect.options).forEach(option => {
                option.selected = selectedCategories.includes(option.textContent);
            });

            // Hiển thị ảnh cũ (nếu có)
            if (product.image_url) {
                imagePreview.src = product.image_url;
                imagePreview.setAttribute('data-current-src', product.image_url); // Lưu ảnh cũ để khôi phục nếu cần
                imagePreview.style.display = 'block';
            } else {
                imagePreview.src = '';
                imagePreview.removeAttribute('data-current-src');
                imagePreview.style.display = 'none';
            }

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

            updateProduct(form);
        }

        // Hàm cập nhật sản phẩm
        function updateProduct(form) {
            const formData = new FormData(form);
            fetch('../../BackEnd/Model/quanlisanpham/xulisanpham.php', {
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
                    fetch('quanlisanpham/fetch_sanpham.php')
                        .then(response => response.json())
                        .then(data => {
                            products = data.data;
                            renderTable(products);
                            editModal.close();
                            const successMessage = document.getElementById('success-message');
                            successMessage.querySelector('.success-text p').textContent = result.message || 'Sản phẩm đã được cập nhật';
                            successMessage.style.display = 'block';
                            setTimeout(() => {
                                successMessage.style.display = 'none';
                            }, 3000);
                        })
                        .catch(error => console.error('Có lỗi khi lấy dữ liệu sản phẩm:', error));
                } else {
                    const errorContainer = editModal.querySelector('.modal-error');
                    errorContainer.textContent = result.message || 'Có lỗi khi cập nhật sản phẩm';
                    errorContainer.style.display = 'block';
                    errorContainer.style.color = 'var(--clr-error)';
                    editModal.scrollTop = 0;
                }
            })
            .catch(error => {
                console.error('Cập nhật sản phẩm thất bại:', error);
                const editModal = document.getElementById('edit-modal');
                editModal.scrollTop = 0;
            });
        }

        // Hàm thêm sản phẩm
        function addProduct(formEl) {
            const formData = new FormData(formEl);
            fetch('../../BackEnd/Model/quanlisanpham/xulisanpham.php', {
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
                    fetch('quanlisanpham/fetch_sanpham.php')
                        .then(response => response.json())
                        .then(data => {
                            products = data.data;
                            renderTable(products);
                            addProductModal.close();
                            const successMessage = document.getElementById('success-message');
                            successMessage.querySelector('.success-text p').textContent = result.message || 'Sản phẩm thêm thành công';
                            successMessage.style.display = 'block';
                            setTimeout(() => {
                                successMessage.style.display = 'none';
                            }, 3000);
                        })
                        .catch(error => console.error('Có lỗi khi lấy dữ liệu sản phẩm:', error));
                } else {
                    const errorContainer = addProductModal.querySelector('.modal-error');
                    errorContainer.textContent = result.message || 'Có lỗi khi thêm sản phẩm';
                    errorContainer.style.display = 'block';
                    errorContainer.style.color = 'var(--clr-error)';
                    addProductModal.scrollTop = 0;
                }
            })
            .catch(error => {
                console.error('Thêm sản phẩm thất bại:', error);
                const addProductModal = document.getElementById("add-modal");
                addProductModal.scrollTop = 0;
            });
        }

        // Hàm xóa sản phẩm
        function deleteProduct(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('status_id', 6);
    formData.append('action', 'delete');

    fetch('../../BackEnd/Model/quanlisanpham/xulisanpham.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        const successMessage = document.getElementById('success-message');
        const deleteModalEl = document.getElementById('delete-modal');
        if (result.status === 'success') {
            // Cập nhật lại danh sách sản phẩm
            fetch('quanlisanpham/fetch_sanpham.php')
                .then(response => response.json())
                .then(data => {
                    products = data.data;
                    renderTable(products);
                    deleteModalEl.close();
                    // Hiển thị thông báo thành công
                    successMessage.querySelector('.success-text p').textContent = result.message || 'Sản phẩm đã được đánh dấu xóa';
                    successMessage.style.display = 'block';
                    successMessage.style.backgroundColor = ''; // Khôi phục màu nền mặc định (thường là xanh)
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 3000);
                })
                .catch(error => console.error('Có lỗi khi lấy dữ liệu sản phẩm:', error));
        } else {
            // Hiển thị thông báo lỗi trong success-message và đóng modal
            deleteModalEl.close();
            successMessage.querySelector('.success-text p').textContent = result.message || 'Xóa thất bại';
            successMessage.style.display = 'block';
            successMessage.style.backgroundColor = 'var(--clr-error)'; // Đổi màu nền thành màu lỗi (thường là đỏ)
            setTimeout(() => {
                successMessage.style.display = 'none';
                successMessage.style.backgroundColor = ''; // Khôi phục màu nền sau khi ẩn
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Lỗi khi gửi yêu cầu xóa:', error);
        const successMessage = document.getElementById('success-message');
        const deleteModalEl = document.getElementById('delete-modal');
        deleteModalEl.close();
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
            const productId = parseInt(deleteModalEl.getAttribute('data-product-id'));
            deleteProduct(productId);
        });

        // Hàm xử lý modal Add
        function addViewProductModalEventListener() {
            const addProductModal = document.getElementById("add-modal");
            const formEl = document.getElementById("modal-add-form");
            const addCloseButton = addProductModal.querySelector("#add-close-button");
            const addProductToolbar = document.querySelector("#add-product-toolbar");

            // Check if the button exists
            if (addProductToolbar) {
                addProductToolbar.addEventListener("click", () => {
                    // Check if the user has permission to add products
                    checkActionPermissionBeforeAction(
                        1, // permission_id for "Quản lý sản phẩm"
                        "Thêm",
                        () => {
                            addProductModal.showModal();
                        },
                        () => {
                            alert("Bạn không có quyền thêm sản phẩm.");
                        }
                    );
                });
            } else {
                console.error("Add product button not found");
            }

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
                    if (value.length > 100) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Tên sản phẩm không được vượt quá 100 ký tự';
                    }
                }

                if (input.id === 'modal-edit-description') {
                    if (value.length > 1000) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 1000 ký tự';
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
                    if (value.length > 100) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Tên sản phẩm không được vượt quá 100 ký tự';
                    }
                }

                if (input.id === 'modal-add-description') {
                    if (value.length > 1000) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 1000 ký tự';
                    }
                }

            });

            return isError;
        }

        // Hàm thêm dữ liệu vào modal
function addModalData(modalEl, product, type) {
    if (type === "innerHTML") {
        modalEl.querySelector("#modal-view-product-id").textContent = product.product_id || 'N/A';
        modalEl.querySelector("#modal-view-name").textContent = product.product_name || 'N/A';
        modalEl.querySelector("#modal-view-description").textContent = product.product_description || 'N/A';
        const imageEl = modalEl.querySelector("#modal-view-image-url");
        imageEl.src = product.image_url || ''; // Điền URL vào src
        imageEl.alt = product.image_url ? 'Hình Ảnh Sản Phẩm' : 'Không có hình ảnh'; // Cập nhật alt
        modalEl.querySelector("#modal-view-categories").textContent = product.categories || 'N/A';
        modalEl.querySelector("#modal-view-price").textContent = formatCurrency(product.price); // Sử dụng formatCurrency
        modalEl.querySelector("#modal-view-stock-quantity").textContent = product.stock_quantity || '0';
        modalEl.querySelector("#modal-view-status").textContent = getStatusText(product.status_id);
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
                    // Reset form
                    formEl.reset();
                    // Xóa lỗi
                    clearFormErrors(formEl);
                    // Reset preview ảnh
                    const imagePreview = formEl.querySelector('.image-preview img');
                    if (imagePreview) {
                        imagePreview.src = '';
                        imagePreview.style.display = 'none';
                    }
                    // Reset select multiple (nếu có)
                    const selectMultiple = formEl.querySelector('select[multiple]');
                    if (selectMultiple) {
                        Array.from(selectMultiple.options).forEach(option => {
                            option.selected = false;
                        });
                    }
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
            // Reset form
            formEl.reset();
            // Xóa lỗi
            clearFormErrors(formEl);
            // Reset preview ảnh
            const imagePreview = formEl.querySelector('.image-preview img');
            if (imagePreview) {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
            // Reset select multiple (nếu có)
            const selectMultiple = formEl.querySelector('select[multiple]');
            if (selectMultiple) {
                Array.from(selectMultiple.options).forEach(option => {
                    option.selected = false;
                });
            }
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
    include 'quanlisanpham/themsanpham.php'; // Add Modal
    include 'quanlisanpham/suasanpham.php'; // Edit Modal
    include 'quanlisanpham/xemsanpham.php';  // View Modal
    include 'quanlisanpham/xoasanpham.php';  // Delete Modal
?>

</body>
</html>