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
    <h1 class="heading"><span>Products</span> Dashboard</h1>
    <div class="toolbar">
        <div class="filters">
            <div class="filter-options-wrapper">
                <label for="filter-options" class="filter-label">Filters </label>
                <select name="filter-options" id="filter-options">
                    <option value="id">ID</option>
                    <option value="name">Name</option>
                    <option value="title">Title</option>
                    <option value="vendor">Vendor</option>
                    <option value="in_stock">In Stock</option>
                    <option value="buying_price">Buying Price</option>
                    <option value="sale_price">Sale Price</option>
                    <option value="purchase_quantity">Purchase Quantity</option>
                    <option value="product_type">Product Type</option>
                    <option value="shipping_rates">Shipping Rates</option>
                    <option value="refill_limit">Refill Limit</option>
                    <option value="product_location">Address</option>
                </select>
            </div>
            <div class="search">
                <input type="text" id="search-text" name="search-text" placeholder="search..." />
            </div>
        </div>
        <div class="toolbar-button-wrapper">
            <button class="toolbar-button add-product-button" id="add-product-toolbar">
                <span>Add Product</span>
                <i class="bx bx-plus-medical"></i>
            </button>
        </div>
    </div>
    <div class="table-container">
    <div class="no-products">
        <p>Looks like you do not have any products.</p>
    </div>

    <table class="table" id="data-table">
    <thead>
        <tr>
            <th class="fixed-column"></th>
            <th class="sortable" data-id="product_id">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Product ID</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="product_name">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Product Name</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="product_description">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Description</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="price">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Price</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="stock_quantity">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Stock Quantity</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="category_names">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Categories</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="status_id">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Status ID</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="image_url">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Image URL</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="created_at">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Created At</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="sortable" data-id="updated_at">
                <div class="sortable-wrapper">
                    <span class="sortable-heading" onselectstart="return false;">Updated At</span>
                    <span class="sort-arrows">
                        <i class="fa fa-caret-up arrow-up"></i>
                        <i class="fa fa-caret-down arrow-down"></i>
                    </span>
                </div>
            </th>
            <th class="actionsTH">Actions</th>
        </tr>
    </thead>
    <tbody id="table-body">
        <!-- Dữ liệu sẽ được thêm bởi JavaScript -->
    </tbody>
</table>

<!-- Script để fetch và hiển thị dữ liệu -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hàm render bảng
    function renderTable(products) {
    const tableBody = document.getElementById('table-body');
    tableBody.innerHTML = '';
    checkNoProducts(products);
    if (products.length > 0) {
        products.forEach(product => {
            let statusText;
            switch (product.status_id) {
                case "1":
                    statusText = 'Hoạt động';
                    break;
                case "2":
                    statusText = 'Không hoạt động';
                    break;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="fixed-column"><input type="checkbox" class="selectBox" data-id="selectbox-${product.product_id}"></td>
                <td>${product.product_id}</td>
                <td>${product.product_name}</td>
                <td>${product.product_description}</td>
                <td>${product.price}</td>
                <td>${product.stock_quantity}</td>
                <td>${product.category_names.length > 0 ? product.category_names.join(', ') : 'N/A'}</td>
                <td>${statusText}</td>
                <td><img src="${product.image_url}" alt="Product Image" style="max-width: 50px;"></td>
                <td>${product.created_at}</td>
                <td>${product.updated_at}</td>
                <td class="actions">
                    <div class="dropdown">
                        <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                        <div class="dropdown-content">
                            <a href="#" class="viewProduct" data-product-id="${product.product_id}">View Product <i class="fa fa-eye"></i></a>
                            <a href="#" class="editProduct" data-product-id="${product.product_id}">Edit Product <i class="fa fa-edit"></i></a>
                            <a href="#" class="deleteProduct" data-product-id="${product.product_id}">Delete Product <i class="fa fa-trash"></i></a>
                        </div>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="12">No products found.</td></tr>';
    }
}

    // Hàm kiểm tra sản phẩm rỗng
    function checkNoProducts(products) {
        const tableContainer = document.querySelector(".table-container");
        const noProductsEl = document.querySelector(".no-products");
        if (products.length === 0) {
            tableContainer.scrollLeft = 0;
            tableContainer.style.overflow = "hidden";
            noProductsEl.style.display = "flex";
        } else {
            noProductsEl.style.display = "none";
            tableContainer.style.overflow = "auto";
        }
    }

    // Fetch dữ liệu ban đầu
    fetch('quanlisanpham/fetch_products.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderTable(data.data);
            } else {
                console.error('Error:', data.message);
                document.getElementById('table-body').innerHTML = '<tr><td colspan="12">Error loading products.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('table-body').innerHTML = '<tr><td colspan="12">Error loading products.</td></tr>';
        });

    function createActions(customEl, product) {
        const td = document.createElement("td");
        td.classList.add("actions");

        const dropdownDiv = document.createElement("div");
        dropdownDiv.classList.add("dropdown");

        const buttonEl = createActionsButton();

        const dropdownContentDiv = document.createElement("div");
        dropdownContentDiv.classList.add("dropdown-content");

        const { viewAnchor, editAnchor, deleteAnchor } = createActionAnchors(product);

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

    function createActionAnchors(product) {
        const viewAnchor = createActionAnchorEl("View Product", "viewProduct", ["fa", "fa-eye"]);
        const editAnchor = createActionAnchorEl("Edit Product", "editProduct", ["fa", "fa-edit"]);
        const deleteAnchor = createActionAnchorEl("Delete Product", "deleteProduct", ["fa", "fa-trash"]);

        const viewModalEl = document.getElementById("view-modal");
        const editModalEl = document.getElementById("edit-modal");
        const deleteModalEl = document.getElementById("delete-modal");

        viewAnchor.addEventListener("click", () => {
            addModalData(viewModalEl, product, "innerHTML");
            viewModalEl.showModal();
        });

        editAnchor.addEventListener("click", () => {
            addModalData(editModalEl, product, "value");
            editModalEl.setAttribute("data-product-id", product.product_id);
            editModalEl.showModal();
        });

        deleteAnchor.addEventListener("click", () => {
            deleteModalEl.setAttribute("data-product-id", product.product_id);
            deleteModalEl.showModal();
        });

        return { viewAnchor, editAnchor, deleteAnchor };
    }

    function createActionAnchorEl(text, className, iconClasses = []) {
        const anchorEl = document.createElement("a");
        anchorEl.classList.add(className);
        anchorEl.setAttribute("data-open-modal", true);

        const spanEl = document.createElement("span");
        spanEl.textContent = text;

        const anchorI = document.createElement("i");
        anchorI.classList.add(...iconClasses);

        anchorEl.appendChild(spanEl);
        anchorEl.appendChild(anchorI);
        return anchorEl;
    }

    function addModalData(modalEl, product, type) {
        if (type === "innerHTML") {
            modalEl.querySelector("#view-content").innerHTML = `
                <p>ID: ${product.product_id}</p>
                <p>Name: ${product.product_name}</p>
                <p>Description: ${product.product_description || 'N/A'}</p>
                <p>Price: ${product.price}</p>
                <p>Stock Quantity: ${product.stock_quantity}</p>
                <p>Categories: ${product.category_names.length > 0 ? product.category_names.join(', ') : 'N/A'}</p>
                <p>Status: ${getStatusText(product.status_id)}</p>
                <p>Image URL: ${product.image_url}</p>
                <p>Created At: ${product.created_at}</p>
                <p>Updated At: ${product.updated_at}</p>
            `;
        } else if (type === "value") {
            modalEl.querySelector("#modal-edit-product-id").value = product.product_id;
            modalEl.querySelector("#modal-edit-name").value = product.product_name;
            modalEl.querySelector("#modal-edit-desc").value = product.product_description || '';
            const categorySelect = modalEl.querySelector("#modal-edit-category");
            product.category_ids.forEach(catId => {
                const option = categorySelect.querySelector(`option[value="${catId}"]`);
                if (option) option.selected = true;
            });
            modalEl.querySelector("#modal-edit-status").value = product.status_id;
            modalEl.querySelector("#modal-edit-image").value = product.image_url || '';
        }
    }

    function getStatusText(statusId) {
        switch (statusId) {
            case "1": return 'Hoạt động';
            case "2": return 'Không hoạt động';
            case "3": return 'Đang chờ xử lý';
            case "4": return 'Hoàn thành';
            case "5": return 'Thất bại';
            case "6": return 'Đã xóa';
            default: return 'N/A';
        }
    }

    // Hàm xóa sản phẩm (giả lập)
    function deleteProduct() {
        const deleteModalEl = document.getElementById("delete-modal");
        const productId = deleteModalEl.getAttribute("data-product-id");
        fetch(`quanlisanpham/delete_product.php?id=${productId}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    fetch('quanlisanpham/fetch_products.php')
                        .then(response => response.json())
                        .then(data => renderTable(data.data));
                    deleteModalEl.close();
                }
            });
    }
    // Hàm thêm sản phẩm (giả lập gửi dữ liệu lên server)
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
                // Cập nhật danh sách sản phẩm
                fetch('quanlisanpham/xulisanpham.php')
                    .then(response => response.json())
                    .then(data => {
                        renderTable(data.data);
                        addProductModal.close();
                        // Hiển thị thông báo thành công
                        alert('Product added successfully!');
                    })
                    .catch(error => {
                        console.error('Error fetching updated products:', error);
                        document.getElementById('modal-add-category-error').textContent = 'Failed to refresh product list';
                    });
            } else {
                // Hiển thị lỗi từ server
                document.getElementById('modal-add-category-error').textContent = result.message || 'An error occurred while adding the product';
                addProductModal.scrollTop = 0;
            }
        })
        .catch(error => {
            console.error('Add product error:', error);
            document.getElementById('modal-add-category-error').textContent = 'Network error. Please try again.';
            const addProductModal = document.getElementById("add-modal");
            addProductModal.scrollTop = 0;
        });
    }

    function validateModalFormInputs(formEl) {
        const inputs = formEl.querySelectorAll('input[required], select[required]');
        const checkboxes = formEl.querySelectorAll('input[name="category_ids[]"]');
        let isError = false;

        // Kiểm tra các input/select bắt buộc
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isError = true;
                input.style.border = '1px solid var(--clr-error)';
                const errorEl = input.parentElement.querySelector('.modal-error');
                if (errorEl) errorEl.textContent = 'This field is required';
            } else {
                input.style.border = '';
                const errorEl = input.parentElement.querySelector('.modal-error');
                if (errorEl) errorEl.textContent = '';
            }
        });

        // Kiểm tra ít nhất một checkbox được chọn
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        if (checkedCount === 0) {
            isError = true;
            document.getElementById('modal-add-category-error').textContent = 'Please select at least one category';
        } else {
            document.getElementById('modal-add-category-error').textContent = '';
        }

        return isError;
    }

    function loadCategories() {
        const checkboxContainer = document.getElementById('modal-add-category-checkboxes');
        
        fetch('quanliloaisp/fetch_categories.php')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    checkboxContainer.innerHTML = ''; // Xóa checkbox cũ
                    result.data.forEach(category => {
                        const label = document.createElement('label');
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = 'category_ids[]';
                        checkbox.value = category.category_id;
                        label.appendChild(checkbox);
                        label.append(` ${category.category_name}`);
                        checkboxContainer.appendChild(label);
                    });
                } else {
                    console.error('Fetch categories error:', result.message);
                }
            })
            .catch(error => console.error('Fetch categories error:', error));
    }

    function addViewProductModalEventListener() {
        const addProductModal = document.getElementById("add-modal");
        const formEl = document.getElementById("modal-add-form");
        const addCloseButton = addProductModal.querySelector("#add-close-button");

        document.querySelector("#add-product-toolbar").addEventListener("click", () => {
            loadCategories();
            addProductModal.showModal();
        });

        addCloseButton.addEventListener("click", () => {
            addProductModal.close();
        });

        formEl.addEventListener("submit", (e) => {
            e.preventDefault();
            const isError = validateModalFormInputs(formEl);
            if (!isError) {
                addProduct(formEl);
            } else {
                addProductModal.scrollTop = 0;
            }
        });
    }

    // Gọi hàm để thêm sự kiện
    addViewProductModalEventListener();
});
</script>
</div>
    <div id="selected-products" class="selected-products"></div>

    <!-- Include các modal từ các file riêng -->
    <?php
        include 'quanlisanpham/themsanphamtest.php'; // Add Modal
        include 'quanlisanpham/suasanphamtest.php'; // Add Modal
    ?>
</div>