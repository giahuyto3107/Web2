<div class="center">
    <button id="open-popup">Thêm loại sản phẩm</button>
</div>

<div class="popup" id="popup">
    <div class="overlay"></div>
    <div class="popup-content">
        <h2>Thêm Loại Sản Phẩm</h2>
        <div class="form">
            <div class="form-content">
                <form method="POST" action="../../BackEnd/Model/quanliloaisanpham/xuliloaisanpham.php">
                    <div class="input1">
                        <p>Tên Loại sản phẩm</p>
                        <input type="text" name="tenloaisp" required>
                    </div>
                    <div class="input1">
                        <p>Mô tả Loại sản phẩm</p>
                        <input type="text" name="motaloaisp" required>
                    </div>
                    <div class="input2">
                        <input type="submit" name="themloaisp" value="Thêm Loại sản phẩm">
                    </div>
                </form>
            </div>
        </div>
        <button class="close-btn">X</button>
    </div>
</div>
