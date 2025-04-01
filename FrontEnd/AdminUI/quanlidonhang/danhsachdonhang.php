<body>
<div class="header"></div>
<div class="data-table">
    <div class="success-message" id="success-message" style="display: none">
        <div class="success-text">
            <p>Đơn hàng đã được xóa</p>
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
                <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
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

        // Hàm chuyển status_id thành văn bản
        function getStatusText(statusId) {
            switch (statusId) {
                case "3": return 'Pending';
                case "4": return 'Delivered';
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

                let filteredData = orders;

                if (searchValue !== "") {
                    filteredData = orders.filter((order) => {
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
                renderTable(orders);
            });
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
                                    <a href="#" class="deleteOrder" data-order-id="${order.order_id}">Xóa <i class="fa fa-trash"></i></a>
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

        // Fetch dữ liệu ban đầu từ server
        fetch('quanlidonhang/fetch_donhang.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    orders = data.data;
                    console.log('Initial orders:', orders);
                    renderTable(orders);
                    addFilterEventListener();
                } else {
                    console.error('Error:', data.message);
                    document.getElementById('table-body').innerHTML = '<tr><td colspan="9">Lỗi khi tải danh sách đơn hàng.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('table-body').innerHTML = '<tr><td colspan="9">Lỗi khi tải danh sách đơn hàng.</td></tr>';
            });

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
            } else if (target.classList.contains('deleteOrder')) {
                const deleteModalEl = document.getElementById("delete-modal");
                deleteModalEl.setAttribute("data-order-id", orderId);
                deleteModalEl.showModal();
            }
        });
                    }

                    const orderInfo = data.data.order_info;
                    const items = data.data.items;
                    const totalValue = data.data.total_value;

                    // Điền thông tin chung
                    document.getElementById('modal-view-order-id').textContent = orderInfo.order_id || 'N/A';
                    document.getElementById('modal-view-user-name').textContent = orderInfo.user_name || 'N/A';
                    document.getElementById('modal-view-order-date').textContent = orderInfo.order_date || 'N/A';
                    document.getElementById('modal-view-total-amount').textContent = totalValue.toLocaleString() + ' VND';
                    document.getElementById('modal-view-status').textContent = getStatusText(orderInfo.status_id);
                    document.getElementById('modal-view-payment-method').textContent = orderInfo.payment_method || 'N/A';
                    document.getElementById('modal-view-phone').textContent = orderInfo.phone || 'N/A';
                    document.getElementById('modal-view-address').textContent = orderInfo.address || 'N/A';

                    // Hiển thị danh sách sản phẩm
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

        // Hàm xóa đơn hàng
        function deleteOrder(orderId) {
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('action', 'delete');

            fetch('../../BackEnd/Model/quanlidonhang/xulidonhang.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    fetch('quanlidonhang/fetch_donhang.php')
                        .then(response => response.json())
                        .then(data => {
                            orders = data.data;
                            renderTable(orders);
                            const deleteModalEl = document.getElementById('delete-modal');
                            deleteModalEl.close();
                            const successMessage = document.getElementById('success-message');
                            successMessage.querySelector('.success-text p').textContent = result.message || 'Đơn hàng đã được xóa';
                            successMessage.style.display = 'block';
                            setTimeout(() => {
                                successMessage.style.display = 'none';
                            }, 3000);
                        })
                        .catch(error => console.error('Có lỗi khi lấy dữ liệu đơn hàng:', error));
                } else {
                    const successMessage = document.getElementById('success-message');
                    successMessage.querySelector('.success-text p').textContent = result.message || 'Xóa thất bại';
                    successMessage.style.display = 'block';
                    successMessage.style.backgroundColor = 'var(--clr-error)';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                        successMessage.style.backgroundColor = 'var(--clr-success)';
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
                    successMessage.style.backgroundColor = 'var(--clr-success)';
                }, 3000);
            });
        }

        // Event listener cho nút xóa trong delete-modal
        const deleteModalEl = document.getElementById('delete-modal');
        const deleteDeleteButton = deleteModalEl.querySelector('#delete-delete-button');
        deleteDeleteButton.addEventListener('click', () => {
            const orderId = parseInt(deleteModalEl.getAttribute('data-order-id'));
            deleteOrder(orderId);
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

        // Gọi hàm để thêm sự kiện
        addModalCloseButtonEventListeners();
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
    include 'quanlidonhang/xemchitietdonhang.php'; // View Modal
    include 'quanlidonhang/xoadonhang.php'; // Delete Modal
?>

</body>
</html>