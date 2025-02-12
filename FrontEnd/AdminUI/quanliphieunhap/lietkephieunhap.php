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

    <!-- <div class="top-right-button">
        <button id="open-popup-phieunhap">Thêm Sản Phẩm</button>
    </div> -->
    
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
                    <?php if ($row['status_id'] == 1) {
                            echo '<p class="active">Đã duyệt</p>';
                        } else if ($row['status_id'] == 2) {
                            echo '<a class="inactive" href="../../BackEnd/Model/quanliphieunhap/xuliphieunhap.php?purchase_order_id=' . $row['purchase_order_id'] . '&status=' . $row['status_id'] . '">Duyệt đơn</a>';
                        }
                    ?>        
                    <button class="detail-button" id = "open-popup" data-id="<?= $row['purchase_order_id'] ?>">Xem chi tiết</button>
                </td>
                <script>
                    document.querySelectorAll(".detail-button").forEach(button => {
                        button.addEventListener("click", function() {
                            let purchaseId = this.getAttribute("data-id");
                            window.location.href = "quanliphieunhap/XemChiTietDon.php?id=" + purchaseId;
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
        position: absolute;
        background-color: #e4e9f7;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
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

    .active, .inactive, .detail-button {
        display: inline-block;        /* Để có thể áp dụng padding và border */
        padding: 5px 10px;           /* Khoảng cách bên trong */
        text-decoration: none;        /* Bỏ gạch chân */
        color: white;                 /* Màu chữ trắng */
        border: 1px solid black;      /* Khung bên ngoài màu đen */
        border-radius: 15px;          /* Bo góc nhẹ */
        transition: background-color 0.3s; /* Hiệu ứng chuyển màu nền */
        padding: 6px 12px;
    }

    .inactive, .detail-button {
        cursor: pointer;
    }

    .active:hover, .inactive:hover {
        opacity: 0.8;                /* Hiệu ứng giảm độ trong suốt khi hover */
    }

    .active {
        background: #28a745; /* Green for approved */
        color: white;
    }

    .active:hover {
        background: #218838;
    }

    .inactive {
        background-color: #008cba;   /* Màu xanh lam cho khôi phục */
    }

    .detail-button {
        background: #007bff; /* Gray */
        color: white;
        font-weight: bold;
    }

    .detail-button:hover {
        background: #0056b3;
    }
    
</style>