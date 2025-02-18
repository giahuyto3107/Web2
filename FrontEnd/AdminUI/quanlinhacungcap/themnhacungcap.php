<div class="center">
    <button id="open-popup">Thêm nhà cung cấp</button>
</div>

<div class="popup" id="popup">
    <div class="overlay"></div>
    <div class="popup-content">
        <h2>Thêm nhà cung cấp</h2>
        <div class="form">
            <div class="form-content">
                <form method="POST" action="../../BackEnd/Model/quanlinhacungcap/xulinhacungcap.php">
                    <div class="input1">
                        <p>Tên nhà cung cấp</p>
                        <input type="text" name="tennhacungcap" required>
                    </div>
                    <div class="input1">
                        <p>Số điện thoại nhà cung cấp</p>
                        <input type="text" name="sdt" required>
                    </div>
                    <div class="input1">
                        <p>Địa chỉ nhà cung cấp</p>
                        <input type="text" name="diachi" required>
                    </div>

                    <div class="form-group">
                        <label for="trangthai">Trạng thái</label>
                        <select id="trangthai" name="trangthai" required>
                            <option value="1">Hoạt động</option>
                            <option value="2">Không hoạt động</option>
                        </select>
                    </div>

                    <div class="input2">
                        <input type="submit" name="themncc" value="Thêm Loại sản phẩm">
                    </div>
                </form>
            </div>
        </div>
        <button class="close-btn">X</button>
    </div>
</div>
