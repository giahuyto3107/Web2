<?php
$sql_sua_ncc = "SELECT * FROM supplier WHERE supplier_id='$_GET[idncc]' LIMIT 1";
$query_sua_ncc = mysqli_query($conn, $sql_sua_ncc);
?>

<div class="popup" id="edit-popup">
    <div class="overlay"></div>
    <div class="popup-content">
        <h2>Sửa Nhà cung cấp</h2>
        <div class="form">
            <div class="form-content">
                <form method="POST" action="../../BackEnd/Model/quanlinhacungcap/xulinhacungcap.php">
                    <?php while ($dong = mysqli_fetch_array($query_sua_ncc)) { ?>
                        <!-- Thêm trường ẩn để truyền category_id -->
                        <input type="hidden" name="idncc" value="<?php echo $dong['supplier_id'] ?>">

                        <div class="input1">
                            <p>Tên Loại sản phẩm</p>
                            <input type="text" name="tennhacungcap" value="<?php echo $dong['supplier_name'] ?>" required>
                        </div>
                        <div class="input1">
                            <p>Số điện thoại liên lạc</p>
                            <input type="text" name="sdt" value="<?php echo $dong['contact_phone'] ?>" required>
                        </div>
                        <div class="input1">
                            <p>Địa chỉ</p>
                            <input type="text" name="diachi" value="<?php echo $dong['address'] ?>" required>
                        </div>
                        <div class="input2">
                            <input type="submit" name="suancc" value="Sửa Nhà cung cấp">
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
        <button class="close-btn">X</button>
    </div>
</div>