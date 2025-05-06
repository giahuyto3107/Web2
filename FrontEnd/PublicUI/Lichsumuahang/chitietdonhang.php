<?php
include ('../../../BackEnd/Config/config.php');

if (!isset($_GET['order_id'])) {
    echo "<section><h2>Không có ID đơn hàng!</h2></section>";
    exit;
}

$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
$query = "SELECT orders.order_id, orders.order_date, orders.status_id, orders.total_amount, orders.phone, orders.address, orders.payment_method          FROM orders 
          WHERE orders.order_id = '$order_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<section><h2>Không tìm thấy đơn hàng!</h2></section>";
    exit;
}

$order = mysqli_fetch_assoc($result);
$order_status = $order['status_id'];

$query_items = "SELECT product.product_id, product.product_name, order_items.quantity, 
                       product.image_url, order_items.price, order_items.review
                FROM order_items 
                JOIN product ON order_items.product_id = product.product_id 
                WHERE order_items.order_id = '$order_id'";
$result_items = mysqli_query($conn, $query_items);

if (!$result_items) {
    echo "<section><h2>Lỗi khi lấy chi tiết đơn hàng!</h2></section>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <title>Chi Tiết Đơn Hàng</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* body {
            background: #ffffff;
            padding: 50px;
            min-height: 100vh;
            color: #1a1a1a;
        } */

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px;
        }

        

        /* Header */
        .order-header {
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 40px;
        }

        .order-header h2 {
            font-size: 1.6rem;
            font-weight: 400;
            letter-spacing: 1px;
            color: #1a1a1a;
        }

        .order-header .order-info {
            font-size: 0.85rem;
            font-weight: 300;
            color: #666;
            margin-top: 5px;
        }

        /* Order Items */
        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-items tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .order-items th, .order-items td {
            padding: 20px 15px;
            vertical-align: middle;
            text-align: left;
        }

        .order-items th {
            font-size: 0.8rem;
            font-weight: 400;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-items .product-image {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border: 1px solid #e0e0e0;
        }

        .order-items .product-name {
            font-size: 1rem;
            font-weight: 400;
            color: #1a1a1a;
        }

        .order-items .btn-review {
            background: #d4af37;
            color: #fff;
            border: none;
            padding: 8px 20px;
            font-size: 0.85rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s ease;
        }

        .order-items .btn-review:hover {
            background: #b7950b;
        }

        .order-items .text-success {
            color: #1a1a1a;
            font-weight: 400;
            font-style: italic;
        }

        /* Status Tracker */
        .status-tracker {
            padding: 30px 0;
            margin-bottom: 40px;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }

        .status-tracker .d-flex {
            align-items: center;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto;
        }

        .status-tracker .btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            /* border: 1px solid #e0e0e0; */
        }

        .status-tracker .bg-success {
            background: #1a1a1a !important;
            color: #fff;
        }

        .status-tracker .bg-secondary {
            background: #1a1a1a !important;
            color: #666;
        }

        .status-tracker .w-50.p-1 {
            height: 2px;
            /* background: #e0e0e0; */
        }

        .status-tracker .bg-success + .w-50.p-1 {
            background: #1a1a1a;
        }

        .status-tracker .status-labels {
            display: flex;
            justify-content: space-between;
            text-align: center;
            margin-top: 15px;
            font-size: 0.85rem;
            color: #666;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .status-tracker .status-labels p {
            margin: 0;
            font-weight: 300;
        }

        /* Total */
        .order-total {
            text-align: right;
            padding: 20px 0;
            font-size: 1.2rem;
            font-weight: 400;
            color: #1a1a1a;
        }

        .order-total span {
            color: #d4af37;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            padding: 10px 25px;
            background: #1a1a1a;
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s ease;
        }

        .back-btn:hover {
            background: #333;
            color: #fff;
        }

        /* Modal */
        .modal-content {
            border-radius: 0;
            border: none;
        }

        .modal-header {
            background: #1a1a1a;
            color: #fff;
            padding: 15px 20px;
            border-bottom: none;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 400;
            letter-spacing: 1px;
        }

        .modal-body {
            padding: 30px;
            background: #fff;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 400;
            color: #1a1a1a;
            letter-spacing: 0.5px;
        }

        .form-control, .form-select {
            border-radius: 0;
            border: 1px solid #e0e0e0;
            padding: 10px;
            font-size: 0.9rem;
            font-weight: 300;
        }

        .form-control:focus, .form-select:focus {
            border-color: #d4af37;
            box-shadow: none;
        }

        textarea.form-control {
            resize: none;
            height: 100px;
        }

        .btn-submit {
            width: 100%;
            padding: 10px;
            background: #d4af37;
            color: #fff;
            border: none;
            font-size: 0.9rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s ease;
        }

        .btn-submit:hover {
            background: #b7950b;
        }
    </style>
</head>
<body>
<div class="container">
<div class="order-header">
    <h2>Đơn Hàng #<?= htmlspecialchars($order['order_id']) ?></h2>
    <div class="order-info">
        <div><b>Ngày đặt hàng:</b><?= htmlspecialchars($order['order_date']) ?></div>
        <div><b>Phương thức thanh toán:</b> 
            <?php
            $payment_method = htmlspecialchars($order['payment_method']);
            if ($payment_method === 'cod') {
                echo 'Tiền mặt';
            } elseif ($payment_method === 'Online') {
                echo 'Chuyển khoản';
            } else {
                echo $payment_method ?: 'Không xác định';
            }
            ?>
        </div>
        <div><b>Số điện thoại:</b> <?= htmlspecialchars($order['phone'] ?: 'Không có') ?></div>
        <div><b>Địa chỉ: </b> <?= htmlspecialchars($order['address'] ?: 'Không có') ?></div>
        <?php if ($order_status == 7) : ?>
            <span class="text-danger ms-3">Đơn hàng bị hủy</span>
        <?php endif; ?>
    </div>
</div>

    <div class="order-items">
        <table>
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Thành tiền</th>
                    <?php if ($order_status == 5) : ?> 
                        <th>Đánh giá</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                    while ($item = mysqli_fetch_assoc($result_items)) { 
                        $price = $item['price'] / $item['quantity'];
                ?>
                    <tr>
                        <td><img src="../../../BackEnd/Uploads/Product Picture/<?= urlencode($item['image_url']) ?>" class="product-image"></td>
                        <td class="product-name"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= number_format($price, 0, ',', '.') ?> đ</td>
                        <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                        <?php if ($order_status == 5) : ?>
                            <td>
                                <?php if ($item['review'] == 0) : ?>
                                    <button class="btn btn-review" data-bs-toggle="modal" data-bs-target="#reviewModal" 
                                            data-product-id="<?= $item['product_id'] ?>" 
                                            data-product-name="<?= htmlspecialchars($item['product_name']) ?>">
                                        Đánh giá
                                    </button>
                                <?php else : ?>
                                    <span class="text-success">Đã đánh giá</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($order_status != 7) : ?>
    <div class="status-tracker">
        <div class="d-flex">
            <?php
            $status_map = [
                4 => 'Pending',
                5 => 'Order Confirmed',
                6 => 'Delivered'
            ];

            $status_colors = [
                4 => 'bg-success',
                5 => 'bg-success',
                6 => 'bg-success'
            ];

            $icons = [
                4 => "fa-spinner",
                5 => "fa-clipboard-check",
                6 => "fa-house-chimney"
            ];

            $order_status = isset($order['status_id']) ? $order['status_id'] : 3;
            $keys = array_keys($status_map);
            $last_key = end($keys);
            foreach ($status_map as $key => $value) {
                $is_completed = $key <= $order_status;
                $btn_color = $is_completed ? $status_colors[$key] : 'bg-secondary';
                $icon = $icons[$key];
            ?>
                <button class="btn <?= $btn_color ?> text-white" data-bs-toggle="tooltip" title="<?= $value ?>">
                    <i class="fa-solid <?= $icon ?>"></i>
                </button>
                <?php if ($key != $last_key): ?>
                    <span class="w-50 p-1 mx-n1 rounded mt-auto mb-auto"></span>
                <?php endif; ?>
            <?php } ?>
        </div>
        <div class="status-labels">
            <p>Đang xử lý</p>
            <p>Xác nhận đơn</p>
            <p>Đã giao</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="order-total">
        Tổng tiền: <span><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</span>
    </div>

    <a href="?page=orders" data-page="orders" class="back-btn">Quay lại</a>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Đánh giá sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="http://localhost/Web2/FrontEnd/PublicUI/Lichsumuahang/submit_review.php" method="POST">
                    <input type="hidden" name="product_id" id="product_id">
                    <input type="hidden" name="order_id" value="<?= $order_id ?>">
                    
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Sản phẩm:</label>
                        <input type="text" id="product_name" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chấm điểm:</label>
                        <select class="form-select" name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐ - Tuyệt vời</option>
                            <option value="4">⭐⭐⭐⭐ - Tốt</option>
                            <option value="3">⭐⭐⭐ - Bình thường</option>
                            <option value="2">⭐⭐ - Tệ</option>
                            <option value="1">⭐ - Rất tệ</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="review_text" class="form-label">Nhận xét:</label>
                        <textarea class="form-control" name="review_text" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-submit">Gửi đánh giá</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var reviewModal = document.getElementById('reviewModal');
    reviewModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var productId = button.getAttribute('data-product-id');
        var productName = button.getAttribute('data-product-name');
        
        var modal = this;
        modal.querySelector('#product_id').value = productId;
        modal.querySelector('#product_name').value = productName;
    });
</script>
<script>
    document.querySelector("#reviewModal form").addEventListener("submit", function(event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch("http://localhost/Web2/FrontEnd/PublicUI/Lichsumuahang/submit_review.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                Swal.fire({
                    title: "Thành công!",
                    text: data.message,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: "Lỗi!",
                    text: data.message,
                    icon: "error",
                    confirmButtonText: "Thử lại"
                });
            }
        })
        .catch(error => console.error("Lỗi:", error));
    });
</script>
</body>
</html>