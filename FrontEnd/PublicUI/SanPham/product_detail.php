<?php
include ('../../../BackEnd/Config/config.php');


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Sản phẩm không tồn tại!");
}

$product_id = intval($_GET['id']);


$sql = "SELECT * FROM product WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: aliceblue;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .product-image {
            width: 100%;
            height: auto;
            border-radius: 10px;
            background: #f1f1f1;
            padding: 10px;
        }
        .price {
            font-size: 24px;
            font-weight: bold;
            color: #e44d26;
        }
        .btn-buy {
            background-color: #ff5722;
            border-color: #ff5722;
            transition: 0.3s;
            font-size: 16px;
            padding: 10px 20px;
        }
        .btn-buy:hover {
            background-color: #d84315;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="product-image" alt="Hình ảnh sản phẩm">
            </div>
            <div class="col-md-6">
                <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                <p class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
                <p><strong>Kho hàng:</strong> <?php echo $product['stock_quantity']; ?> sản phẩm</p>
                <a href="cart.php?add=<?php echo $product['product_id']; ?>" class="btn btn-buy">Thêm vào giỏ hàng</a>
            </div>
        </div>
    </div>
</body>
</html>
