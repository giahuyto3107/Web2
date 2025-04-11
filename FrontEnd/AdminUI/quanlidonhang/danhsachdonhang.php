<body>
<div class="header"></div>
<div class="data-table">
    <div class="success-message" id="success-message" style="display: none">
        <div class="success-text">
            <p>Đơn hàng đã được cập nhật</p>
            <a id="success-message-cross" style="cursor: pointer">
                <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
            </a>
        </div>
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </div>
    <h1 class="heading"> Quản lý <span>ĐƠN HÀNG</span></h1>
    <div class="toolbar">
        <div class="filters">
            <div class="filter-options-wrapper">
                <label for="filter-options" class="filter-label">Bộ lọc </label>
                <select id="filter-options">
                    <option value="order_id">Mã đơn</option>
                    <option value="user_name">Khách hàng</option>
                    <option value="order_date">Ngày đặt</option>
                    <option value="total_amount">Tổng tiền</option>
                    <option value="status_id">Trạng thái</option>
                    <option value="payment_method">Phương thức thanh toán</option>
                    <option value="phone">Số điện thoại</option>
                    <option value="address">Địa chỉ</option>
                </select>
            </div>

    <div class="search">
    <!-- Trường nhập liệu văn bản (mặc định) -->
    <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." style="display: block;" />
    <!-- Dropdown cho trạng thái (ẩn ban đầu) -->
    <select id="search-status" name="search-status" style="display: none;">
        <option value="">Tất cả</option>
        <option value="3">Chờ duyệt</option>
        <option value="4">Đã duyệt</option>
        <option value="5">Đã giao</option>
        <option value="7">Đã hủy</option>
    </select>
    <!-- Dropdown cho phương thức thanh toán (ẩn ban đầu) -->
    <select id="search-payment-method" name="search-payment-method" style="display: none;">
        <option value="">Tất cả</option>
        <option value="Tiền mặt">Tiền mặt</option>
        <option value="Chuyển khoản">Chuyển khoản</option>
    </select>
   <div id="search-date" style="display: none; margin-top: 0.5rem;">
    <div class="date-filter-wrapper">
        <label for="date-from" class="filter-label">Từ: </label>
        <input type="date" id="date-from" name="date-from" />
    </div>
    <div class="date-filter-wrapper">
        <label for="date-to" class="filter-label">Đến: </label>
        <input type="date" id="date-to" name="date-to" />
    </div>
</div>
</div>
        </div>
        <div class="toolbar-button-wrapper">
            <!-- Bỏ nút Thêm đơn hàng -->
        </div>
    </div>

    <div id="selected-products"></div>

    <div class="table-container">
        <div class="no-products">
            <p>Có vẻ hiện tại bạn chưa có đơn hàng nào?</p>
        </div>

        <table class="table" id="data-table">
            <thead>
                <tr>
                    <th data-id="order_id">Mã đơn</th>
                    <th data-id="user_name">Khách hàng</th>
                    <th data-id="order_date">Ngày đặt</th>
                    <th data-id="total_amount">Tổng tiền</th>
                    <th data-id="status_id">Trạng thái</th>
                    <th data-id="payment_method">Phương thức thanh toán</th>
                    <th data-id="phone">Số điện thoại</th>
                    <th data-id="address">Địa chỉ</th>
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
        let orders = []; // Dữ liệu gốc, không thay đổi
        let filteredOrders = []; // Dữ liệu đã lọc

        // Hàm chuyển status_id thành văn bản
        function getStatusText(statusId) {
            switch (statusId) {
                case "3": return 'Chờ duyệt';
                case "4": return 'Đã duyệt';
                case "5": return 'Đã giao';
                case "7": return 'Đã hủy';
                default: return 'N/A';
            }
        }

        // Hàm render bảng
        function renderTable(displayedOrders) {
            const tableBody = document.getElementById('table-body');
            const noProductsEl = document.querySelector('.no-products');

            if (!tableBody || !noProductsEl) {
                console.error('Required elements not found: #table-body or .no-products');
                return;
            }

            tableBody.innerHTML = '';
            const activeOrders = displayedOrders;

            if (activeOrders.length > 0) {
                noProductsEl.style.display = 'none';
                activeOrders.forEach((order, index) => {
                    const row = document.createElement('tr');
                    let actionButtons = '';
                    if (order.status_id === "3") {
                        actionButtons = `
                            <a href="#" class="updateStatus" data-order-id="${order.order_id}" data-status="4">Duyệt đơn <i class="fa fa-check"></i></a>
                            <a href="#" class="cancelOrder" data-order-id="${order.order_id}" data-status="7">Hủy đơn <i class="fa fa-times"></i></a>
                        `;
                    } else if (order.status_id === "4") {
                        actionButtons = `
                            <a href="#" class="updateStatus" data-order-id="${order.order_id}" data-status="5">Hoàn tất <i class="fa fa-check-circle"></i></a>
                            <a href="#" class="cancelOrder" data-order-id="${order.order_id}" data-status="7">Hủy đơn <i class="fa fa-times"></i></a>
                        `;
                    }

                    row.innerHTML = `
                        <td>${order.order_id}</td>
                        <td>${order.user_name || 'N/A'}</td>
                        <td>${order.order_date || 'N/A'}</td>
                        <td>${order.total_amount || '0'}</td>
                        <td>${getStatusText(order.status_id)}</td>
                        <td>${order.payment_method || 'N/A'}</td>
                        <td>${order.phone || 'N/A'}</td>
                        <td>${order.address || 'N/A'}</td>
                        <td class="actions">
                            <div class="dropdown">
                                <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                                <div class="dropdown-content">
                                    <a href="#" class="viewOrder" data-order-id="${order.order_id}">Xem <i class="fa fa-eye"></i></a>
                                    ${actionButtons}
                                </div>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                noProductsEl.style.display = 'flex';
                tableBody.innerHTML = '<tr><td colspan="9">Không tìm thấy đơn hàng nào.</td></tr>';
            }
        }

        // Hàm hiển thị/ẩn các thành phần trong .search
        function toggleSearchFields() {
            const filterBy = document.getElementById('filter-options').value;
            const searchText = document.getElementById('search-text');
            const searchStatus = document.getElementById('search-status');
            const searchPaymentMethod = document.getElementById('search-payment-method');
            const searchDate = document.getElementById('search-date');

            // Reset giá trị các trường khi thay đổi bộ lọc
            searchText.value = '';
            searchStatus.value = '';
            searchPaymentMethod.value = '';
            document.getElementById('date-from').value = '';
            document.getElementById('date-to').value = '';

            if (filterBy === 'status_id') {
                searchText.style.display = 'none';
                searchStatus.style.display = 'block';
                searchPaymentMethod.style.display = 'none';
                searchDate.style.display = 'none';
            } else if (filterBy === 'payment_method') {
                searchText.style.display = 'none';
                searchStatus.style.display = 'none';
                searchPaymentMethod.style.display = 'block';
                searchDate.style.display = 'none';
            } else if (filterBy === 'order_date') {
                searchText.style.display = 'none';
                searchStatus.style.display = 'none';
                searchPaymentMethod.style.display = 'none';
                searchDate.style.display = 'flex';
            } else {
                searchText.style.display = 'block';
                searchStatus.style.display = 'none';
                searchPaymentMethod.style.display = 'none';
                searchDate.style.display = 'none';
            }
        }

        // Hàm lọc dữ liệu trên client-side
        function filterOrders() {
            const filterBy = document.getElementById('filter-options').value;
            const searchText = document.getElementById('search-text').value.trim();
            const searchStatus = document.getElementById('search-status').value;
            const searchPaymentMethod = document.getElementById('search-payment-method').value;
            const dateFrom = document.getElementById('date-from').value;
            const dateTo = document.getElementById('date-to').value;

            let filteredData = [...orders]; // Sao chép dữ liệu gốc để lọc

            if (filterBy === 'status_id') {
                if (searchStatus) {
                    filteredData = orders.filter(order => order.status_id === searchStatus);
                }
            } else if (filterBy === 'payment_method') {
                if (searchPaymentMethod) {
                    filteredData = orders.filter(order => order.payment_method === searchPaymentMethod);
                }
            } else if (filterBy === 'order_date') {
                if (dateFrom || dateTo) {
                    filteredData = orders.filter(order => {
                        const orderDate = new Date(order.order_date);
                        const fromDate = dateFrom ? new Date(dateFrom) : null;
                        const toDate = dateTo ? new Date(dateTo) : null;

                        if (fromDate && toDate) {
                            return orderDate >= fromDate && orderDate <= toDate;
                        } else if (fromDate) {
                            return orderDate >= fromDate;
                        } else if (toDate) {
                            return orderDate <= toDate;
                        }
                        return true;
                    });
                }
            } else {
                if (searchText) {
                    filteredData = orders.filter(order => {
                        const value = order[filterBy];
                        if (typeof value === 'string') {
                            return value.toLowerCase().includes(searchText.toLowerCase());
                        } else {
                            return value.toString().includes(searchText);
                        }
                    });
                }
            }

            renderTable(filteredData);
        }

        // Thêm sự kiện cho các thành phần lọc
        function addFilterEventListeners() {
            const filterOptionsEl = document.getElementById('filter-options');
            const searchText = document.getElementById('search-text');
            const searchStatus = document.getElementById('search-status');
            const searchPaymentMethod = document.getElementById('search-payment-method');
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');

            filterOptionsEl.addEventListener('change', () => {
                toggleSearchFields();
                filterOrders();
            });
            searchText.addEventListener('input', filterOrders);
            searchStatus.addEventListener('change', filterOrders);
            searchPaymentMethod.addEventListener('change', filterOrders);
            dateFrom.addEventListener('change', filterOrders);
            dateTo.addEventListener('change', filterOrders);
        }

        // Fetch dữ liệu ban đầu từ server (chỉ gọi một lần)
        fetch('quanlidonhang/fetch_donhang.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    orders = data.data;
                    console.log('Initial orders:', orders);
                    renderTable(orders);
                    addFilterEventListeners();
                } else {
                    console.error('Error:', data.message);
                    document.getElementById('table-body').innerHTML = '<tr><td colspan="9">Lỗi khi tải danh sách đơn hàng.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('table-body').innerHTML = '<tr><td colspan="9">Lỗi khi tải danh sách đơn hàng.</td></tr>';
            });

        toggleSearchFields(); // Hiển thị trường tìm kiếm ban đầu

        // Sử dụng event delegation để xử lý các hành động
        document.getElementById('table-body').addEventListener('click', (e) => {
            const target = e.target.closest('a');
            if (!target) return;

            e.preventDefault();
            const orderId = target.getAttribute('data-order-id');
            const order = orders.find(o => o.order_id === orderId);

            if (!order) {
                console.error('Order not found:', orderId);
                return;
            }

            if (target.classList.contains('viewOrder')) {
                const viewModalEl = document.getElementById("view-modal");
                loadOrderItems(orderId);
                viewModalEl.showModal();
            } else if (target.classList.contains('updateStatus') || target.classList.contains('cancelOrder')) {
                const newStatus = target.getAttribute('data-status');
                const updateModalEl = document.getElementById("update-modal");
                updateModalEl.setAttribute("data-order-id", orderId);
                updateModalEl.setAttribute("data-status", newStatus);

                if (newStatus === "4") {
                    checkProductStock(orderId, updateModalEl);
                } else {
                    updateModalEl.querySelector('#update-message').textContent = 
                        newStatus === "5" ? "Bạn có chắc chắn muốn hoàn tất đơn hàng này?" :
                        "Bạn có chắc chắn muốn hủy đơn hàng này?";
                    updateModalEl.showModal();
                }
            }
        });

        // Hàm kiểm tra số lượng sản phẩm trước khi duyệt đơn
        function checkProductStock(orderId, updateModalEl) {
            fetch(`quanlidonhang/check_product_stock.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateModalEl.querySelector('#update-message').textContent = "Bạn có chắc chắn muốn duyệt đơn hàng này?";
                        updateModalEl.showModal();
                    } else {
                        const successMessage = document.getElementById('success-message');
                        successMessage.querySelector('.success-text p').textContent = data.message || 'Không đủ số lượng sản phẩm để duyệt đơn hàng';
                        successMessage.style.display = 'block';
                        successMessage.style.backgroundColor = 'var(--clr-error)';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                            successMessage.style.backgroundColor = 'var(--clr-success)';
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi kiểm tra số lượng sản phẩm:', error);
                    const successMessage = document.getElementById('success-message');
                    successMessage.querySelector('.success-text p').textContent = 'Lỗi khi kiểm tra số lượng sản phẩm';
                    successMessage.style.display = 'block';
                    successMessage.style.backgroundColor = 'var(--clr-error)';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                        successMessage.style.backgroundColor = 'var(--clr-success)';
                    }, 3000);
                });
        }

        // Hàm lấy và hiển thị chi tiết đơn hàng
        function loadOrderItems(orderId) {
            fetch(`quanlidonhang/fetch_donhang_items.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'success') {
                        console.error('Lỗi khi tải chi tiết đơn hàng:', data.message);
                        return;
                    }

                    const orderInfo = data.data.order_info;
                    const items = data.data.items;
                    const totalValue = data.data.total_value;

                    document.getElementById('modal-view-order-id').textContent = orderInfo.order_id || 'N/A';
                    document.getElementById('modal-view-user-name').textContent = orderInfo.user_name || 'N/A';
                    document.getElementById('modal-view-order-date').textContent = orderInfo.order_date || 'N/A';
                    document.getElementById('modal-view-total-amount').textContent = totalValue.toLocaleString() + ' VND';
                    document.getElementById('modal-view-status').textContent = getStatusText(orderInfo.status_id);
                    document.getElementById('modal-view-payment-method').textContent = orderInfo.payment_method || 'N/A';
                    document.getElementById('modal-view-phone').textContent = orderInfo.phone || 'N/A';
                    document.getElementById('modal-view-address').textContent = orderInfo.address || 'N/A';

                    const tableBody = document.getElementById('order-items-body');
                    tableBody.innerHTML = '';

                    if (items.length > 0) {
                        items.forEach(item => {
                            const row = `
                                <tr>
                                    <td>${item.product_id || 'N/A'}</td>
                                    <td>${item.product_name || 'N/A'}</td>
                                    <td>${item.quantity || '0'}</td>
                                    <td>${item.price || '0'}</td>
                                    <td>${(item.quantity * item.price).toLocaleString() || '0'} VND</td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="5">Không có sản phẩm trong đơn hàng.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải chi tiết đơn hàng:', error);
                    document.getElementById('order-items-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải chi tiết đơn hàng.</td></tr>';
                });
        }

        // Hàm cập nhật trạng thái đơn hàng
        function updateOrderStatus(orderId, newStatus) {
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('status_id', newStatus);
            formData.append('action', 'update_status');

            fetch('../../BackEnd/Model/quanlidonhang/xulidonhang.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    // Tải lại dữ liệu từ server để cập nhật danh sách đơn hàng
                    fetch('quanlidonhang/fetch_donhang.php')
                        .then(response => response.json())
                        .then(data => {
                            orders = data.data;
                            filterOrders(); // Áp dụng lại bộ lọc hiện tại
                            const updateModalEl = document.getElementById('update-modal');
                            updateModalEl.close();
                            const successMessage = document.getElementById('success-message');
                            successMessage.querySelector('.success-text p').textContent = result.message || 'Đơn hàng đã được cập nhật';
                            successMessage.style.display = 'block';
                            setTimeout(() => {
                                successMessage.style.display = 'none';
                            }, 3000);
                        })
                        .catch(error => console.error('Có lỗi khi lấy dữ liệu đơn hàng:', error));
                } else {
                    const successMessage = document.getElementById('success-message');
                    successMessage.querySelector('.success-text p').textContent = result.message || 'Cập nhật thất bại';
                    successMessage.style.display = 'block';
                    successMessage.style.backgroundColor = 'var(--clr-error)';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                        successMessage.style.backgroundColor = 'var(--clr-success)';
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Lỗi khi gửi yêu cầu cập nhật:', error);
                const successMessage = document.getElementById('success-message');
                successMessage.querySelector('.success-text p').textContent = 'Lỗi khi gửi yêu cầu cập nhật';
                successMessage.style.display = 'block';
                successMessage.style.backgroundColor = 'var(--clr-error)';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                    successMessage.style.backgroundColor = 'var(--clr-success)';
                }, 3000);
            });
        }

        // Event listener cho nút cập nhật trạng thái trong update-modal
        const updateModalEl = document.getElementById('update-modal');
        const updateButton = updateModalEl.querySelector('#update-button');
        updateButton.addEventListener('click', () => {
            const orderId = parseInt(updateModalEl.getAttribute('data-order-id'));
            const newStatus = updateModalEl.getAttribute('data-status');
            updateOrderStatus(orderId, newStatus);
        });

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
            const cancelButton = modalEl.querySelector('[id$="-close-button"]');
            if (!cancelButton) {
                console.error('Cancel button with id ending in "-close-button" not found in modal!');
                return;
            }

            cancelButton.addEventListener("click", () => {
                modalEl.close();
            });
        }

        addModalCloseButtonEventListeners();
        const viewModal = document.getElementById('view-modal');
        if (viewModal) {
            addModalCancelButtonEventListener(viewModal);
        }
        const updateModal = document.getElementById('update-modal');
        if (updateModal) {
            addModalCancelButtonEventListener(updateModal);
        }
    });
    </script>
</div>

<?php
    include 'quanlidonhang/xemchitietdonhang.php'; // View Modal
    include 'quanlidonhang/xoadonhang.php'; // Update Status Modal
?>

</body>
</html>