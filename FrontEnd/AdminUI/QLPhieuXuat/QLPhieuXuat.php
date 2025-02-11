<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Thay bằng username của bạn
$password = "1234"; // Thay bằng password của bạn
$dbname = "web2_sql";
$port = "3305"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql_pn = 
        "SELECT po.purchase_order_id, 
            s.supplier_name, 
            u.full_name AS user_name, 
            po.order_date, 
            po.total_amount AS amount,
            po.total_price, 
            po.status_id, 
            po.import_status
        FROM purchase_order po
        JOIN supplier s ON s.supplier_id = po.supplier_id
        JOIN purchase_order_items pot ON po.purchase_order_id = pot.purchase_order_id
        JOIN user u ON u.user_id = po.user_id
        WHERE pot.purchase_order_item_id = (
            SELECT MIN(pot2.purchase_order_item_id)
            FROM purchase_order_items pot2
            WHERE pot2.purchase_order_id = po.purchase_order_id
        )
        ORDER BY po.purchase_order_id ASC;";

$query_pn = mysqli_query($conn, $sql_pn);
if (!$query_pn) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<div class="form">
    <div class="form-title">
        <h2>Quản lý phiếu xuất</h2>
    </div>

    <div class="top-right-button">
        <button id="open-popup-phieunhap">Thêm Sản Phẩm</button>
    </div>
    
    <div class="form-content">
        <table>
            <tr>
                <th>Mã đơn</th>
                <th>Nhà Xuất Bản</th>
                <th>Nhân Viên</th>
                <th>Ngày Lập</th>
                <th>Số lượng</th>
                <th>Tổng tiền</th>
                <th>Quản lý</th>
            </tr>

            <?php
                while ($row = mysqli_fetch_array($query_pn)) {
                   ; 
            ?>
            <tr>
                <td><?= $row['purchase_order_id'] ?></td>
                <td><?= $row['supplier_name'] ?></td>
                <td><?= $row['user_name'] ?></td>
                <td><?= $row['order_date'] ?></td>
                <td><?= $row['amount'] ?></td>
                <td><?= $row['total_price'] ?></td>
                <td>
                    <!-- <button class="detail-button" data-id="<?= $row['purchase_order_id'] ?>">Xem chi tiết</button> -->
                    <button class="detail-button" data-id="<?= $row['purchase_order_id'] ?>">Xem chi tiết</button>
                </td>
                <script>
                    document.querySelectorAll(".detail-button").forEach(button => {
                        button.addEventListener("click", function() {
                            let purchaseId = this.getAttribute("data-id");
                            window.location.href = "XemChiTietDon.php?id=" + purchaseId;
                        });
                    });
                </script>
            </tr>

            <?php
                }
            ?>
        </table>
    </div>
</div>



<style>
    .form {
        background-color: #e4e9f7;
        width: 100%;
        height: 100%;
    }

    .form-title {
        text-align: center;
    }

    .form-content {
        background-color: white;
        border-radius: 20px;
        width: 100%;
        margin: 10px;
        padding: 20px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }
    tr, th {
        border: 1px solid #ccc;
        
    }

    th {
        background-color: #f2f2f2;
    }

    td {
        background-color: white;
        text-align: center;
    }

    .top-right-button {
        position: absolute;
        top: 1.5%;
        left: 88%;
    }

    #open-popup-phieunhap {
        color: white;
        background-color: #3284ed;
        border-radius: 15px;
        padding: 12px 25px;
        border: none
    }

    #dis-enable-button {
        background-color: orange;
        color: white;
        padding: 5px;
    }

    #detail-button {
        padding: 6px 12px;
        border-radius: 15px;
    }
    
</style>