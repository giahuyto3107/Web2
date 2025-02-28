<?php
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

<div class="popup" id="popup">
    <div class="overlay"></div>
    <div class="popup-content">
        <div class="popup-header">
            <h2 class="popup-title">Xem Chi Tiết Đơn</h2>
            <button class="close-btn">✖</button>    
        </div>
        
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
                <td style="font-weight: bold; padding-top: 15px;">Danh Sách Sản Phẩm</td>
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
                <!-- /<div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">     -->
                    <td rowspan="4">
                        <img src="../../BackEnd/Uploads/Product Picture/<?= $row['image'] ?>" alt="image" class="book-image">
                        
                        <td><p style="font-weight: bold"><?= $row['product_name'] ?></p></td>
                    </td>
                    <tr><td>Lợi nhuận: <?= $row['profit'] . "%"?></td></tr>
                    <tr><td class="amount-title">SL: <?= $row['quantity'] ?></td></tr>
                    <tr><td class="price-title">Gia: <?= $row['price'] . " VND" ?></td></tr>              
                <!-- </div> -->
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
    html {
        width: 100%;
        height: 100%;
    }

    .popup{
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 100%;
        /* height: 1500px; */
        height: auto;
        background: rgba(0, 0, 0, 0.5); /* ✅ Creates the dark background effect */
        opacity: 1;
        transition: opacity 100ms ease-in-out 200ms; /* Smooth fade effect */
    }

    .popup-header {
        display: flex;
        justify-content: center; /* Center the title */
        align-items: center;
        position: relative; /* Needed for absolute positioning of close button */
        padding: 10px;
    }

    .popup-title {
        margin: 0 auto; /* Ensures the title stays centered */
        font-size: 20px;
        font-weight: bold;
    }

    .close-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background-color: #b0b0b0; /* Light gray */
        border: 2px solid #808080; /* Dark gray border */
        color: white;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 5px; /* Rounded corners */
        transition: all 0.3s ease-in-out; /* Smooth transition */
    }

    /* Hover effect */
    .close-btn:hover {
        background-color: #808080; /* Darker gray on hover */
        border-color: #606060; /* Even darker border */
        color: white;
        transform: translateY(-50%) scale(1.1); /* Slightly enlarge */
    }

    /* Active (clicked) effect */
    .close-btn:active {
        background-color: #b30000; /* Even darker red */
        transform: translateY(-50%) scale(0.95); /* Slight shrink effect */
    }

    .overlay {
        display: none; /* Initially hidden */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Dark overlay */
        z-index: 1000; /* Ensures it appears above other content */
    }

    .popup-active {
        display: block;
    }

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

    .price-title {
        color: #65e4dd;
    }


</style>


<script>
    function createPopup(id) {
        let popupNode = document.querySelector(id);
        let overlay = document.querySelector(".overlay");
        let closeBtn = document.querySelector(".close-btn");

        function openPopup() {
            popupNode.classList.add("active");
        }

        function closePopup() {
            popupNode.classList.remove("active"); // Hide the popup
            setTimeout(() => {
                window.location.href = "index.php?action=quanliphieunhap&query=them";
            }, 0); // Small delay to allow animation (optional)
        }

        overlay.addEventListener("click", closePopup); // Clicking overlay closes popup
        closeBtn.addEventListener("click", closePopup); // Clicking close button closes popup

        return openPopup;
        }

        let popup = createPopup("#popup"); // Create popup instance
        // document.querySelector("#open-popup").addEventListener("click", popup); // Open popup o
</script>