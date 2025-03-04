<?php
include ('../../../BackEnd/Config/config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Sản phẩm không tồn tại!");
}

$product_id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$sql = "SELECT product_id, product_name, product_description, price, stock_quantity, category_id, status_id, image_url FROM product WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// Lấy thông tin danh mục sản phẩm
$sqlCategory = "SELECT category_name, category_description FROM category WHERE category_id = ?";
$stmt_category = $conn->prepare($sqlCategory);
$stmt_category->bind_param("i", $product['category_id']);
$stmt_category->execute();
$result_category = $stmt_category->get_result();
$category = $result_category->fetch_assoc();

// Lấy đánh giá sản phẩm và trung bình rating
$sqlReview = "SELECT user_id, rating, review_text FROM review WHERE product_id = ?";
$stmt_review = $conn->prepare($sqlReview);
$stmt_review->bind_param("i", $product_id);
$stmt_review->execute();
$result_review = $stmt_review->get_result();

// Tính điểm đánh giá trung bình
$sqlAvgRating = "SELECT AVG(rating) AS avg_rating FROM review WHERE product_id = ?";
$stmt_avg_rating = $conn->prepare($sqlAvgRating);
$stmt_avg_rating->bind_param("i", $product_id);
$stmt_avg_rating->execute();
$result_avg_rating = $stmt_avg_rating->get_result();
$avg_rating = $result_avg_rating->fetch_assoc()['avg_rating'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Hiệu ứng marquee */
        .marquee {
            white-space: nowrap;
            overflow: hidden;
            box-sizing: border-box;
        }
        .marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo htmlspecialchars($category['category_name']); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_name']); ?></li>
            </ol>
        </nav>

        <!-- Chi tiết sản phẩm -->
        <div class="row">
            <div class="col-md-6">
                <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            </div>
            <div class="col-md-6">
                <h1 class="mb-3"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <p class="text-muted">Mã SP: <?php echo $product['product_id']; ?> | Số lượng có sẵn: <?php echo $product['stock_quantity']; ?></p>
                <h3 class="text-danger"><?php echo number_format($product['price'], 0, ',', '.') . ' ₫'; ?></h3>
                <p class="text-success">Giá đã bao gồm VAT</p>
                <form action="cart.php" method="POST" class="mb-4">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Thêm vào giỏ hàng</button>
                </form>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông số sản phẩm</h5>
                        <p class="card-text"><?php echo htmlspecialchars($product['product_description']); ?></p>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin danh mục</h5>
                        <p><strong>Danh mục:</strong> <?php echo htmlspecialchars($category['category_name']); ?></p>
                        <p><strong>Mô tả danh mục:</strong> <?php echo htmlspecialchars($category['category_description']); ?></p>
                        <p><strong>Trạng thái:</strong> <?php echo ($product['status_id'] == 1) ? "Còn hàng" : "Hết hàng"; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đánh giá sản phẩm -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h2>Đánh giá sản phẩm</h2>
                <p>Đánh giá trung bình: <?php echo number_format($avg_rating, 1); ?> / 5 ⭐</p>
                <?php while ($review = $result_review->fetch_assoc()) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $review['user_id']; ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <p class="card-text"><small class="text-muted">Rating: <?php echo str_repeat("⭐", $review['rating']); ?></small></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Gợi ý sản phẩm -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h2>Sản phẩm gợi ý</h2>
                <div class="marquee bg-light p-3 rounded">
                    <span>
                        <?php
                        // Lấy danh sách sản phẩm gợi ý (ví dụ: 5 sản phẩm ngẫu nhiên)
                        $sqlSuggested = "SELECT product_name, image_url FROM product ORDER BY RAND() LIMIT 5";
                        $result_suggested = $conn->query($sqlSuggested);
                        while ($suggested = $result_suggested->fetch_assoc()) {
                            echo '<img src="../../../BackEnd/Uploads/Product Picture/' . htmlspecialchars($suggested['image_url']) . '" class="img-thumbnail mx-2" style="width: 100px; height: 100px;" alt="' . htmlspecialchars($suggested['product_name']) . '">';
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>