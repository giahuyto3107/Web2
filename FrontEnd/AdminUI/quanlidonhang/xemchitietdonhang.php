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
        <div class="view-content">
            <span>Danh sách sản phẩm</span>
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
        <div class="modal-buttons">
            <button class="close" id="view-close-button">Đóng</button>
        </div>
    </div>
</dialog>
