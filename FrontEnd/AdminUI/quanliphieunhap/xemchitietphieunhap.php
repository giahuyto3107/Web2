<dialog data-modal id="view-modal">
    <div class="popup" id="popup">
        <div class="overlay"></div>
        <div class="popup-content">
        <div class="modal-header">
        <h2>Chi tiết Phiếu nhập</h2>
        <button class="modal-close" data-id="view-modal">
            <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
        </button>
    </div>
            
            <table>
                <tr>
                    
                    <td style="font-weight: bold; padding-top: 15px;" >Nhân viên</td>
                    <td id="modal-view-user-name"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding-top: 15px;">Nhà Xuất Bản</td>
                    <td id="modal-view-supplier-name"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding-top: 15px;">Ngày</td>
                    <td id="modal-view-order-date"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding-top: 15px;">Danh Sách Sản Phẩm</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <!-- Thêm div bao quanh bảng để tạo thanh cuộn -->
                        <div style="max-height: 300px; max-width: 100%; overflow-x: auto; overflow-y: auto;">
                            <table class="product-table" style="min-width: 1000px;">
                                <thead>
                                    <tr>
                                        <th>ID Sản phẩm</th>
                                        <th>Tên Sản phẩm</th>
                                        <th>Lợi nhuận (%)</th>
                                        <th>Số lượng</th>
                                        <th>Giá (VND)</th>
                                    </tr>
                                </thead>
                                <tbody id="purchase-order-items-body"></tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="display: flex; justify-content: flex-end;">
                            <p style="font-weight: bold; font-size: 20px; margin-right: 10px;">Tổng tiền:</p>
                            <span id="modal-view-total-value" style="color: #65e4dd; font-size: 20px;"></span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</dialog>