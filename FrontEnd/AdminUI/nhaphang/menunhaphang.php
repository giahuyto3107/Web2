<?php
$sql_product_invoice = 
        "SELECT image_url, product_name, 
        product_description, stock_quantity, price, product_id
        FROM product p
        ORDER BY p.product_id ASC;";

$query_product_invoice = mysqli_query($conn, $sql_product_invoice);
if (!$query_product_invoice) {
    die("Query failed: " . mysqli_error($conn));
}
?>
 
<div class="form">
    <div class="import-block">
        <div class="form-title">
            <h1>Nhập sách</h1>
            <h6>Khám phá tri thức - chia sẻ niềm vui</h6>
        </div>
        
        <div class="form-content">
            <table>
                <tr>
                    <td>Mã SP</td>
                    <td>Tên</td>
                    <td>Mô tả</td>
                    <td>SL</td>
                    <td>Thao tác</td>
                    
                </tr>
            <?php
                while($row = mysqli_fetch_array($query_product_invoice)) {
            ?>

                <tr>
                    <td><?= $row['product_id'] ?></td>
                    <td><?= $row['product_name'] ?></td>
                    <td><?= $row['product_description'] ?></td>
                    <td><?= $row['stock_quantity'] ?></td>
                    <td><button class="select-button">Chọn</button></td>
                </tr>
                
            <?php
                }
            ?>

            </table>
        </div>
    </div>

    
    <div class="bill-block">
        <div class="bill-title">
            <h3>Hóa đơn</h3>
            <label for="supplier">Nhà xuất bản</label>

            <select name="supplier" id="supplier">
            </select>
        </div>

        <div class="invoice-block">
            <div class="invoice-content">
     
            </div>
            <div class="cart-info">
                <div class="priceContainer">
                        <h2>Tổng tiền hàng:</h2>
                        <h2 class="price"></h2>
                    </div>
                    <div class="percentContainer">
                        <input type="text" id="percent" name="percent" placeholder="Lợi nhuận bán ra" oninput="updatePercentage(this)" value="0">
                        <label for="percent">%</label>
                        <button type="button" id="applyAllBtn">Apply All</button>
                    </div>
                <button type="button" id="confirmBtn">Đặt hàng</button>
            </div>
        </div>
    </div>
 
</div>

<script>
    function loadSupplier() {
        const selectElement = document.getElementById('supplier');

        fetch('quanlinhacungcap/fetch_ncc.php')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    selectElement.innerHTML = '<option value="default">Chọn nhà xuất bản</option>';
                    result.data.forEach(supplier => {
                        const option = document.createElement('option');
                        option.value = supplier.supplier_id;
                        option.textContent = supplier.supplier_name;
                        selectElement.appendChild(option);
                    });
                } else {
                    console.error('Fetch categories error:', result.message);
                }
            })
            .catch(error => console.error('Fetch categories error:', error));
    }

    document.addEventListener('DOMContentLoaded', loadSupplier);

    document.getElementById('supplier').addEventListener('change', function() {
        const supplierValueOption = this.value;
        if (supplierValueOption !== "default") {
            // Handle supplier change event (optional future logic)
        }
    });

    function checkCartIsEmptyOrNot() {
        const cartItems = document.querySelectorAll(".single-invoice-content");
        const confirmBtn = document.getElementById("confirmBtn");
        if (cartItems.length <= 0) {
            confirmBtn.style.backgroundColor = "#c0c0c0";
            confirmBtn.setAttribute("disabled", "true");
            confirmBtn.style.cursor = "default";
        } else {
            confirmBtn.style.backgroundColor = "#2dd2c0";
            confirmBtn.removeAttribute("disabled");
            confirmBtn.style.cursor = "pointer";
        }
    }

    document.addEventListener('DOMContentLoaded', checkCartIsEmptyOrNot);

    document.addEventListener("DOMContentLoaded", function () {
        const selectButtons = document.querySelectorAll(".select-button");
        const invoiceContent = document.querySelector(".invoice-content");
        const totalPriceElement = document.querySelector(".price");

        selectButtons.forEach(button => {
            button.addEventListener("click", function () {
                const row = this.closest("tr");
                const productId = row.querySelector("td:nth-child(1)").innerText;
                const productName = row.querySelector("td:nth-child(2)").innerText;
                const productDesc = row.querySelector("td:nth-child(3)").innerText;
                
                const existingProduct = invoiceContent.querySelector(`[data-product-id="${productId}"]`);
                let totalPrice = 0;

                if (existingProduct) {
                    let quantityInput = existingProduct.querySelector(".quantity-input");
                    quantityInput.value = parseInt(quantityInput.value) + 1;
                } else {
                    const invoiceItem = document.createElement("div");
                    invoiceItem.classList.add("single-invoice-content");
                    invoiceItem.setAttribute("data-product-id", productId);
                    invoiceItem.innerHTML = `
                        <div class="product-info-invoice">
                            <h2>${productName}</h2>
                            <h6>${productDesc}</h6>
                            <div class="inline-container">
                                <label for="${productId}">Giá nhập: </label>
                                <input 
                                    type="text" 
                                    class="bill-product-price" 
                                    id="${productId}"
                                    style="width: 100px;" 
                                    oninput="updatePrice(this)" 
                                    value="0">
                                <label for="${productId}">VNĐ</label>
                            </div>
                            <div class="single-invoice-btns">
                                <div class="quantity-container">
                                    <button class="quantity-btn" onclick="decreaseQuantity(this)">-</button>
                                    <input type="text" class="quantity-input" value="1" oninput="updateQuantity(this)">
                                    <button class="quantity-btn" onclick="increaseQuantity(this)">+</button>
                                </div>
                                <button class="remove" onclick="removeItem(this)">Remove</button>
                            </div>
                            <div class="percent-container">
                                <label for="percent-${productId}">Phần trăm: </label>
                                <input 
                                    type="text" 
                                    id="percent-${productId}" 
                                    class="percent-input" 
                                    value="0" 
                                    oninput="updatePrice(this)">
                                <label for="percent-${productId}">%</label>
                            </div>
                        </div>
                    `;
                    invoiceContent.appendChild(invoiceItem);
                }

                updateTotalPrice();
                checkCartIsEmptyOrNot();
            });
        });

        // Add event listener for "Apply All" button
        document.getElementById("applyAllBtn").addEventListener("click", function() {
            const globalPercent = document.getElementById("percent").value;
            if (!globalPercent.match(/^\d+$/) || parseFloat(globalPercent) < 0 || parseFloat(globalPercent) > 100) {
                alert("Vui lòng nhập phần trăm hợp lệ (0-100)");
                return;
            }

            const percentInputs = document.querySelectorAll(".percent-input");
            percentInputs.forEach(input => {
                input.value = globalPercent;
            });
            updateTotalPrice();
        });
    });

    function increaseQuantity(button) {
        let quantityInput = button.parentElement.querySelector(".quantity-input");
        quantityInput.value = parseInt(quantityInput.value) + 1;
        updateTotalPrice();
    }

    function decreaseQuantity(button) {
        let quantityInput = button.parentElement.querySelector(".quantity-input");
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
        updateTotalPrice();
    }

    function removeItem(button) {
        let invoiceItem = button.closest(".single-invoice-content");
        if (invoiceItem) {
            invoiceItem.remove();
            updateTotalPrice();
            checkCartIsEmptyOrNot();
        }
    }

    function updatePrice(input) {
        if (!input.value.match(/^\d+$/)) { 
            alert("Vui lòng nhập số");
            input.value = 0;
            updateTotalPrice();
            return;
        }

        let inputValue = parseFloat(input.value);
        if (inputValue < 0 || (input.classList.contains("percent-input") && inputValue > 100)) { 
            alert(input.classList.contains("percent-input") ? "Vui lòng nhập từ 0-100" : "Vui lòng nhập giá hợp lệ");
            input.value = 0;
            updateTotalPrice();
            return;
        }

        updateTotalPrice();
    }
    
    function updateQuantity(input) {
        if (!input.value.match(/^\d+$/)) { 
            alert("Vui lòng nhập số lượng là số nguyên");
            input.value = 1;
            updateTotalPrice();
            return;
        }
        updateTotalPrice();
    }

    function updatePercentage(input) {
        if (!input.value.match(/^\d+$/)) { 
            alert("Vui lòng nhập số");
            input.value = 0;
            updateTotalPrice();
            return;
        }

        let inputValue = parseFloat(input.value);
        if (inputValue < 0 || inputValue > 100) { 
            alert("Vui lòng nhập từ 0-100");
            input.value = 0;
            updateTotalPrice();
            return;
        }

        updateTotalPrice();
    }

    function updateTotalPrice() {
        let totalPrice = 0;
        const totalPriceElement = document.querySelector(".price");
        const singleInvoiceContent = document.querySelectorAll(".single-invoice-content");

        singleInvoiceContent.forEach(single => {
            let priceElement = single.querySelector(".bill-product-price");
            let quantityElement = single.querySelector(".quantity-input");
            let percentElement = single.querySelector(".percent-input");

            if (priceElement && quantityElement && percentElement) {
                let priceValue = parseFloat(priceElement.value) || 0;
                let quantityValue = parseInt(quantityElement.value) || 1;
                let percentValue = parseFloat(percentElement.value) || 0;

                totalPrice += priceValue * quantityValue * (1 + percentValue / 100);
            }
        });

        totalPriceElement.innerText = `${totalPrice.toLocaleString()} VND`;
    }

    document.getElementById("confirmBtn").addEventListener("click", function(event) {
        event.preventDefault();

        let supplierId = document.getElementById("supplier").value;
        let totalPrice = document.querySelector(".price").innerText.trim();
        let profitPercent = document.getElementById("percent").value;
        let userId = 1;
        let orderDate = new Date().toISOString().slice(0, 19).replace("T", " ");
        let statusId = 1;
        let isValid = true;

        totalPrice = totalPrice.replace(/[^\d.]/g, ''); // Result: "50000"
        totalPrice = parseFloat(totalPrice);          // Result: 50000

        let purchaseItems = [];
        document.querySelectorAll(".single-invoice-content").forEach(item => {
            let singlePrice = item.querySelector(".bill-product-price").value;
            if (singlePrice == "0") {
                alert("Vui lòng kiểm tra lại giá nhập của hóa đơn");
                isValid = false;
                return;
            }
            purchaseItems.push({
                product_id: item.dataset.productId,
                quantity: parseInt(item.querySelector(".quantity-input").value.trim()),
                price: parseFloat(item.querySelector(".bill-product-price").value),
                profit_percent: parseFloat(item.querySelector(".percent-input").value) // Include individual profit percent
            });
        });

        if (!isValid) return;

        if (supplierId == "default") {
            alert("Vui lòng chọn nhà xuất bản!");
            return;
        }

        if (purchaseItems.length === 0) {
            alert("Vui lòng thêm sản phẩm vào đơn hàng!");
            return;
        }

        let totalAmount = purchaseItems.reduce((sum, item) => sum + item.quantity, 0);
        console.log(totalAmount);
        let formData = new FormData();
        formData.append("supplier_id", supplierId);
        formData.append("user_id", userId);
        formData.append("order_date", orderDate);
        formData.append("total_price", totalPrice);
        formData.append("total_amount", totalAmount);
        formData.append("profit_percent", profitPercent); // Global profit percent (optional)
        formData.append("status_id", statusId);
        formData.append("purchase_items", JSON.stringify(purchaseItems));

        fetch("../../BackEnd/Model/nhaphang/xulinhaphang.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Đơn hàng đã được gửi thành công!");
                window.location.reload();
            } else {
                alert("Lỗi khi gửi đơn hàng: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
</script>


<style>
    body {
        background-color: #f8f9fa;
        display: flex;
        min-height: 100vh;
        padding: 20px;
    }

    .form {
        display: flex;
        width: 100%;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .import-block {
        width: 70%;
    }

    .form-title {
        text-align: center;
    }

    .form-title h1 {
        font-weight: bold;
        color: black;
    }

    .form-title h6 {
        color: #80858a;
    }

    .form-content {
        background-color: white;
        border-radius: 20px;
        width: 100%;
        margin: 10px;
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
    }

    .product-block {
        display: flex;
        gap: 10px;
        cursor: pointer;
        width: calc(100% / 3 - 10px); /* 4 columns per row */
        align-items: center;
        padding: 5px;
    }

    .invoice-content {
        background-color: white;
        border-radius: 20px;
        width: 100%;
        margin: 10px;
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
        max-height: 300px; /* Ensure there's enough content to scroll */
        overflow-y: auto; /* Enables vertical scrolling */
    }

    .invoice-block {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%; /* 4 columns per row */
        align-items: center;
        padding: 5px;
    }

    .product-info-invoice-block {
        width: 100%;
        display: flex;
    }

    .product-block:hover {
        background-color: #e4e4e4;
    }

    .product-image {
        margin-bottom: 15px;
    }

    .product-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        font-weight: bold;
        width: 100%;
        justify-content: center;
    }

    .invoice-content::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    .invoice-content::-webkit-scrollbar-track {
    background: #f1f1f1; 
    }
    
    /* Handle */
    .invoice-content::-webkit-scrollbar-thumb {
    background: #888; 
    }

    /* Handle on hover */
    .invoice-content::-webkit-scrollbar-thumb:hover {
    background: #555; 
    }

    .single-invoice-content {
        display: flex;  
        gap: 10px;
        cursor: pointer;
    }

    .product-image-invoice {
        margin-bottom: 15px;
        width: 79px;
        height: 122px;
    }

    .product-info-invoice {
        display: flex;
        flex-direction: column;
        justify-content: center;
        font-weight: bold;
        width: 100%;
    }

    /* .product-info h4 {
    } */

    .product-info h6 {
        color: #9e9790;
        font-size: 12px;
    }

    .product-block img {
        height: 122px;
        width: 100px;
    }

    .bill-block {
        display: flex;
        flex-direction: column;
        width: 30%;
        border-left: 1px black solid;
    }

    .bill-title {
        text-align: center;
        font-weight: bold;
        color: black;   
    }

    #supplier {
        border: 1px solid black;
    }

    table tr td{
        text-align: center;
    }
    
    #percent {
        background-color: #e2e2e2;
    }

    #percent::placeholder { color: #919191; }

    #confirmBtn {
        margin-top: 10px;
        border: 1px black solid;
        padding: 5px 15px;
        color: white;
        font-weight: bold;
    }

    .cart-info {
    }

    .quantity-container {
        display: flex;
        align-items: center;
        border: 1px solid #000;
        width: 120px;
        justify-content: space-between;
        padding: 5px;
        border-radius: 5px;
    }

    .quantity-btn {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        width: 30px;
    }

    .quantity-input {
        width: 40px;
        text-align: center;
        font-size: 16px;
        border: none;
        outline: none;
    }

    .percent-container {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px; /* Adds spacing between quantity buttons and percentage input */
    }

    #applyAllBtn {
        padding: 5px 10px;
        background-color: #4CAF50; /* Green color, adjust as needed */
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    #applyAllBtn:hover {
        background-color: #45a049; /* Darker green on hover */
    }

    .percent-input {
        width: 40px;
        text-align: center;
        border: 1px solid #000;
        border-radius: 3px;
        padding: 2px;
    }

    .single-invoice-btns {
        display: flex;
        gap: 5px;
    }

    .inline-container {
        display: flex;
        align-items: center; /* Aligns input and label */
        gap: 5px; /* Adjust spacing */
    }
</style>