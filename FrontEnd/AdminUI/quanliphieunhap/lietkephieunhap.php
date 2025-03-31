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
        <h1 class="heading"> Quản lý <span>PHIẾU NHẬP</span></h1>
        <div class="toolbar">
            <div class="filters">
                <div class="filter-options-wrapper">
                    <label for="filter-options" class="filter-label">Bộ lọc </label>
                    <select id="filter-options">
                        <option value="purchase_order_id">Mã đơn</option>
                        <option value="supplier_name">Nhà xuất bản</option>
                        <option value="user_name">Nhân viên</option>
                        <option value="order_date">Ngày lập</option>
                    </select>
                </div>
                <div class="search">
                    <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
                </div>
            </div>
        </div>

        <div id="selected-products"></div>

        <div class="table-container">
            <div class="no-products">
                <p>Không có phiếu nhập nào được tìm thấy.</p>
            </div>

            <table class="table" id="purchase-order-table">
                <thead>
                    <tr>
                        <th data-id="purchase_order_id">Mã đơn</th>
                        <th data-id="supplier_name">Nhà Xuất Bản</th>
                        <th data-id="user_name">Nhân Viên</th>
                        <th data-id="order_date">Ngày Lập</th>
                        <th data-id="amount">Số lượng</th>
                        <th data-id="total_price">Tổng tiền</th>
                        <th class="actionsTH">Hành động</th>
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>
        </div>

        <!-- Include modal -->
        <?php
            include 'quanliphieunhap/xemchitietphieunhap.php'; // View Modal
            include 'quanliphieunhap/duyetdon.php'; // Approve Modal
        ?>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Biến toàn cục
            let purchaseOrders = [];

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

                    let filteredData = purchaseOrders;

                    if (searchValue !== "") {
                        filteredData = purchaseOrders.filter((order) => {
                            if (typeof order[filterBy] === "string") {
                                return order[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                            } else {
                                return order[filterBy].toString().includes(searchValue);
                            }
                        });
                    }

                    renderTable(filteredData);
                });

                filterOptionsEl.addEventListener("change", () => {
                    searchEl.value = "";
                    renderTable(purchaseOrders);
                });
            }

            // Hàm render bảng
            function renderTable(data) {
                const tableBody = document.getElementById('table-body');
                const noProductsEl = document.querySelector('.no-products');

                if (!tableBody || !noProductsEl) {
                    console.error('Required elements not found: #table-body or .no-products');
                    return;
                }

                tableBody.innerHTML = '';

                if (data.length === 0) {
                    noProductsEl.style.display = 'flex';
                    tableBody.innerHTML = '<tr><td colspan="7">Không có phiếu nhập nào.</td></tr>';
                    return;
                }

                noProductsEl.style.display = 'none';
                data.forEach(order => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${order.purchase_order_id}</td>
                        <td>${order.supplier_name || 'N/A'}</td>
                        <td>${order.user_name || 'N/A'}</td>
                        <td>${order.order_date || 'N/A'}</td>
                        <td>${order.amount || '0'}</td>
                        <td>${order.total_price || '0'}</td>
                        <td class="actions">
                            <div class="dropdown">
                                <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                                <div class="dropdown-content">
                                    <a href="#" class="viewPurchaseOrder" data-purchase-order-id="${order.purchase_order_id}">Xem Chi Tiết <i class="fa fa-eye"></i></a>
                                    ${order.import_status == 0 ? 
                                        `<a href="#" class="approvePurchaseOrder" data-purchase-order-id="${order.purchase_order_id}">Duyệt Đơn <i class="fa fa-check"></i></a>` : 
                                        ''}
                                </div>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }

            // Fetch dữ liệu ban đầu từ server
            fetch('quanliphieunhap/fetch_phieunhap.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        purchaseOrders = data.data;
                        console.log('Fetched purchase orders:', purchaseOrders);
                        renderTable(purchaseOrders);
                        addFilterEventListener();
                    } else {
                        console.error('Error:', data.message);
                        document.getElementById('table-body').innerHTML = '<tr><td colspan="7">Lỗi khi tải danh sách phiếu nhập: ' + data.message + '</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('table-body').innerHTML = '<tr><td colspan="7">Lỗi khi tải danh sách phiếu nhập.</td></tr>';
                });

            // Hàm lấy và hiển thị chi tiết phiếu nhập
            function loadPurchaseOrderItems(purchaseOrderId) {
                fetch(`quanliphieunhap/fetch_phieunhap_items.php?purchase_order_id=${purchaseOrderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== 'success') {
                            console.error('Lỗi khi tải chi tiết phiếu nhập:', data.message);
                            return;
                        }

                        const orderInfo = data.data.order_info;
                        const items = data.data.items;
                        const totalValue = data.data.total_value;

                        // Điền thông tin chung
                        document.getElementById('modal-view-user-name').textContent = orderInfo.user_name || 'N/A';
                        document.getElementById('modal-view-supplier-name').textContent = orderInfo.supplier_name || 'N/A';
                        document.getElementById('modal-view-order-date').textContent = orderInfo.order_date || 'N/A';
                        document.getElementById('modal-view-total-value').textContent = totalValue.toLocaleString() + ' VND';

                        // Hiển thị danh sách sản phẩm
                        const tableBody = document.getElementById('purchase-order-items-body');
                        tableBody.innerHTML = '';

                        if (items.length > 0) {
                            items.forEach(item => {
                                const row = `
                                    <tr>
                                        <td>${item.product_id || 'N/A'}</td>
                                        <td>${item.product_name || 'N/A'}</td>
                                        <td>${item.profit || '0'}</td>
                                        <td>${item.quantity || '0'}</td>
                                        <td>${item.price || '0'}</td>
                                    </tr>
                                `;
                                tableBody.innerHTML += row;
                            });
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="5">Không có sản phẩm trong phiếu nhập.</td></tr>';
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi tải chi tiết:', error);
                        document.getElementById('purchase-order-items-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải chi tiết phiếu nhập.</td></tr>';
                    });
            }

            // Hàm duyệt đơn
            function approvePurchaseOrder(purchaseOrderId) {
                const formData = new FormData();
                formData.append('purchase_order_id', purchaseOrderId);
                formData.append('import_status', 1);

                fetch('../../BackEnd/Model/quanliphieunhap/xuliphieunhap.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        fetch('quanliphieunhap/fetch_phieunhap.php')
                            .then(response => response.json())
                            .then(data => {
                                purchaseOrders = data.data;
                                renderTable(purchaseOrders);
                                const approveModalEl = document.getElementById('approve-modal');
                                if (approveModalEl) {
                                    approveModalEl.close();
                                }
                                const successMessage = document.getElementById('success-message');
                                successMessage.querySelector('.success-text p').textContent = result.message || 'Phiếu nhập đã được duyệt';
                                successMessage.style.display = 'block';
                                setTimeout(() => {
                                    successMessage.style.display = 'none';
                                }, 3000);
                            })
                            .catch(error => console.error('Có lỗi khi lấy dữ liệu:', error));
                    } else {
                        const successMessage = document.getElementById('success-message');
                        successMessage.querySelector('.success-text p').textContent = result.message || 'Duyệt đơn thất bại';
                        successMessage.style.display = 'block';
                        successMessage.style.backgroundColor = 'var(--clr-error)';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                            successMessage.style.backgroundColor = '';
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi gửi yêu cầu duyệt:', error);
                    const successMessage = document.getElementById('success-message');
                    successMessage.querySelector('.success-text p').textContent = 'Lỗi khi gửi yêu cầu duyệt';
                    successMessage.style.display = 'block';
                    successMessage.style.backgroundColor = 'var(--clr-error)';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                        successMessage.style.backgroundColor = '';
                    }, 3000);
                });
            }

            // Sử dụng event delegation để xử lý các hành động
            document.getElementById('table-body').addEventListener('click', (e) => {
                const target = e.target.closest('a');
                if (!target) return;

                e.preventDefault();
                const purchaseOrderId = target.getAttribute('data-purchase-order-id');

                if (target.classList.contains('viewPurchaseOrder')) {
                    const viewModalEl = document.getElementById("view-modal");
                    if (viewModalEl) {
                        viewModalEl.showModal();
                        loadPurchaseOrderItems(purchaseOrderId);
                    }
                } else if (target.classList.contains('approvePurchaseOrder')) {
                    const approveModalEl = document.getElementById("approve-modal");
                    if (approveModalEl) {
                        approveModalEl.setAttribute("data-purchase-order-id", purchaseOrderId);
                        approveModalEl.showModal();
                    }
                }
            });

            // Event listener cho nút duyệt trong approve-modal
            const approveModalEl = document.getElementById('approve-modal');
            if (approveModalEl) {
                const approveButton = approveModalEl.querySelector('#approve-button');
                if (approveButton) {
                    approveButton.addEventListener('click', () => {
                        const purchaseOrderId = parseInt(approveModalEl.getAttribute('data-purchase-order-id'));
                        approvePurchaseOrder(purchaseOrderId);
                    });
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
                        }
                    }
                });
            }

            function addModalCancelButtonEventListener(modalEl) {
                if (!modalEl) return;
                const cancelButton = modalEl.querySelector('[id$="-close-button"]');
                if (cancelButton) {
                    cancelButton.addEventListener("click", () => {
                        modalEl.close();
                    });
                }
            }

            // Gọi hàm để thêm sự kiện
            addModalCloseButtonEventListeners();
            const viewModal = document.getElementById('view-modal');
            if (viewModal) {
                addModalCancelButtonEventListener(viewModal);
            }
            const approveModal = document.getElementById('approve-modal');
            if (approveModal) {
                addModalCancelButtonEventListener(approveModal);
            }
        });
        </script>
    </div>
</body>