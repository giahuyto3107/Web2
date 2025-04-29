<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê Top 5 Khách hàng</title>
    <!-- Font Awesome để sử dụng icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
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

    <!-- Biểu đồ top 5 khách hàng -->
    <div class="chart-container">
        <canvas id="top-customers-chart"></canvas>
    </div>

    <!-- Bảng top 5 khách hàng -->
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

    <!-- Biểu đồ doanh thu theo tháng -->
    <h2 class="sub-heading">Doanh thu theo tháng</h2>
    <div class="chart-container">
        <canvas id="monthly-revenue-chart"></canvas>
    </div>

    <!-- Bảng top sản phẩm bán chạy -->
    <h2 class="sub-heading">Top sản phẩm bán chạy</h2>
    <div class="table-container">
        <table class="table" id="top-products-table">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng bán</th>
                    <th>Doanh thu (VND)</th>
                </tr>
            </thead>
            <tbody id="top-products-body"></tbody>
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

    <!-- Thêm Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Script để fetch và hiển thị dữ liệu -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateFromEl = document.getElementById('date-from');
        const dateToEl = document.getElementById('date-to');
        const applyFilterBtn = document.getElementById('apply-filter');
        const sortAscBtn = document.getElementById('sort-asc');
        const sortDescBtn = document.getElementById('sort-desc');
        const tableBody = document.getElementById('table-body');
        const topProductsBody = document.getElementById('top-products-body');
        const orderDetailsBody = document.getElementById('order-details-body');
        const orderDetailsModal = document.getElementById('order-details-modal');
        const orderDetailsCloseIcon = document.querySelector('#order-details-modal .modal-close');
        const viewModal = document.getElementById('view-modal');
        const viewCloseButton = document.getElementById('view-close-button');
        const viewCloseIcon = document.querySelector('#view-modal .modal-close');
        let customersData = []; // Lưu dữ liệu khách hàng để sắp xếp
        let chartInstance = null; // Lưu instance của biểu đồ top khách hàng
        let revenueChartInstance = null; // Lưu instance của biểu đồ doanh thu

        // Đặt giá trị mặc định cho "Từ ngày" và "Đến ngày"
        const defaultFromDate = '2020-01-01';
        const today = new Date();
        const defaultToDate = today.toISOString().split('T')[0];

        dateFromEl.value = defaultFromDate;
        dateToEl.value = defaultToDate;

        // Hàm fetch dữ liệu tổng hợp
        function fetchAllData() {
            const dateFrom = dateFromEl.value;
            const dateTo = dateToEl.value;

            if (!dateFrom || !dateTo) {
                alert('Vui lòng chọn khoảng thời gian');
                return;
            }

            // Fetch top 5 khách hàng
            fetchTopCustomers(dateFrom, dateTo);

            // Fetch doanh thu theo tháng
            fetchMonthlyRevenue(dateFrom, dateTo);

            // Fetch top sản phẩm bán chạy
            fetchTopProducts(dateFrom, dateTo);
        }

        // Hàm fetch dữ liệu top 5 khách hàng
        function fetchTopCustomers(dateFrom, dateTo) {
            fetch(`thongke/fetch_top_customers.php?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        customersData = data.data || [];
                        if (customersData.length === 0) {
                            tableBody.innerHTML = '<tr><td colspan="4">Không có dữ liệu để hiển thị.</td></tr>';
                            if (chartInstance) chartInstance.destroy();
                            return;
                        }
                        renderTable(customersData);
                        renderChart(customersData);
                    } else {
                        console.error('Error:', data.message);
                        tableBody.innerHTML = `<tr><td colspan="4">Lỗi: ${data.message}</td></tr>`;
                        if (chartInstance) chartInstance.destroy();
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    tableBody.innerHTML = '<tr><td colspan="4">Lỗi khi tải dữ liệu. Vui lòng thử lại.</td></tr>';
                    if (chartInstance) chartInstance.destroy();
                });
        }

        // Hàm fetch dữ liệu doanh thu theo tháng
        function fetchMonthlyRevenue(dateFrom, dateTo) {
            fetch(`thongke/fetch_monthly_revenue.php?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const revenueData = data.data || [];
                        if (revenueData.length === 0) {
                            document.getElementById('monthly-revenue-chart').style.display = 'none';
                            if (revenueChartInstance) revenueChartInstance.destroy();
                            return;
                        }
                        document.getElementById('monthly-revenue-chart').style.display = 'block';
                        renderRevenueChart(revenueData);
                    } else {
                        console.error('Error:', data.message);
                        alert(`Lỗi: ${data.message}`);
                        if (revenueChartInstance) revenueChartInstance.destroy();
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Lỗi khi tải dữ liệu doanh thu. Vui lòng thử lại.');
                    if (revenueChartInstance) revenueChartInstance.destroy();
                });
        }

        // Hàm fetch dữ liệu top sản phẩm bán chạy
        function fetchTopProducts(dateFrom, dateTo) {
            fetch(`thongke/fetch_top_products.php?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const productsData = data.data || [];
                        if (productsData.length === 0) {
                            topProductsBody.innerHTML = '<tr><td colspan="3">Không có dữ liệu để hiển thị.</td></tr>';
                            return;
                        }
                        renderTopProducts(productsData);
                    } else {
                        console.error('Error:', data.message);
                        topProductsBody.innerHTML = `<tr><td colspan="3">Lỗi: ${data.message}</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    topProductsBody.innerHTML = '<tr><td colspan="3">Lỗi khi tải dữ liệu. Vui lòng thử lại.</td></tr>';
                });
        }

        // Hàm render bảng top 5 khách hàng
        function renderTable(customers) {
            tableBody.innerHTML = '';
            customers.forEach(customer => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${customer.user_name || 'N/A'}</td>
                    <td>${customer.order_count || '0'}</td>
                    <td>${parseFloat(customer.total_spent || 0).toLocaleString()} VND</td>
                    <td>
                        <button class="view-details" data-user-id="${customer.user_id}">Xem chi tiết</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Hàm vẽ biểu đồ top 5 khách hàng
        function renderChart(customers) {
            const ctx = document.getElementById('top-customers-chart').getContext('2d');
            const labels = customers.map(customer => customer.user_name || 'N/A');
            const totalSpent = customers.map(customer => parseFloat(customer.total_spent || 0));
            const orderCount = customers.map(customer => parseInt(customer.order_count || 0));

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Tổng tiền mua (VND)',
                            data: totalSpent,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Số đơn hàng',
                            data: orderCount,
                            type: 'line',
                            fill: false,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            tension: 0.1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Tổng tiền mua (VND)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' VND';
                                }
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Số đơn hàng'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tên khách hàng'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label === 'Tổng tiền mua (VND)') {
                                        return label + ': ' + context.parsed.y.toLocaleString() + ' VND';
                                    }
                                    return label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Hàm vẽ biểu đồ doanh thu theo tháng
        function renderRevenueChart(revenueData) {
            const ctx = document.getElementById('monthly-revenue-chart').getContext('2d');
            const labels = revenueData.map(item => `${item.sale_month}/${item.sale_year}`);
            const revenues = revenueData.map(item => parseFloat(item.monthly_revenue || 0));

            if (revenueChartInstance) {
                revenueChartInstance.destroy();
            }

            revenueChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Doanh thu (VND)',
                            data: revenues,
                            fill: false,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Doanh thu (VND)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' VND';
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tháng/Năm'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    return label + ': ' + context.parsed.y.toLocaleString() + ' VND';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Hàm render bảng top sản phẩm bán chạy
        function renderTopProducts(products) {
            topProductsBody.innerHTML = '';
            products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.product_name || 'N/A'}</td>
                    <td>${product.total_sold || '0'}</td>
                    <td>${parseFloat(product.total_revenue || 0).toLocaleString()} VND</td>
                `;
                topProductsBody.appendChild(row);
            });
        }

        // Hàm fetch chi tiết đơn hàng của khách hàng
        function fetchOrderDetails(userId) {
            fetch(`thongke/fetch_customer_orders.php?user_id=${userId}&date_from=${dateFromEl.value}&date_to=${dateToEl.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (data.data && data.data.length > 0) {
                            renderOrderDetails(data.data);
                            orderDetailsModal.showModal();
                        } else {
                            alert('Không có đơn hàng nào trong khoảng thời gian này.');
                        }
                    } else {
                        console.error('Error:', data.message);
                        alert(`Lỗi: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Lỗi khi tải chi tiết đơn hàng. Vui lòng thử lại.');
                });
        }

        // Hàm render chi tiết đơn hàng
        function renderOrderDetails(orders) {
            orderDetailsBody.innerHTML = '';
            orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.order_id || 'N/A'}</td>
                    <td>${order.order_date || 'N/A'}</td>
                    <td>${parseFloat(order.total_amount || 0).toLocaleString()} VND</td>
                    <td>${getStatusText2(order.status_id)}</td>
                    <td><button class="view-order" data-order-id="${order.order_id}">Xem chi tiết</button></td>
                `;
                orderDetailsBody.appendChild(row);
            });
        }

        // Hàm chuyển status_id thành văn bản
        function getStatusText2(statusId) {
            switch (statusId) {
                case 3: return 'Chờ duyệt';
                case 4: return 'Đã duyệt';
                case 5: return 'Đã giao';
                case 7: return 'Đã hủy';
                default: return statusId; // Hiển thị dữ liệu gốc thay vì 'N/A'
            }
        }

        // Hàm chuyển status_id thành văn bản
        function getStatusText(statusId) {
            switch (statusId) {
                case "3": return 'Chờ duyệt';
                case "4": return 'Đã duyệt';
                case "5": return 'Đã giao';
                case "7": return 'Đã hủy';
                default: return statusId; // Hiển thị dữ liệu gốc thay vì 'N/A'
            }
        }

        // Hàm sắp xếp dữ liệu top khách hàng
        function sortData(order) {
            const sortedData = [...customersData];
            sortedData.sort((a, b) => {
                return order === 'asc' 
                    ? parseFloat(a.total_spent || 0) - parseFloat(b.total_spent || 0) 
                    : parseFloat(b.total_spent || 0) - parseFloat(a.total_spent || 0);
            });
            renderTable(sortedData);
            renderChart(sortedData);
        }

        // Hàm hiển thị chi tiết sản phẩm trong đơn hàng
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
                        if (items && items.length > 0) {
                            items.forEach(item => {
                                tableBody.innerHTML += `
                                    <tr>
                                        <td>${item.product_id || 'N/A'}</td>
                                        <td>${item.product_name || 'N/A'}</td>
                                        <td>${item.quantity || '0'}</td>
                                        <td>${parseFloat(item.price || 0).toLocaleString()} VND</td>
                                        <td>${(parseFloat(item.quantity || 0) * parseFloat(item.price || 0)).toLocaleString()} VND</td>
                                    </tr>
                                `;
                            });
                            document.getElementById('view-modal').showModal();
                        } else {
                            alert('Đơn hàng này không có sản phẩm.');
                        }
                    } else {
                        console.error('Error:', data.message);
                        alert(`Lỗi: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi khi tải chi tiết sản phẩm. Vui lòng thử lại.');
                });
        }

        // Event listener cho các nút
        applyFilterBtn.addEventListener('click', fetchAllData);
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

        // Đóng modal order-details-modal khi nhấp vào nút "Đóng"
        document.getElementById('order-details-close-button').addEventListener('click', () => {
            orderDetailsModal.close();
        });

        // Đóng modal order-details-modal khi nhấp vào nút "X"
        orderDetailsCloseIcon.addEventListener('click', () => {
            orderDetailsModal.close();
        });

        // Đóng modal view-modal khi nhấp vào nút "Đóng"
        viewCloseButton.addEventListener('click', () => {
            viewModal.close();
        });

        // Đóng modal view-modal khi nhấp vào nút "X"
        viewCloseIcon.addEventListener('click', () => {
            viewModal.close();
        });

        // Tự động gọi fetchAllData() khi trang được tải
        fetchAllData();
    });
    </script>
</div>

<?php include 'quanlidonhang/xemchitietdonhang.php'; // Modal xem chi tiết đơn hàng ?>
</body>

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

/* Định dạng .sub-heading */
.sub-heading {
    margin-block: 1.5rem;
    font-size: 1.25rem;
    color: var(--clr-primary-300);
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
    align-items: center;
    gap: 1rem;
    flex-wrap: nowrap;
    min-width: 570px;
    flex: 1;
}

/* Định dạng .filter-date-wrapper */
.filter-date-wrapper {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
    min-width: 210px;
    max-width: 210px;
}

/* Định dạng label */
.filter-date-wrapper .filter-label {
    font-weight: bold;
    flex: 0 0 70px;
}

/* Định dạng input type="date" */
input[type="date"] {
    padding: 0.6rem;
    border: 1px solid #ccc;
    border-radius: 0.35rem;
    background-color: hsl(0 0% 100%);
    color: #333;
    flex: 1;
    min-width: 140px;
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

/* Định dạng .chart-container */
.chart-container {
    position: relative;
    width: 100%;
    height: 300px;
    margin-block: 1.25rem;
    background-color: white;
    box-shadow: 0 0 0.875rem 0 rgba(33, 37, 41, 0.05);
    border-radius: 0.35rem;
    padding: 1rem;
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
.table {
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
    max-width: 44rem;
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
    .chart-container {
        height: 250px;
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
    .chart-container {
        height: 200px;
    }
}

@media (max-width: 28em) {
    .data-table {
        padding-inline: 1.2rem;
    }
    .heading, .sub-heading {
        font-size: 1.2rem;
    }
    .chart-container {
        height: 180px;
    }
}
</style>
</html>