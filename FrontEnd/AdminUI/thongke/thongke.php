
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

        // Đặt giá trị mặc định cho "Từ ngày" và "Đến ngày"
        const defaultFromDate = '2020-01-01'; // Ngày 1/1/2020
        const today = new Date(); // Ngày hiện tại (11/4/2025)
        const defaultToDate = today.toISOString().split('T')[0]; // Định dạng YYYY-MM-DD

        dateFromEl.value = defaultFromDate;
        dateToEl.value = defaultToDate;

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

        // Tự động gọi fetchTopCustomers() khi trang được tải
        fetchTopCustomers();
    });
    </script>
</div>

<?php include 'quanlidonhang/xemchitietdonhang.php'; // Modal xem chi tiết đơn hàng ?>
</body>
</html>

<style>
    /* thongke.css */

/* Định dạng .data-table */
.data-table {
    height: 85%;
    padding-inline: 2rem;
    position: relative;
}

/* Định dạng .heading */
.heading {
    margin-block: 1.5rem;
    font-size: 1.5rem;
    color: var(--clr-primary-300);
}

.heading span {
    font-weight: var(--fw-bold);
}

/* Định dạng .toolbar */
.toolbar {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

/* Định dạng .filters */
.filters {
    display: flex;
    align-items: center; /* Căn giữa các phần tử theo chiều dọc */
    gap: 1rem;
    flex-wrap: nowrap;
    min-width: 570px; /* Tăng min-width để chứa cả ba phần tử */
    flex: 1;
}

/* Định dạng .filter-date-wrapper */
.filter-date-wrapper {
    display: flex;
    flex-direction: row; /* Đổi thành row để label và input nằm ngang */
    align-items: center; /* Căn giữa label và input theo chiều dọc */
    gap: 0.5rem; /* Khoảng cách giữa label và input */
    flex: 1;
    min-width: 210px; /* Đủ chỗ cho label (60px) + input (150px) + gap (8px) */
    max-width: 210px;
}

/* Định dạng label */
.filter-date-wrapper .filter-label {
    font-weight: bold;
    flex: 0 0 70px; /* Đặt chiều rộng cố định cho label */
}

/* Định dạng input type="date" */
input[type="date"] {
    padding: 0.6rem;
    border: 1px solid #ccc;
    border-radius: 0.35rem;
    background-color: hsl(0 0% 100%);
    color: #333;
    flex: 1; /* Input chiếm phần không gian còn lại */
    min-width: 140px; /* Đảm bảo input không bị quá nhỏ */
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.6rem center;
    background-size: 1rem;
    cursor: pointer;
}

/* Hiệu ứng hover và focus cho input type="date" */
input[type="date"]:hover,
input[type="date"]:focus {
    border-color: #999;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

/* Định dạng nút "Áp dụng" */
#apply-filter {
    padding: 0.6rem 1rem;
    border-radius: 0.35rem;
    border: none;
    background-color: var(--clr-primary-300);
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
    flex: 0 0 auto;
    min-width: 100px;
}

#apply-filter:hover {
    background-color: #3a506b;
}

/* Định dạng .sort-buttons */
.sort-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: nowrap;
    flex: 0 0 auto;
    min-width: 320px;
}

/* Định dạng nút "Sắp xếp tăng dần" và "Sắp xếp giảm dần" */
#sort-asc,
#sort-desc {
    padding: 0.6rem 1rem;
    border-radius: 0.35rem;
    border: none;
    background-color: var(--clr-primary-300);
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
    flex: 0 0 auto;
    min-width: 150px;
}

#sort-asc:hover,
#sort-desc:hover {
    background-color: #3a506b;
}

/* Định dạng .table-container */
.table-container {
    position: relative;
    overflow: auto;
    height: 100%;
    max-height: 64vh;
    margin-block: 1.25rem;
    background-color: white;
    box-shadow: 0 0 0.875rem 0 rgba(33, 37, 41, 0.05);
}

/* Định dạng bảng */
#top-customers-table {
    width: 100%;
    border-collapse: collapse;
    position: relative;
}

thead {
    position: sticky;
    top: 0;
    z-index: 12;
}

th,
td {
    height: 3.5rem;
    border-bottom: 0.094rem solid #ddd;
    padding: 0 1rem;
    text-align: left;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    max-width: 12.5rem;
}

th {
    background-color: white;
    color: var(--fs-table-header);
    font-weight: var(--fw-bold);
}

tr:hover {
    background-color: #f1f1f1;
}

/* Định dạng nút "Xem chi tiết" */
.view-details,
.view-order {
    padding: 0.5rem 1rem;
    border-radius: 0.35rem;
    border: none;
    background-color: var(--clr-primary-300);
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

.view-details:hover,
.view-order:hover {
    background-color: #3a506b;
}

/* Định dạng modal */
dialog {
    margin: auto;
    padding: 1rem;
    border: none;
    max-width: 40rem;
    width: calc(100% - 1rem);
    color: inherit;
    scroll-behavior: smooth;
}

dialog::backdrop {
    background-color: rgb(0 0 0 / 50%);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-block: 1rem;
    border-bottom: solid 1px #999999;
}

.modal-header h2 {
    color: var(--clr-primary-300);
    font-weight: var(--fw-bold);
    font-size: 2rem;
}

.modal-close {
    cursor: pointer;
    background: none;
    border: none;
}

.modal-close:focus-visible {
    outline: none;
}

.modal-close:hover {
    opacity: 0.8;
}

/* Định dạng bảng trong modal */
.modal-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.modal-table th,
.modal-table td {
    padding: 0.75rem;
    border-bottom: 0.094rem solid #ddd;
    text-align: left;
}

/* Định dạng modal buttons */
.modal-buttons {
    text-align: center;
    padding-top: 0.5rem;
    padding-bottom: 2rem;
}

.modal-buttons button {
    margin-inline: 0.25rem;
    padding: 0.625rem 1.5rem;
    border-radius: 100vmax;
    border: none;
    cursor: pointer;
    color: #fff;
}

.modal-buttons button:hover {
    opacity: 0.8;
}

.modal-buttons .close {
    background-color: #dc3545;
}

/* Responsive */
@media (max-width: 67em) {
    .toolbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .filters {
        flex-wrap: wrap;
        min-width: 0;
        width: 100%;
    }
    .filter-date-wrapper {
        min-width: 210px;
        max-width: 250px;
    }
    .sort-buttons {
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 40em) {
    .filters {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .filter-date-wrapper {
        min-width: 100%;
        max-width: none;
    }
    #apply-filter {
        min-width: 100%;
    }
    .sort-buttons {
        flex-direction: column;
        gap: 0.5rem;
    }
    #sort-asc,
    #sort-desc {
        min-width: 100%;
    }
}

@media (max-width: 28em) {
    .data-table {
        padding-inline: 1.2rem;
    }
    .heading {
        font-size: 1.2rem;
    }
}
</style>