<body>
<div class="header"></div>
<div class="data-table">
    <h1 class="heading">Thống kê <span>Top 5 khách hàng mua nhiều nhất</span></h1>
    <div class="toolbar">
        <div class="filters">
            <div class="filter-date-wrapper">
                <label for="date-from" class="filter-label">Từ ngày</label>
                <input type="date" id="date-from" name="date-from" />
            </div>
            <div class="filter-date-wrapper">
                <label for="date-to" class="filter-label">Đến ngày</label>
                <input type="date" id="date-to" name="date-to" />
            </div>
            <button id="apply-filter">Áp dụng</button>
        </div>
        <div class="sort-buttons">
            <button id="sort-asc">Sắp xếp tăng dần</button>
            <button id="sort-desc">Sắp xếp giảm dần</button>
        </div>
    </div>

    <div class="table-container">
        <table class="table" id="top-customers-table">
            <thead>
                <tr>
                    <th>Tên khách hàng</th>
                    <th>Số đơn hàng</th>
                    <th>Tổng tiền mua (VND)</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="table-body"></tbody>
        </table>
    </div>

    <!-- Modal chi tiết đơn hàng -->
    <dialog data-modal id="order-details-modal">
        <div class="modal-header">
            <h2>Chi tiết đơn hàng của khách hàng</h2>
            <button class="modal-close" data-id="order-details-modal">
                <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
            </button>
        </div>
        <div class="modal-content">
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền (VND)</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody id="order-details-body"></tbody>
            </table>
        </div>
        <div class="modal-buttons">
            <button class="close" id="order-details-close-button">Đóng</button>
        </div>
    </dialog>

    <!-- Script để fetch và hiển thị dữ liệu -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateFromEl = document.getElementById('date-from');
        const dateToEl = document.getElementById('date-to');
        const applyFilterBtn = document.getElementById('apply-filter');
        const sortAscBtn = document.getElementById('sort-asc');
        const sortDescBtn = document.getElementById('sort-desc');
        const tableBody = document.getElementById('table-body');
        const orderDetailsBody = document.getElementById('order-details-body');
        const orderDetailsModal = document.getElementById('order-details-modal');
        let customersData = []; // Lưu dữ liệu khách hàng để sắp xếp

        // Hàm fetch dữ liệu top 5 khách hàng
        function fetchTopCustomers() {
            const dateFrom = dateFromEl.value;
            const dateTo = dateToEl.value;

            if (!dateFrom || !dateTo) {
                alert('Vui lòng chọn khoảng thời gian');
                return;
            }

            fetch(`thongke/fetch_top_customers.php?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        customersData = data.data;
                        renderTable(customersData);
                    } else {
                        console.error('Error:', data.message);
                        tableBody.innerHTML = '<tr><td colspan="4">Lỗi khi tải dữ liệu.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    tableBody.innerHTML = '<tr><td colspan="4">Lỗi khi tải dữ liệu.</td></tr>';
                });
        }

        // Hàm render bảng top 5 khách hàng
        function renderTable(customers) {
            tableBody.innerHTML = '';
            customers.forEach(customer => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${customer.user_name}</td>
                    <td>${customer.order_count}</td>
                    <td>${parseFloat(customer.total_spent).toLocaleString()}</td>
                    <td>
                        <button class="view-details" data-user-id="${customer.user_id}">Xem chi tiết</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Hàm fetch chi tiết đơn hàng của khách hàng
        function fetchOrderDetails(userId) {
            fetch(`thongke/fetch_customer_orders.php?user_id=${userId}&date_from=${dateFromEl.value}&date_to=${dateToEl.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderOrderDetails(data.data);
                        orderDetailsModal.showModal();
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => console.error('Fetch error:', error));
        }

        // Hàm render chi tiết đơn hàng
        function renderOrderDetails(orders) {
            orderDetailsBody.innerHTML = '';
            orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.order_id}</td>
                    <td>${order.order_date}</td>
                    <td>${parseFloat(order.total_amount).toLocaleString()}</td>
                    <td>${getStatusText(order.status_id)}</td>
                    <td><button class="view-order" data-order-id="${order.order_id}">Xem chi tiết</button></td>
                `;
                orderDetailsBody.appendChild(row);
            });
        }

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

        // Hàm sắp xếp dữ liệu
        function sortData(order) {
            const sortedData = [...customersData];
            sortedData.sort((a, b) => {
                return order === 'asc' 
                    ? parseFloat(a.total_spent) - parseFloat(b.total_spent) 
                    : parseFloat(b.total_spent) - parseFloat(a.total_spent);
            });
            renderTable(sortedData);
        }

        // Event listener cho các nút
        applyFilterBtn.addEventListener('click', fetchTopCustomers);
        sortAscBtn.addEventListener('click', () => sortData('asc'));
        sortDescBtn.addEventListener('click', () => sortData('desc'));

        tableBody.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-details')) {
                const userId = e.target.getAttribute('data-user-id');
                fetchOrderDetails(userId);
            }
        });

        orderDetailsBody.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-order')) {
                const orderId = e.target.getAttribute('data-order-id');
                loadOrderItems(orderId);
            }
        });

        document.getElementById('order-details-close-button').addEventListener('click', () => {
            orderDetailsModal.close();
        });

        // Hàm hiển thị chi tiết sản phẩm trong đơn hàng (tái sử dụng từ danhsachdonhang.php)
        function loadOrderItems(orderId) {
            fetch(`quanlidonhang/fetch_donhang_items.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
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
                        items.forEach(item => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${item.product_id || 'N/A'}</td>
                                    <td>${item.product_name || 'N/A'}</td>
                                    <td>${item.quantity || '0'}</td>
                                    <td>${item.price || '0'}</td>
                                    <td>${(item.quantity * item.price).toLocaleString()} VND</td>
                                </tr>
                            `;
                        });
                        document.getElementById('view-modal').showModal();
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
    </script>
</div>

<?php include 'quanlidonhang/xemchitietdonhang.php'; // Modal xem chi tiết đơn hàng ?>
</body>