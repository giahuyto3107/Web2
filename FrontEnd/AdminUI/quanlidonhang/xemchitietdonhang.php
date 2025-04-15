
<dialog data-modal id="view-modal">
    <div class="modal-header">
        <h2>Chi tiết Đơn Hàng</h2>
        <button class="modal-close" data-id="view-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
    <div class="view-container">
        <div class="view-content">
            <span>Mã đơn</span>
            <p id="modal-view-order-id">N/A</p>
        </div>
        <div class="view-content">
            <span>Khách hàng</span>
            <p id="modal-view-user-name">N/A</p>
        </div>
        <div class="view-content">
            <span>Ngày đặt</span>
            <p id="modal-view-order-date">N/A</p>
        </div>
        <div class="view-content">
            <span>Tổng tiền</span>
            <p id="modal-view-total-amount">N/A</p>
        </div>
        <div class="view-content">
            <span>Trạng thái</span>
            <p id="modal-view-status">N/A</p>
        </div>
        <div class="view-content">
            <span>Phương thức thanh toán</span>
            <p id="modal-view-payment-method">N/A</p>
        </div>
        <div class="view-content">
            <span>Số điện thoại</span>
            <p id="modal-view-phone">N/A</p>
        </div>
        <div class="view-content">
            <span>Địa chỉ</span>
            <p id="modal-view-address">N/A</p>
        </div>
        <!-- Tách phần "Danh sách sản phẩm" ra khỏi .view-content -->
        <div class="order-items-section">
            <span class="section-title">Danh sách sản phẩm</span>
            <div class="table-scroll-wrapper">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody id="order-items-body"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-buttons">
            <button class="close" id="view-close-button">Đóng</button>
        </div>
    </div>
</dialog>
<style>
/* chitietdonhang.css */

/* Định dạng dialog */
dialog {
    margin: auto;
    padding: 1rem;
    padding-left: 2rem;
    padding-right: 2rem;
    border: none;
    max-width: 43rem;
    width: calc(100% - 1rem);
    color: inherit;
    scroll-behavior: smooth;
}

dialog::backdrop {
    background-color: rgb(0 0 0 / 50%);
}

/* Định dạng modal-header */
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

/* Định dạng nút đóng */
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

/* Định dạng view-container */
.view-container {
    display: grid;
    grid-template-columns: 1fr 1fr; /* Hai cột cho các .view-content */
    gap: 1.5rem;
    row-gap: 1.25rem;
}

/* Định dạng view-content */
.view-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.view-content span {
    font-weight: var(--fw-bold);
    color: var(--clr-neutral-900);
}

.view-content p {
    width: 100%;
    padding: 0.6rem;
    background-color: hsl(0 0% 100%);
    box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;
}

/* Định dạng order-items-section */
.order-items-section {
    grid-column: span 2; /* Chiếm cả hai cột */
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
}

/* Định dạng tiêu đề "Danh sách sản phẩm" */
.order-items-section .section-title {
    font-weight: var(--fw-bold);
    color: var(--clr-neutral-900);
    font-size: 1.1rem;
}

/* Định dạng div bao quanh bảng để hiển thị thanh cuộn */
.table-scroll-wrapper {
    max-height: 13rem; /* Chiều cao tối đa cho 5 sản phẩm */
    overflow-y: auto; /* Thanh cuộn dọc chỉ xuất hiện khi vượt quá chiều cao */
    overflow-x: hidden; /* Ẩn thanh cuộn ngang */
}

/* Định dạng bảng trong modal */
.modal-table {
    width: 100%;
    border-collapse: collapse;
}

.modal-table th,
.modal-table td {
    padding: 0.75rem;
    border-bottom: 0.094rem solid #ddd;
    text-align: left;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    max-width: 12.5rem;
}

.modal-table th {
    background-color: white;
    color: var(--fs-table-header);
    font-weight: var(--fw-bold);
}

.modal-table tr:hover {
    background-color: #f1f1f1;
}

/* Định dạng modal-buttons */
.modal-buttons {
    text-align: center;
    grid-column: span 2; /* Nút "Đóng" chiếm cả hai cột */
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
@media (max-width: 40em) {
    .view-container {
        grid-template-columns: 1fr; /* Chuyển thành một cột trên màn hình nhỏ */
        gap: 1rem;
    }

    .modal-table th,
    .modal-table td {
        padding: 0.5rem;
        max-width: none; /* Xóa giới hạn chiều rộng trên màn hình nhỏ */
    }
}

@media (max-width: 28em) {
    dialog {
        padding: 0.5rem;
        width: calc(100% - 0.5rem);
    }

    .modal-header h2 {
        font-size: 1.5rem;
    }

    .modal-buttons button {
        padding: 0.5rem 1rem;
    }

    .order-items-section .section-title {
        font-size: 1rem;
    }
}
</style>
