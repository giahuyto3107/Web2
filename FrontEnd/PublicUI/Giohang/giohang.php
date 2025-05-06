<?php
include '../../../BackEnd/Config/config.php';
session_start();


if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

// Lấy dữ liệu giỏ hàng
$sql = "SELECT cart_items.product_id, product.product_name, product.image_url, product.price, cart_items.quantity 
        FROM cart_items 
        JOIN product ON cart_items.product_id = product.product_id
        WHERE cart_items.user_id = $user_id";

$result = mysqli_query($conn, $sql);

$cart_items = [];
$total_price = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

$sql1 = "SELECT full_name
         FROM user
         Where user_id=$user_id";

$result1 = mysqli_query($conn, $sql1);
$user = mysqli_fetch_assoc($result1);
$full_name = $user['full_name'];
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        h4{
            color:black !important;
            /* font-weight: 600 !important; */
            font-size: 20px !important;
        }
        .cart-container { max-width: 900px; margin-top:10px; margin-bottom:10px; margin: auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .product-details {
            padding: 10px;
            max-height: 400px;  
            overflow-y: auto; 
            padding-right: 10px; 
        }

        .product-details::-webkit-scrollbar {
            width: 8px;
        }

        .product-details::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        .product-details::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }

        .items { box-shadow: 5px 5px 4px -1px rgba(0, 0, 0, 0.08); padding: 10px; border-radius: 5px; }
        .payment-info {  padding: 15px; border-radius: 6px; color: #000000; }
        .btn-primary { background: blue; border: none; }
        .form-control { border-radius: 5px; }
        .payment-method label { margin-right: 15px; cursor: pointer; }
        .quantity-display {
            min-width: 30px; 
            text-align: center;
        }

        .total-price {
            min-width: 70px; 
            display: inline-block;
            text-align: right;
            font-size: 14px;
        }

        #checkout-button:disabled {
            cursor: not-allowed; 
            opacity: 0.6; 
        }


    /* Khi radio button bị disabled, hiển thị icon cấm */
        .disabled-radio:disabled {
            cursor: not-allowed !important;
            opacity: 0.6;
        }

        .text-center {
            text-align: center;
            font-size: 24px;
            color: #ff6347; 
            margin-top: 50px;
            font-weight: bold;
        }

        p {
            
            font-size: 18px;
            color: #555; 
            margin: 20px auto;
            max-width: 600px; 
            line-height: 1.5;
        }

        .funny-message {
            font-style: italic;
            color: #007bff; 
            margin-top: 10px;
            border: 1px dashedrgb(119, 119, 119); 
            padding: 15px;
            border-radius: 10px; 
            background-color: #f0f8ff;
        }

    </style>
</head>
<body>

<div class="container mt-5 cart-container">
    <h4 class="mb-3">🛒 Giỏ hàng của bạn</h4>
    <hr>

    <div class="row">
        <div class="col-md-8">
            <div class="product-details">
                <?php if (!empty($cart_items)) { ?>
                    <?php foreach ($cart_items as $item) { ?>
                        <div class="d-flex justify-content-between align-items-center mt-3 p-2 items">
                            <div class="d-flex flex-row">
                            <input type="checkbox" class="product-checkbox" id="checkbox-<?= $item['product_id'] ?>" data-product-id="<?= $item['product_id'] ?>" data-price="<?= $item['price'] * $item['quantity'] ?>">

                                <img class="rounded ml-2" src="../../../BackEnd/Uploads/Product Picture/<?= htmlspecialchars($item['image_url']) ?>" width="50">
                                <div class="ml-3">
                                    <span class="font-weight-bold1"><?= htmlspecialchars($item['product_name']) ?></span>
                                    <div class="text-muted small"><?= number_format($item['price'], 0, ',', ',') ?>₫</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center quantity-container">
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-secondary update-quantity" data-product-id="<?= $item['product_id'] ?>" data-action="decrease">-</button>
                                    <div class="quantity-display mx-2" id="quantity-<?= $item['product_id'] ?>"><?= $item['quantity'] ?></div>
                                    <button class="btn btn-sm btn-outline-secondary update-quantity" data-product-id="<?= $item['product_id'] ?>" data-action="increase">+</button>
                                </div>
                                <span class="font-weight-bold ml-4 total-price">
                                    <span id="total-<?= $item['product_id'] ?>"><?= number_format($item['price'] * $item['quantity'], 0, ',', ',') ?>₫</span>
                                </span>

                                <a href="#" class="fa fa-trash ml-3 text-danger remove-item" data-product-id="<?= $item['product_id'] ?>"></a>

                            </div>
                        </div>

                    <?php } ?>
                <?php } else { ?>
                    <p class="text-center">Giỏ hàng trống! 🛒</p>
                    <p class="funny-message">"Nếu bạn thích thứ gì đó thì cứ mua đi, đắt một chút thì cũng có sao. Vốn dĩ để gặp được người mình thích đã rất khó rồi, giờ đến thứ mình thích chẳng lẽ cũng không thể mua!"</p>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-4">
    <div class="payment-info">
        <h5>Thanh toán</h5>
        <hr>
        <div class="mb-3">
            <label for="phone">📞 Số điện thoại</label>
            <input type="text" class="form-control" id="phone" placeholder="Nhập số điện thoại" oninput="saveUserInfo()">
            <span class="error-message text-danger"></span>
        </div>
        <div class="mb-3">
            <label for="address">📍 Địa chỉ giao hàng</label>
            
            <input type="text" class="form-control" id="address" placeholder="Nhập địa chỉ" oninput="saveUserInfo()">
            <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="defaultAddress" onchange="toggleDefaultAddress()">
        <label class="form-check-label" for="defaultAddress">
            Sử dụng địa chỉ mặc định
        </label>
    </div>
        </div>

        <script>
            function saveUserInfo(type, value) {
                if (typeof type === 'undefined' || type === null || type === '') {
                    console.error("Lỗi: type không hợp lệ:", type);
                    return;
                }
                if (typeof value === 'undefined' || value === null) {
                    value = '';
                }

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "http://localhost/Web2/FrontEnd/PublicUI/Giohang/save_user_info.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            let response = JSON.parse(xhr.responseText);
                            console.log("Response:", response);
                            console.log("Session hiện tại:", response.session);
                        } else {
                            console.error("Lỗi server:", xhr.status, xhr.statusText);
                        }
                    }
                };

                let data = type + "=" + encodeURIComponent(value);
                console.log("Sending:", data);
                xhr.send(data);
            }

            function toggleDefaultAddress() {
    var checkbox = document.getElementById("defaultAddress");
    var addressInput = document.getElementById("address");

    if (checkbox.checked) {
        // Lấy địa chỉ mặc định từ server
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "http://localhost/Web2/FrontEnd/PublicUI/Giohang/get_default_address.php", true);
        
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success && response.address) {
                    addressInput.value = response.address; // Cập nhật giá trị vào input
                    addressInput.disabled = true;
                    saveUserInfo("address", response.address);
                    checkPaymentInfo(); // Gọi lại để kiểm tra ngay lập tức
                } else {
                    console.error("Không tìm thấy địa chỉ mặc định:", response.message);
                    checkbox.checked = false;
                    checkPaymentInfo(); // Cập nhật trạng thái
                }
            }
        };
        xhr.send();
    } else {
        addressInput.value = "";
        addressInput.disabled = false;
        checkPaymentInfo(); // Cập nhật trạng thái
    }
}

            // Gắn sự kiện input sau khi DOM sẵn sàng
            var addressInput = document.getElementById("address");
            var phoneInput = document.getElementById("phone");

            if (addressInput) {
                addressInput.addEventListener("input", function () {
                    if (!document.getElementById("defaultAddress").checked) {
                        saveUserInfo("address", this.value);
                    }
                });
            } else {
                console.error("Không tìm thấy #address trong DOM");
            }

            if (phoneInput) {
                phoneInput.addEventListener("input", function () {
                    saveUserInfo("phone", this.value);
                });
            } else {
                console.error("Không tìm thấy #phone trong DOM");
            }
        </script>
        <div class="payment-method mb-3">
    <label>💳 Chọn hình thức thanh toán:</label><br>
    
    <label>
        <input type="radio" name="payment" value="cod" checked> Ship COD
    </label>

    <label id="momo-label" class="disabled-radio">
        <input type="radio" name="payment" value="momo" id="momo-option" disabled> Thanh toán Online (MoMo)
    </label>

    <form id="momo-form" method="POST" action="http://localhost/Web2/FrontEnd/PublicUI/Giohang/thanhtoanmomo.php">
        <input type="hidden" name="payment_method" value="momo">
    </form>
</div>
<div class="d-flex justify-content-between">
    <span><b>Tổng tiền:</b></span>
    <span><b><span id="cart-total">0</span></b></span>
</div>
<button id="checkout-button" class="btn btn-light btn-block mt-3" disabled>🛍 Thanh toán ngay</button>
</div>

                



                
        </div>
    </div>
</div>

<!-- <script>
    document.getElementById("momo-option").addEventListener("change", function() {
        if (this.checked) {
            document.getElementById("momo-form").submit(); 
        }
    });
    document.getElementById("QR-option").addEventListener("change", function() {
        if (this.checked) {
            document.getElementById("QR-form").submit(); 
        }
    });
</script> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    // Xóa sản phẩm khỏi giỏ hàng
    $(".remove-item").click(function (e) {
        e.preventDefault(); 
        let productId = $(this).data("product-id"); 
        let itemRow = $(this).closest(".items"); 

        Swal.fire({
            title: "Bạn có chắc chắn?",
            text: "Sản phẩm sẽ bị xóa khỏi giỏ hàng!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Xóa ngay giùm tui!",
            cancelButtonText: "Hủy"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "http://localhost/Web2/FrontEnd/PublicUI/Giohang/remove_item.php", 
                    type: "POST",
                    data: { product_id: productId },
                    dataType: "json", 
                    success: function (response) {
                        if (response.success) {
                            itemRow.fadeOut(300, function () { $(this).remove(); });
                            Swal.fire({
                                title: "Đã xóa!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            });
                        } else {
                            Swal.fire({
                                title: "Lỗi!",
                                text: response.message,
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: "Lỗi!",
                            text: "Không thể kết nối đến server. Vui lòng thử lại",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            }
        });
    });

    // Cập nhật tổng tiền
    function updateTotalPrice() {
        let total = 0;
        $(".product-checkbox:checked").each(function () {
            total += parseFloat($(this).data("price"));
        });
        $("#cart-total").text(new Intl.NumberFormat('vi-VN').format(total) + "₫");
    }

    // Cập nhật số lượng sản phẩm
    $(".update-quantity").click(function () {
        var productId = $(this).data("product-id");
        var action = $(this).data("action");

        $.ajax({
            url: "http://localhost/Web2/FrontEnd/PublicUI/Giohang/update_cart.php",
            type: "POST",
            data: { product_id: productId, action: action },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $("#quantity-" + productId).text(data.new_quantity);
                    $("#total-" + productId).text(new Intl.NumberFormat('vi-VN').format(data.new_total_price) + "₫");
                    $("#checkbox-" + productId).data("price", parseFloat(data.new_total_price));
                    if ($("#checkbox-" + productId).prop("checked")) {
                        updateTotalPrice();
                    }
                }
            }
        });
    });

    // Cập nhật sản phẩm được chọn
    function updateSelectedProducts() {
        let selectedProducts = [];
        $(".product-checkbox:checked").each(function () {
            let productId = $(this).data("product-id");
            let price = parseFloat($(this).data("price"));
            let quantity = $(this).closest(".items").find(".quantity-display").text();
            selectedProducts.push({ product_id: productId, price: price, quantity: quantity });
        });

        $.ajax({
            url: "http://localhost/Web2/FrontEnd/PublicUI/Giohang/update_selected_products.php",
            type: "POST",
            data: { selectedProducts: JSON.stringify(selectedProducts) },
            success: function (response) {
                console.log("Sản phẩm đã chọn đã được cập nhật!");
            }
        });
    }

    // Kiểm tra thông tin thanh toán
    function checkPaymentInfo() {
        let phone = $("#phone").val().trim();
        let address = $("#address").val().trim();
        let hasSelectedProduct = $(".product-checkbox:checked").length > 0;
        let phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$/;
        let isDefaultAddress = $("#defaultAddress").is(":checked"); 

        if (phoneRegex.test(phone) && 
            (isDefaultAddress || address !== "") && 
            hasSelectedProduct) {
            $("#checkout-button").prop("disabled", false);
            $("#momo-option").prop("disabled", false);
            $("#phone").css("border-color", "");
            $("#address").css("border-color", "");
            $("#phone").next(".error-message").text("");
            $("#address").next(".error-message").text("");
        } else {
            $("#checkout-button").prop("disabled", true);
            $("#momo-option").prop("disabled", true);

            if (!phoneRegex.test(phone)) {
                $("#phone").css("border-color", "red");
                $("#phone").next(".error-message").text("❌ Số điện thoại không hợp lệ");
            } else {
                $("#phone").css("border-color", "");
                $("#phone").next(".error-message").text("");
            }

            if (!isDefaultAddress && address === "") {
                $("#address").css("border-color", "red");
                $("#address").next(".error-message").text("❌ Vui lòng nhập địa chỉ");
            } else {
                $("#address").css("border-color", "");
                $("#address").next(".error-message").text("");
            }

            if (!hasSelectedProduct) {
                console.log("Vui lòng chọn ít nhất một sản phẩm");
            }
        }
    }

    // Sự kiện thay đổi checkbox sản phẩm
    $(".product-checkbox").change(function () {
        updateTotalPrice();
        updateSelectedProducts();
        checkPaymentInfo();
    });

    // Sự kiện thay đổi thông tin thanh toán
    $("#phone, #address, #defaultAddress").on("input change", checkPaymentInfo);

    // Hàm hiển thị modal xác nhận
    function showConfirmationModal() {
        if (!$("#checkout-button").prop("disabled")) { 
            let name = "<?= $full_name ?>"; 
            let phone = $("#phone").val();
            let diachi = $("#address").val();
            let paymentMethod = $("input[name='payment']:checked").val();
            let totalPrice = $("#cart-total").text();

            $("#confirm-name").text(name);
            $("#confirm-phone").text(phone);
            $("#confirm-address").text(diachi);
            $("#confirm-payment-method").text(paymentMethod === "cod" ? "Ship COD" : "Thanh toán Online (MoMo)");
            $("#confirm-total-price").text(totalPrice);

            let productList = $("#confirm-product-list");
            productList.empty();
            $(".product-checkbox:checked").each(function () {
                let productImg = $(this).closest(".items").find(".rounded").attr("src"); 
                let productName = $(this).closest(".items").find(".font-weight-bold1").text();
                let productQuantity = $(this).closest(".items").find(".quantity-display").text();
                let productTotal = $(this).closest(".items").find(".total-price").text();

                productList.append(`
                    <li class="d-flex align-items-center mb-3">
                        <img src="${productImg}" width="50" class="rounded mr-3">
                        <div>
                            <span class="font-weight-bold">${productName}</span>
                            <div>Số lượng: ${productQuantity}</div>
                            <div>${productTotal}</div>
                        </div>
                    </li>
                `);
            });

            $("#confirmPaymentModal").modal("show");
        }
    }

    // Sự kiện nhấn nút thanh toán
    $("#checkout-button").click(function () {
        showConfirmationModal();
    });

    // Sự kiện thay đổi phương thức thanh toán
    $("input[name='payment']").change(function () {
        if (!$(this).prop("disabled")) {
            showConfirmationModal();
        }
    });

    // Xử lý xác nhận thanh toán
    $("#confirm-payment-button").click(function () {
        let paymentMethod = $("input[name='payment']:checked").val();
        let products = []; 
        $(".product-checkbox:checked").each(function () {
            let productId = $(this).data("product-id");
            let quantity = $(this).closest(".items").find(".quantity-display").text();
            products.push({ product_id: productId, quantity: quantity });
        });

        if (products.length === 0) {
            Swal.fire({
                title: "Lỗi!",
                text: "Vui lòng chọn ít nhất một sản phẩm để thanh toán.",
                icon: "warning",
                confirmButtonText: "OK"
            });
            return;
        }

        if (paymentMethod === "cod") {
            $.ajax({
                url: "http://localhost/Web2/FrontEnd/PublicUI/Giohang/process_payment.php",
                type: "POST",
                data: { 
                    user_id: <?= $user_id ?>, 
                    total_amount: $("#cart-total").text().replace(/[^0-9]/g, ''), 
                    status_id: 3, 
                    payment_method: paymentMethod, 
                    phone: $("#phone").val(), 
                    address: $("#address").val(), 
                    products: JSON.stringify(products) 
                },
                success: function (response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        Swal.fire({
                            title: "Thanh toán thành công!",
                            text: "Cảm ơn bạn đã mua hàng.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            $("#confirmPaymentModal").modal("hide");
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Lỗi!",
                            text: data.message,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Lỗi!",
                        text: "Không thể kết nối đến server. Vui lòng thử lại.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            });
        } else if (paymentMethod === "momo") {
            let form = $("#momo-form");
            form.empty(); 
            form.append(`<input type="hidden" name="user_id" value="<?= $user_id ?>">`);
            form.append(`<input type="hidden" name="total_amount" value="${$("#cart-total").text().replace(/[^0-9]/g, '')}">`);
            form.append(`<input type="hidden" name="phone" value="${$("#phone").val()}">`);
            form.append(`<input type="hidden" name="address" value="${$("#address").val()}">`);
            form.append(`<input type="hidden" name="products" value='${JSON.stringify(products)}'>`);
            form.append(`<input type="hidden" name="payment_method" value="momo">`);
            form.submit(); 
        }
    });
});
</script>

<div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmPaymentModalLabel">Xác nhận thanh toán</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Tên người mua:</strong> <span id="confirm-name"></span></p>
                <p><strong>Số điện thoại:</strong> <span id="confirm-phone"></span></p>
                <p><strong>Địa chỉ giao hàng:</strong> <span id="confirm-address"></span></p>
                <p><strong>Phương thức thanh toán:</strong> <span id="confirm-payment-method"></span></p>
                <p><strong>Danh sách sản phẩm:</strong></p>
                <ul id="confirm-product-list" class="list-unstyled"></ul> 
                <p class="mt-3"><strong>Tổng tiền:</strong> <span id="confirm-total-price" class="text-danger font-weight-bold"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-payment-button">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
<style>

.modal.fade .modal-dialog {
    transform: translateY(-20px);
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: translateY(0);
}


.modal-content {
    font-family: 'Poppins', sans-serif;
    border-radius: 10px;
    border: none;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    padding: 15px;
    background: linear-gradient(to bottom, #ffffff, #f8f9fa);
}

.modal-header {
    background-color: #007bff;
    color: white;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    padding: 15px;
}


.modal-title {
    font-weight: bold;
    font-size: 18px;
}

.close {
    color: white;
    opacity: 0.8;
    font-size: 22px;
}

.close:hover {
    opacity: 1;
}


.modal-body {
    padding: 20px;
    font-size: 16px;
    line-height: 1.6;
}

#confirm-product-list {
    list-style: none;
    padding-left: 0;
}

#confirm-product-list li {
    padding: 5px 0;
    border-bottom: 1px solid #ddd;
}


#confirm-total-price {
    font-size: 18px;
    font-weight: bold;
    color: #e74c3c;
}


.modal-footer {
    border-top: 1px solid #ddd;
    padding: 15px;
    display: flex;
    justify-content: space-between;
}


.modal-footer .btn {
    border-radius: 5px;
    font-size: 16px;
    padding: 8px 15px;
}


.modal-footer .btn-secondary {
    background-color: #6c757d;
    border: none;
}

.modal-footer .btn-secondary:hover {
    background-color: #5a6268;
}


.modal-footer .btn-primary {
    background-color: #28a745;
    border: none;
}

.modal-footer .btn-primary:hover {
    background-color: #218838;
}

</style>
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
