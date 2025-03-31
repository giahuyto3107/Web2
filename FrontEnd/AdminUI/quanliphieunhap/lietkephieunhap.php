<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phiếu nhập</title>
    <link rel="stylesheet" href="path/to/your/css.css"> <!-- Thay bằng đường dẫn CSS thực tế -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script> <!-- Thay bằng mã FontAwesome của bạn -->
</head>
<body>
<div class="form">
    <div class="form-title">
        <h2>Quản lý phiếu nhập</h2>
    </div>

    <div class="form-content">
        <table id="purchase-order-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Nhà Xuất Bản</th>
                    <th>Nhân Viên</th>
                    <th>Ngày Lập</th>
                    <th>Số lượng</th>
                    <th>Tổng tiền</th>
                    <th>Quản lý</th>
                </tr>
            </thead>
            <tbody id="table-body"></tbody>
        </table>
    </div>
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

    // Hàm render bảng
    function renderTable(data) {
        const tableBody = document.getElementById('table-body');
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7">Không có phiếu nhập nào.</td></tr>';
            return;
        }

        data.forEach(order => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${order.purchase_order_id}</td>
                <td>${order.supplier_name || 'N/A'}</td>
                <td>${order.user_name || 'N/A'}</td>
                <td>${order.order_date || 'N/A'}</td>
                <td>${order.amount || '0'}</td>
                <td>${order.total_price || '0'}</td>
                <td>
                    ${order.import_status == 1 ? 
                        '<p class="active">Đã duyệt</p>' : 
                        (order.import_status == 0 ? 
                            `<a href="#" class="inactive approvePurchaseOrder" data-purchase-order-id="${order.purchase_order_id}">Duyệt đơn</a>` : 
                            '')
                    }
                    <a href="#" class="detail-button viewPurchaseOrder" data-purchase-order-id="${order.purchase_order_id}">Chi tiết</a>
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
                console.log('Fetched purchase orders:', purchaseOrders); // Debug dữ liệu
                renderTable(purchaseOrders);
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

                // Hiển thị danh sách sản phẩm dưới dạng bảng ngang
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
                console.error('Lỗi khi tải chi tiết phiếu nhập:', error);
                document.getElementById('purchase-order-items-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải chi tiết phiếu nhập.</td></tr>';
            });
    }

    // Hàm duyệt đơn (chuyển import_status từ 0 thành 1)
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
                        alert(result.message || 'Phiếu nhập đã được duyệt');
                    })
                    .catch(error => console.error('Có lỗi khi lấy dữ liệu phiếu nhập:', error));
            } else {
                alert(result.message || 'Duyệt đơn thất bại');
            }
        })
        .catch(error => {
            console.error('Lỗi khi gửi yêu cầu duyệt:', error);
            alert('Lỗi khi gửi yêu cầu duyệt');
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

    // Đóng modal
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
</body>
</html>