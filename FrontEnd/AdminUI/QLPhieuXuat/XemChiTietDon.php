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
$purchase_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($purchase_id == 0) {
    die("Invalid purchase_order_id.");
}

// Secure the SQL query with a prepared statement
$sql_chiTietPN = "SELECT u.full_name AS user_name, s.supplier_name AS supplier_name, 
                    pot.profit as profit, po.order_date
                FROM purchase_order po
                JOIN supplier s ON s.supplier_id = po.supplier_id
                JOIN purchase_order_items pot ON po.purchase_order_id = pot.purchase_order_id
                JOIN user u ON u.user_id = po.user_id
                WHERE po.purchase_order_id = ? 
                ORDER BY pot.purchase_order_item_id ASC";

$stmt = $conn->prepare($sql_chiTietPN);
$stmt->bind_param("i", $purchase_id);
$stmt->execute();
$query_pn = $stmt->get_result();

if (!$query_pn) {
    die("Query failed: " . $conn->error);
}
?>

<div class="overlay">
    <div class="popup-content">
        <h2>Xem Chi Tiết Đơn</h2>
        <table>
            <?php
                while ($row = mysqli_fetch_array($query_pn)) {
            ?>
            <tr>
                <td>Nhân viên</td>
                <td><?= $row['user_name'] ?></td>
            </tr>
            <tr>
                <td>Nhà Xuất Bản</td>
                <td><?= $row['supplier_name'] ?></td>
            </tr>
            <tr>
                <td>Ngày</td>
                <td><?= $row['order_date'] ?></td>
            </tr>

            <?php
                }
            ?>

            <tr>
                <td>Danh Sách Sản Phẩm</td>
            </tr>
            
            <?php
                $sql_chiTietSanPham_PN = 
                    "SELECT p.image_url AS image, p.product_name, 
                        pot.quantity as quantity, pot.price as price,
                        pot.profit AS profit
                    FROM purchase_order po
                    JOIN supplier s ON s.supplier_id = po.supplier_id
                    JOIN purchase_order_items pot ON po.purchase_order_id = pot.purchase_order_id
                    JOIN product p ON p.product_id = pot.product_id
                    WHERE po.purchase_order_id = ?
                    ORDER BY p.product_name ASC";

                $stmt = $conn->prepare($sql_chiTietSanPham_PN);

                // ✅ Debugging
                if (!$stmt) {
                die("Query preparation failed: " . $conn->error);
                }

                $stmt->bind_param("i", $purchase_id);

                if (!$stmt->execute()) {
                die("Query execution failed: " . $stmt->error);
                }

                $query_pn = $stmt->get_result();
                if (!$query_pn) {
                die("Fetching results failed: " . $stmt->error);
                }

                $total_value = 0.0;
                while ($row = mysqli_fetch_array($query_pn)) {
                    $profit = $row['profit'] ;
                    $quanity = $row['quantity'];
                    $price = $row['price'];
                    $total_value += (1 + $profit/100.0) * $quanity * $price;
            ?> 

            <tr>
                <div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">    
                    <td rowspan="4">
                        <img src="../../../BackEnd/Uploads/Product Picture/<?= $row['image'] ?>" alt="image" class="book-image">
                        
                        <td><p style="font-weight: bold"><?= $row['product_name'] ?></p></td>
                    </td>
                    <tr><td>Lợi nhuận: <?= $row['profit'] . "%"?></td></tr>
                    <tr><td class="amount-title">SL: <?= $row['quantity'] ?></td></tr>
                    <tr><td class="price-title">Gia: <?= $row['price'] . " VND" ?></td></tr>              
                </div>
            </tr>
        


            <?php
                }
            ?>

            <tr>
                <div style="display:flex">
                    <td> <p style="font-weight: bold; text-align: center; font-size: 20px">Tổng tiền: </p></td>
                    <td style="color: #65e4dd; font-size: 20px"> <?= $total_value . " VND" ?></td>
                </div>
            </tr>
        </table>
    </div>
</div>


<style>
    /* .overlay {
        background-color: green;
        border-radius: 10px;
    } */
    .popup-content {
        border-radius: 15px;
        padding: 15px;
        margin: 50px;
        background-color: #ffffff;
    }

    h2 {
        text-align: center;
    }

    table {
        display: flex;
        justify-content: center;
    }

    .book-image {
        width: 160px;
        height: 155px;
        margin: 10px 20px;
    }

    .amount-title {

    }

    .price-title {
        color: #65e4dd;
    }


</style>


