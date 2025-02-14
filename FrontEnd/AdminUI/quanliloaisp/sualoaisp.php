<?php
$sql_sua_loaisp = "SELECT * FROM category WHERE category_id='$_GET[idloaisp]' LIMIT 1";
$query_sua_loaisp = mysqli_query($conn, $sql_sua_loaisp);
?>

<div class="popup" id="edit-popup">
    <div class="overlay"></div>
    <div class="popup-content">
        <h2>Sửa Loại Sản Phẩm</h2>
        <div class="form">
            <div class="form-content">
                <form method="POST" action="../../BackEnd/Model/quanliloaisanpham/xuliloaisanpham.php">
                    <?php while ($dong = mysqli_fetch_array($query_sua_loaisp)) { ?>
                        <!-- Thêm trường ẩn để truyền category_id -->
                        <input type="hidden" name="category_id" value="<?php echo $dong['category_id'] ?>">

                        <div class="input1">
                            <p>Tên Loại sản phẩm</p>
                            <input type="text" name="tenloaisp" value="<?php echo $dong['category_name'] ?>" required>
                        </div>
                        <div class="input1">
                            <p>Mô tả Loại sản phẩm</p>
                            <input type="text" name="motaloaisp" value="<?php echo $dong['category_description'] ?>" required>
                        </div>
                        <div class="input2">
                            <input type="submit" name="sualoaisp" value="Sửa Loại sản phẩm">
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
        <button class="close-btn">X</button>
    </div>
</div>