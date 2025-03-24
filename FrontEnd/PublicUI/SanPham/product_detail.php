<?php
include ('../../../BackEnd/Config/config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Sản phẩm không tồn tại!");
}

$product_id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$sql = "SELECT product_id, product_name, product_description, price, stock_quantity, 
               status_id, image_url, created_at, updated_at 
        FROM product 
        WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// Lấy tất cả danh mục của sản phẩm
$sqlCategories = "SELECT c.category_id, c.category_name, c.category_description 
                  FROM category c 
                  INNER JOIN product_category pc ON c.category_id = pc.category_id 
                  WHERE pc.product_id = ?";
$stmt_categories = $conn->prepare($sqlCategories);
$stmt_categories->bind_param("i", $product_id);
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();
$categories = [];
while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row;
}

// Lấy đánh giá sản phẩm và trung bình rating (không thay đổi)
$sqlReview = "SELECT user_id, rating, review_text, feedback 
              FROM review 
              WHERE product_id = ? AND status_id = 1";
$stmt_review = $conn->prepare($sqlReview);
$stmt_review->bind_param("i", $product_id);
$stmt_review->execute();
$result_review = $stmt_review->get_result();

// Tính điểm đánh giá trung bình (không thay đổi)
$sqlAvgRating = "SELECT AVG(rating) AS avg_rating 
                 FROM review 
                 WHERE product_id = ?";
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
    <title><?php echo htmlspecialchars($product['product_name']); ?> | Chi tiết sản phẩm</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    .container-fluid {
        max-width: 1400px;
        padding: 0 20px;
    }

    /* Product Detail Section */
    .product-detail {
        background: #ffffff;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .product-detail:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        width: 100%;
        height: auto;
        border-radius: 12px;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .product-image:hover {
        transform: scale(1.03);
    }

    .product-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
        margin-bottom: 15px;
    }

    .price {
        font-size: 2rem;
        font-weight: 600;
        color: #e74c3c;
        margin: 15px 0;
    }

    .stock-info {
        font-size: 1rem;
        color: #27ae60;
        font-weight: 500;
    }

    .btn-add-to-cart {
        background: linear-gradient(135deg, #f1c40f, #e67e22);
        border: none;
        padding: 12px 25px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #fff;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-add-to-cart:hover {
        background: linear-gradient(135deg, #e67e22, #d35400);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(230, 126, 34, 0.4);
    }

    .card {
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border-radius: 10px;
    }

    .card-body {
        padding: 20px;
    }

    .card-title {
        font-size: 1.25rem;
        color: #34495e;
        font-weight: 600;
    }

    .quantity-wrapper {
        display: flex;
        align-items: center;
    }

    .quantity-wrapper label {
        font-size: 1rem;
        color: #34495e;
        font-weight: 500;
    }

    .input-group {
        border-radius: 8px;
        overflow: hidden;
    }


    /* Review Section */
    .review-section {
        background: #ffffff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        margin-top: 40px;
    }

    .review-section h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .review-item {
        padding: 20px 0;
        border-bottom: 1px solid #f1f3f5;
        transition: background 0.2s ease;
    }

    .review-item:hover {
        background: #f9fafa;
    }

    .profile-picture {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
        border: 2px solid #ecf0f1;
    }

    .review-content {
        width: 100%;
    }

    .review-text {
        font-size: 1rem;
        color: #34495e;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .admin-reply {
        background: #f1f8ff;
        padding: 15px;
        border-radius: 8px;
        margin-left: 60px; /* Chừa khoảng cách với avatar */
        border-left: 4px solid #3498db;
    }

    .admin-reply p {
        margin-bottom: 0;
    }

    .admin-reply .feedback-text {
        font-size: 0.95rem;
        color: #2c3e50;
        line-height: 1.4;
    }

    /* Suggested Products Section */
    .suggested-products {
        position: sticky;
        top: 20px;
        background: #ffffff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        height: calc(100vh - 40px);
        overflow: hidden;
    }

    .suggested-products h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .marquee-vertical {
        height: 100%;
        overflow: hidden;
    }

    .marquee-content {
        animation: marquee-vertical 25s linear infinite;
    }

    .marquee-content:hover {
        animation-play-state: paused;
    }

    .product-item {
        margin-bottom: 25px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .product-item:hover {
        transform: translateY(-5px);
    }

    .marquee-vertical img {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .marquee-vertical .product-name {
        font-size: 0.95rem;
        color: #34495e;
        font-weight: 500;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @keyframes marquee-vertical {
        0% { transform: translateY(0); }
        100% { transform: translateY(-100%); }
    }

    /* Responsive Design */
    @media (max-width: 991px) {
        .product-title {
            font-size: 1.8rem;
        }
        .price {
            font-size: 1.6rem;
        }
        .suggested-products {
            position: static;
            height: auto;
            margin-top: 30px;
        }
    }

    @media (max-width: 767px) {
        .product-detail {
            padding: 20px;
        }
        .review-section {
            padding: 20px;
        }
    }
</style>
</head>
<body>
    <div class="container-fluid my-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="http://localhost/Web2/FrontEnd/PublicUI/SanPham/danhsachSP.php">Trang chủ</a></li>

                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Chi tiết sản phẩm -->
            <div class="col-lg-10">
                <div class="product-detail">
                    <div class="row">
                        <div class="col-md-5">
                            <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url']); ?>" class="product-image img-fluid" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                        <div class="col-md-7">
                            <h1 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                            <p class="text-muted mb-1">Mã SP: <?php echo $product['product_id']; ?></p>
                            <p class="stock-info">Số lượng: <?php echo $product['stock_quantity']; ?> sản phẩm trong kho</p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="price"><?php echo number_format($product['price'], 0, ',', '.') . ' ₫'; ?></p>
                                <div class="quantity-wrapper">
                                    <label for="quantity" class="me-2">Số lượng:</label>
                                    <div class="input-group" style="width: 120px;">
                                        <input type="number" id="quantity" name="quantity" class="form-control text-center" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-muted">Giá đã bao gồm VAT</p>
                            <form action="cart.php" method="POST" class="mb-4">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <input type="hidden" name="quantity" id="hidden-quantity" value="1">
                                <button type="submit" class="btn btn-add-to-cart w-100"><i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng</button>
                            </form>
                            
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Giới thiệu về sách</h5>
                                    <p><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Thông tin sản phẩm</h5>
                                    <p><strong>Danh mục:</strong> 
                                        <?php 
                                        if (!empty($categories)) {
                                            $category_names = array_map(function($cat) {
                                                return htmlspecialchars($cat['category_name']);
                                            }, $categories);
                                            echo implode(', ', $category_names);
                                        } else {
                                            echo "Không có danh mục";
                                        }
                                        ?>
                                    </p>
                                    
                                    <p><strong>Trạng thái:</strong> <?php echo ($product['status_id'] == 1) ? "Còn hàng" : "Hết hàng"; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Đánh giá sản phẩm -->
                <div class="review-section">
                    <h2>Đánh giá sản phẩm</h2>
                    <p>Đánh giá trung bình: <strong><?php echo number_format($avg_rating, 1); ?> / 5</strong> <span class="text-warning"><?php echo str_repeat("★", round($avg_rating)); ?></span></p>
                    <?php while ($review = $result_review->fetch_assoc()) { ?>
                        <div class="review-item">
                            <div class="d-flex align-items-start">
                                <?php
                                $sqlUser = "SELECT full_name, profile_picture FROM user WHERE account_id = ?";
                                $stmt_user = $conn->prepare($sqlUser);
                                $stmt_user->bind_param("i", $review['user_id']);
                                $stmt_user->execute();
                                $result_user = $stmt_user->get_result();
                                $user = $result_user->fetch_assoc();
                                ?>
                                <img src="../../../BackEnd/Uploads/Profile Picture/<?php echo htmlspecialchars($user['profile_picture']); ?>" class="profile-picture" alt="<?php echo htmlspecialchars($user['full_name']); ?>">
                                <div class="review-content flex-grow-1">
                                    <div class="">
                                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($user['full_name']); ?></p>
                                        <p class="text-warning mb-0"><?php echo str_repeat("★", $review['rating']); ?></p>
                                    </div>
                                    <p class="review-text mt-2"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                    <?php if (!empty($review['feedback'])) { ?>
                                        <div class="admin-reply mt-3">
                                            <p class="mb-0"><strong>Phản hồi từ Admin:</strong></p>
                                            <p class="feedback-text"><?php echo htmlspecialchars($review['feedback']); ?></p>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Sản phẩm gợi ý -->
            <div class="col-lg-2">
                <div class="suggested-products">
                    <h3 class="text-center mb-3">Có thể</h3>
                    <h3 class="text-center mb-3">Bạn sẽ thích</h3>
                    <div class="marquee-vertical">
                        <div class="marquee-content">
                            <?php
                            // Lấy danh sách category_id của sản phẩm hiện tại
                            $category_ids = array_column($categories, 'category_id');
                            if (!empty($category_ids)) {
                                $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
                                $sqlSuggested = "SELECT p.product_id, p.product_name, p.image_url 
                                                FROM product p 
                                                INNER JOIN product_category pc ON p.product_id = pc.product_id 
                                                WHERE pc.category_id IN ($placeholders) 
                                                AND p.product_id != ? 
                                                ORDER BY RAND()"; 
                                                // LIMIT 5";
                                $stmt_suggested = $conn->prepare($sqlSuggested);
                                $params = array_merge($category_ids, [$product_id]);
                                $types = str_repeat('i', count($category_ids)) . 'i';
                                $stmt_suggested->bind_param($types, ...$params);
                                $stmt_suggested->execute();
                                $result_suggested = $stmt_suggested->get_result();

                                while ($suggested = $result_suggested->fetch_assoc()) {
                                    echo '<div class="product-item">';
                                    echo '<a href="product_detail.php?id=' . htmlspecialchars($suggested['product_id']) . '">';
                                    echo '<img src="../../../BackEnd/Uploads/Product Picture/' . htmlspecialchars($suggested['image_url']) . '" alt="' . htmlspecialchars($suggested['product_name']) . '">';
                                    echo '<span class="product-name">' . htmlspecialchars($suggested['product_name']) . '</span>';
                                    echo '</a>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>