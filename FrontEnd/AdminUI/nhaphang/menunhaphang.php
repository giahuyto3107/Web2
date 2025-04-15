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
/* Định dạng body */
body {
    background-color: #f8f9fa; /* Giữ nguyên màu nền */
    display: flex;
    min-height: 100vh;
    padding: 1.2rem; /* Đồng bộ với padding trong data-table.css */
}

/* Định dạng container chính */
.form {
    display: flex;
    width: 100%;
    background: hsl(0 0% 100%); /* Đồng bộ màu nền trắng */
    padding: 1.5rem; /* Đồng bộ padding */
    border-radius: 0.75rem; /* Đồng bộ border-radius */
    box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px; /* Đồng bộ box-shadow */
}

/* Định dạng import-block */
.import-block {
    width: 70%;
    padding-right: 1rem; /* Thêm padding để tránh sát mép */
}

/* Định dạng tiêu đề form-title */
.form-title {
    text-align: center;
    margin-bottom: 1.5rem; /* Đồng bộ margin với heading trong data-table.css */
}

.form-title h1 {
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    color: var(--clr-primary-300); /* Đồng bộ màu với modal-header h2 */
    font-size: 2rem; /* Đồng bộ font-size */
}

.form-title h6 {
    color: #999999; /* Đồng bộ màu chữ phụ */
    font-size: 0.875rem; /* Đồng bộ font-size nhỏ */
}

/* Định dạng bảng trong form-content */
.form-content {
    background-color: hsl(0 0% 100%);
    border-radius: 0.75rem; /* Đồng bộ border-radius */
    width: 100%;
    margin: 0.5rem 0; /* Đồng bộ margin */
    padding: 1rem; /* Đồng bộ padding */
    overflow-x: auto; /* Thêm thanh cuộn ngang nếu cần */
}

.form-content table {
    width: 100%;
    border-collapse: collapse;
}

.form-content th,
.form-content td {
    padding: 0.75rem; /* Đồng bộ padding với modal-table */
    border-bottom: 0.094rem solid #ddd; /* Đồng bộ border-bottom */
    text-align: left; /* Căn trái thay vì center để đồng bộ với modal-table */
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    max-width: 12.5rem; /* Đồng bộ max-width */
}

.form-content th {
    background-color: white;
    color: var(--fs-table-header); /* Đồng bộ màu chữ tiêu đề bảng */
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
}

.form-content tr:hover {
    background-color: #f1f1f1; /* Đồng bộ hiệu ứng hover */
}

/* Định dạng nút "Chọn" */
.select-button {
    padding: 0.5rem 1rem; /* Đồng bộ padding với nút nhỏ */
    border-radius: 100vmax; /* Đồng bộ border-radius */
    border: none;
    background-color: var(--clr-primary-300); /* Đồng bộ màu nút */
    color: white;
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    cursor: pointer;
    transition: opacity 0.3s ease; /* Đồng bộ hiệu ứng */
}

.select-button:hover {
    opacity: 0.8; /* Đồng bộ hiệu ứng hover */
}

/* Định dạng bill-block */
.bill-block {
    width: 30%;
    border-left: 1px solid #999999; /* Đồng bộ màu border */
    padding-left: 1rem; /* Thêm padding để tránh sát mép */
}

/* Định dạng tiêu đề bill-title */
.bill-title {
    text-align: center;
    margin-bottom: 1.5rem; /* Đồng bộ margin */
}

.bill-title h3 {
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    color: var(--clr-primary-300); /* Đồng bộ màu */
    font-size: 1.5rem; /* Đồng bộ font-size với heading */
}

.bill-title label {
    display: block;
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    color: var(--clr-neutral-900); /* Đồng bộ màu */
    margin-bottom: 0.5rem; /* Đồng bộ margin */
}

/* Định dạng select (combobox) */
#supplier {
    width: 100%;
    padding: 0.6rem; /* Đồng bộ padding */
    border: 1px solid #ccc; /* Đồng bộ border */
    border-radius: 0.35rem; /* Đồng bộ border-radius */
    background-color: hsl(0 0% 100%); /* Đồng bộ background */
    color: #333; /* Đồng bộ màu chữ */
    cursor: pointer;
    transition: border-color 0.15s ease, box-shadow 0.15s ease; /* Đồng bộ hiệu ứng */
    appearance: none; /* Xóa kiểu dáng mặc định */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M8 12l-4-4h8l-4 4z'/%3E%3C/svg%3E"); /* Thêm icon mũi tên */
    background-repeat: no-repeat;
    background-position: right 0.6rem center;
    background-size: 1rem;
}

#supplier:hover {
    border-color: var(--clr-primary-300); /* Đồng bộ hiệu ứng hover */
}

#supplier:focus {
    outline: none;
    border-color: var(--clr-primary-300); /* Đồng bộ hiệu ứng focus */
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25); /* Đồng bộ box-shadow */
}

/* Định dạng invoice-block */
.invoice-block {
    display: flex;
    flex-direction: column;
    gap: 1rem; /* Đồng bộ gap */
    width: 100%;
}

/* Định dạng invoice-content */
.invoice-content {
    background-color: hsl(0 0% 100%);
    border-radius: 0.75rem; /* Đồng bộ border-radius */
    width: 100%;
    margin: 0.5rem 0; /* Đồng bộ margin */
    padding: 1rem; /* Đồng bộ padding */
    max-height: 300px; /* Giữ max-height */
    overflow-y: auto; /* Thanh cuộn dọc */
}

/* Định dạng thanh cuộn */
.invoice-content::-webkit-scrollbar {
    width: 0.375rem; /* Đồng bộ với data-table.css */
}

.invoice-content::-webkit-scrollbar-track {
    background: #f1f1f1; /* Đồng bộ màu track */
    border-radius: 1.875rem;
}

.invoice-content::-webkit-scrollbar-thumb {
    background: #cecece; /* Đồng bộ màu thumb */
    border-radius: 1.875rem;
}

.invoice-content::-webkit-scrollbar-thumb:hover {
    background: #555; /* Đồng bộ màu hover */
}

/* Định dạng single-invoice-content */
.single-invoice-content {
    display: flex;
    flex-direction: column; /* Sửa thành column để đồng bộ layout */
    gap: 0.5rem; /* Đồng bộ gap */
    padding: 0.5rem;
    border-bottom: 0.094rem solid #ddd; /* Thêm border-bottom để phân cách */
}

/* Định dạng product-info-invoice */
.product-info-invoice {
    display: flex;
    flex-direction: column;
    gap: 0.25rem; /* Đồng bộ gap */
}

.product-info-invoice h2 {
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    font-size: 1rem; /* Font-size nhỏ hơn để phù hợp */
    color: var(--clr-neutral-900); /* Đồng bộ màu */
}

.product-info-invoice h6 {
    font-size: 0.875rem; /* Đồng bộ font-size */
    color: #999999; /* Đồng bộ màu phụ */
}

/* Định dạng inline-container */
.inline-container {
    display: flex;
    align-items: center;
    gap: 0.5rem; /* Đồng bộ gap */
}

.inline-container label {
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    color: var(--clr-neutral-900); /* Đồng bộ màu */
}

/* Định dạng input giá nhập */
.bill-product-price {
    width: 100px;
    padding: 0.6rem; /* Đồng bộ padding */
    border: 1px solid #ccc; /* Đồng bộ border */
    border-radius: 0.35rem; /* Đồng bộ border-radius */
    background-color: hsl(0 0% 100%); /* Đồng bộ background */
    color: #333; /* Đồng bộ màu chữ */
    transition: border-color 0.15s ease; /* Đồng bộ hiệu ứng */
}

.bill-product-price:focus {
    outline: none;
    border-color: var(--clr-primary-300); /* Đồng bộ hiệu ứng focus */
}

/* Định dạng percent-container */
.percent-container {
    display: flex;
    align-items: center;
    gap: 0.5rem; /* Đồng bộ gap */
    margin-top: 0.5rem; /* Đồng bộ margin */
}

.percent-container label {
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    color: var(--clr-neutral-900); /* Đồng bộ màu */
}

/* Định dạng percent-input */
.percent-input {
    width: 40px;
    padding: 0.6rem; /* Đồng bộ padding */
    border: 1px solid #ccc; /* Đồng bộ border */
    border-radius: 0.35rem; /* Đồng bộ border-radius */
    background-color: hsl(0 0% 100%); /* Đồng bộ background */
    color: #333; /* Đồng bộ màu chữ */
    text-align: center;
    transition: border-color 0.15s ease; /* Đồng bộ hiệu ứng */
}

.percent-input:focus {
    outline: none;
    border-color: var(--clr-primary-300); /* Đồng bộ hiệu ứng focus */
}

/* Định dạng single-invoice-btns */
.single-invoice-btns {
    display: flex;
    gap: 0.5rem; /* Đồng bộ gap */
    align-items: center;
}

/* Định dạng quantity-container */
.quantity-container {
    display: flex;
    align-items: center;
    border: 1px solid #ccc; /* Đồng bộ border */
    border-radius: 0.35rem; /* Đồng bộ border-radius */
    width: 120px;
    justify-content: space-between;
    padding: 0.25rem; /* Giảm padding để gọn hơn */
}

/* Định dạng nút quantity-btn */
.quantity-btn {
    background: none;
    border: none;
    font-size: 1rem; /* Giảm font-size để gọn */
    color: var(--clr-primary-300); /* Đồng bộ màu */
    cursor: pointer;
    width: 30px;
    text-align: center;
    transition: opacity 0.3s ease; /* Đồng bộ hiệu ứng */
}

.quantity-btn:hover {
    opacity: 0.8; /* Đồng bộ hiệu ứng hover */
}

/* Định dạng quantity-input */
.quantity-input {
    width: 40px;
    text-align: center;
    font-size: 0.875rem; /* Đồng bộ font-size */
    border: none;
    outline: none;
    background-color: hsl(0 0% 100%); /* Đồng bộ background */
    color: #333; /* Đồng bộ màu chữ */
}

/* Định dạng nút Remove */
.remove {
    padding: 0.5rem 1rem; /* Đồng bộ padding */
    border-radius: 100vmax; /* Đồng bộ border-radius */
    border: none;
    background-color: #dc3545; /* Đồng bộ màu nút hủy */
    color: white;
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    cursor: pointer;
    transition: opacity 0.3s ease; /* Đồng bộ hiệu ứng */
}

.remove:hover {
    opacity: 0.8; /* Đồng bộ hiệu ứng hover */
}

/* Định dạng cart-info */
.cart-info {
    display: flex;
    flex-direction: column;
    gap: 1rem; /* Đồng bộ gap */
    padding: 1rem; /* Thêm padding để đẹp hơn */
}

/* Định dạng priceContainer */
.priceContainer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.priceContainer h2 {
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    font-size: 1.1rem; /* Font-size nhỏ hơn để phù hợp */
    color: var(--clr-neutral-900); /* Đồng bộ màu */
}

.priceContainer .price {
    color: #65e4dd; /* Giữ màu hiện tại */
}

/* Định dạng percentContainer */
.percentContainer {
    display: flex;
    align-items: center;
    gap: 0.5rem; /* Đồng bộ gap */
}

/* Định dạng input #percent */
#percent {
    width: 80px; /* Tăng chiều rộng để dễ nhập */
    padding: 0.6rem; /* Đồng bộ padding */
    border: 1px solid #ccc; /* Đồng bộ border */
    border-radius: 0.35rem; /* Đồng bộ border-radius */
    background-color: hsl(0 0% 100%); /* Đồng bộ background */
    color: #333; /* Đồng bộ màu chữ */
    transition: border-color 0.15s ease; /* Đồng bộ hiệu ứng */
}

#percent::placeholder {
    color: #999999; /* Đồng bộ màu placeholder */
}

#percent:focus {
    outline: none;
    border-color: var(--clr-primary-300); /* Đồng bộ hiệu ứng focus */
}

/* Định dạng nút Apply All */
#applyAllBtn {
    padding: 0.5rem 1rem; /* Đồng bộ padding */
    border-radius: 100vmax; /* Đồng bộ border-radius */
    border: none;
    background-color: #4caf50; /* Đồng bộ màu nút thành công */
    color: white;
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    cursor: pointer;
    transition: background-color 0.3s ease; /* Đồng bộ hiệu ứng */
}

#applyAllBtn:hover {
    background-color: #45a049; /* Đồng bộ hiệu ứng hover */
}

/* Định dạng nút Đặt hàng */
#confirmBtn {
    margin-top: 1rem; /* Đồng bộ margin */
    padding: 0.625rem 1.5rem; /* Đồng bộ padding */
    border-radius: 100vmax; /* Đồng bộ border-radius */
    border: none;
    background-color: #2dd2c0; /* Giữ màu hiện tại */
    color: white;
    font-weight: var(--fw-bold); /* Đồng bộ font-weight */
    cursor: pointer;
    transition: opacity 0.3s ease; /* Đồng bộ hiệu ứng */
}

#confirmBtn[disabled] {
    background-color: #c0c0c0; /* Giữ màu khi disabled */
    cursor: default;
}

#confirmBtn:not([disabled]):hover {
    opacity: 0.8; /* Đồng bộ hiệu ứng hover */
}

/* Responsive */
@media (max-width: 40em) {
    .form {
        flex-direction: column; /* Chuyển thành cột trên màn hình nhỏ */
        padding: 1rem; /* Giảm padding */
    }

    .import-block,
    .bill-block {
        width: 100%; /* Full-width trên màn hình nhỏ */
        padding: 0; /* Xóa padding để tránh sát mép */
    }

    .bill-block {
        border-left: none; /* Xóa border-left */
        border-top: 1px solid #999999; /* Thêm border-top */
        padding-top: 1rem; /* Thêm padding-top */
    }

    .form-content th,
    .form-content td {
        padding: 0.5rem; /* Giảm padding trên màn hình nhỏ */
        max-width: none; /* Xóa giới hạn chiều rộng */
    }
}

@media (max-width: 28em) {
    .form {
        padding: 0.5rem; /* Giảm padding thêm */
    }

    .form-title h1 {
        font-size: 1.5rem; /* Giảm font-size */
    }

    .bill-title h3 {
        font-size: 1.2rem; /* Giảm font-size */
    }

    #confirmBtn,
    #applyAllBtn,
    .select-button,
    .remove {
        padding: 0.5rem 1rem; /* Giảm padding */
    }
}
</style>