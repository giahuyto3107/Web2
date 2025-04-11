<?php
include ('../../../BackEnd/Config/config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Sản phẩm không tồn tại!");
}

$product_id = intval($_GET['id']);

$sql = "SELECT product_id, product_name, product_description, price, stock_quantity, 
               status_id, image_url, created_at, updated_at 
        FROM product 
        WHERE product_id = ? and product.stock_quantity>0 and product.status_id=1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

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

$sqlReview = "SELECT user_id, rating, review_text, feedback 
              FROM review 
              WHERE product_id = ? AND status_id = 1";
$stmt_review = $conn->prepare($sqlReview);
$stmt_review->bind_param("i", $product_id);
$stmt_review->execute();
$result_review = $stmt_review->get_result();

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
    <title><?php echo htmlspecialchars($product['product_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

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

        body {
            background: #ffffff;
            /* padding: 50px; */
            min-height: 100vh;
            color: #1a1a1a;
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Breadcrumb */
        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 300;
            color: #666;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "—";
            color: #666;
        }

        .breadcrumb-item.active {
            color: #1a1a1a;
            font-weight: 400;
        }

        /* Product Detail Section */
        .product-detail {
            padding: 40px 0;
        }

        .product-image {
            /* width: 100%;
            height: 400px; */
            object-fit: cover;
            border: 1px solid #e0e0e0;
        }

        .product-title {
            font-size: 1.8rem;
            font-weight: 400;
            color: #1a1a1a;
            letter-spacing: 1px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .price {
            font-size: 1.6rem;
            font-weight: 400;
            color: #d4af37;
            margin: 15px 0;
        }

        .stock-info {
            font-size: 0.9rem;
            font-weight: 300;
            color: #666;
        }

        .quantity-wrapper {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .quantity-wrapper label {
            font-size: 0.9rem;
            font-weight: 400;
            color: #1a1a1a;
            margin-right: 15px;
        }

        .input-group {
            width: 120px;
            border: 1px solid #e0e0e0;
        }

        .form-control {
            border: none;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 300;
            color: #1a1a1a;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #d4af37;
        }

        .btn-add-to-cart {
            background: #1a1a1a;
            border: none;
            padding: 10px 25px;
            font-size: 0.9rem;
            font-weight: 400;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s ease;
            width: 100%;
        }

        .btn-add-to-cart:hover {
            background: #333;
            color: #fff;
        }

        .card {
            border: none;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
            margin-top: 20px;
        }

        .card-body {
            padding: 0;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 400;
            color: #1a1a1a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .card p {
            font-size: 0.9rem;
            font-weight: 300;
            color: #666;
            line-height: 1.6;
        }

        /* Review Section */
        .review-section {
            padding: 40px 0;
            border-top: 1px solid #e0e0e0;
            margin-top: 40px;
        }

        .review-section h2 {
            font-size: 1.4rem;
            font-weight: 400;
            color: #1a1a1a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .review-section > p {
            font-size: 0.9rem;
            font-weight: 300;
            color: #666;
        }

        .review-section .text-warning {
            color: #d4af37;
        }

        .review-item {
            padding: 20px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .profile-picture {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 1px solid #e0e0e0;
        }

        .review-content .fw-bold {
            font-size: 0.9rem;
            font-weight: 400;
            color: #1a1a1a;
        }

        .review-text {
            font-size: 0.9rem;
            font-weight: 300;
            color: #666;
            margin-top: 5px;
        }

        .admin-reply {
            padding: 15px;
            margin-left: 60px;
            margin-top: 10px;
            border-left: 2px solid #d4af37;
        }

        .admin-reply p {
            font-size: 0.85rem;
            font-weight: 300;
            color: #666;
        }

        /* Suggested Products Section */
        .suggested-products {
            padding: 20px 0;
        }

        .suggested-products h3 {
            font-size: 1rem;
            font-weight: 400;
            color: #1a1a1a;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 20px;
        }

        .marquee-vertical {
            height: calc(100vh - 100px);
            overflow: hidden;
            /* display: flex; */
        }

        .marquee-content {
            animation: marquee-vertical 25s linear infinite;
        }

        .marquee-content:hover {
            animation-play-state: paused;
        }

        .product-item {
            margin-bottom: 20px;
            text-align: center;
            
        }

        .marquee-vertical img {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border: 1px solid #e0e0e0;
            display: block;
            margin: 0 auto;
        }

        .marquee-vertical .product-name {
            font-size: 0.85rem;
            font-weight: 300;
            color: #666;
            margin-top: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        #stock-warning .alert {
            font-size: 0.85rem;
            padding: 8px 12px;
            margin-top: 10px;
            border-radius: 0.375rem;
        }
        #toast {
            position: fixed;
            right: 20px;
            top: 100px; /* Đã dịch xuống để không che icon */
            background-color: #16a34a; /* màu xanh thành công */
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        /* Trạng thái ẩn */
        .toast-hidden {
            display: none;
            opacity: 0;
        }
        .toast-error {
                background-color: #dc2626; /* đỏ */
        }
        @keyframes marquee-vertical {
            0% { transform: translateY(0); }
            100% { transform: translateY(-100%); }
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .product-title {
                font-size: 1.6rem;
            }
            .price {
                font-size: 1.4rem;
            }
            .suggested-products {
                margin-top: 30px;
            }
        }

        @media (max-width: 767px) {
            .product-image {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid my-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=product" data-page="product" class="back-btn">Quay lại</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-10">
                <div class="product-detail">
                    <div class="row">
                        <div class="col-md-5">
                            <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url']); ?>" class="product-image img-fluid" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                        <div class="col-md-7">
                            <h1 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                            <p class="text-muted mb-1">Mã sản phẩm: <?php echo $product['product_id']; ?></p>
                            <p class="stock-info">Số lượng: <?php echo $product['stock_quantity']; ?> sản phẩm trong kho</p>
                            <p class="price"><?php echo number_format($product['price'], 0, ',', '.') . ' ₫'; ?></p>
                            <div class="quantity-wrapper">
                                <label for="quantity">Số lượng:</label>
                                <div class="input-group">
                                    <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                                </div>
                            </div>
                            <div id="stock-warning" class="mt-2"></div>
                            <form id="add-to-cart-form" class="mb-4">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <input type="hidden" name="quantity" id="hidden-quantity" value="1">
                                <button type="submit" class="btn btn-add-to-cart"><i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng</button>
                            </form>
                            <div class="card">
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
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($user['full_name']); ?></p>
                                    <p class="text-warning mb-0"><?php echo str_repeat("★", $review['rating']); ?></p>
                                    <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                    <?php if (!empty($review['feedback'])) { ?>
                                        <div class="admin-reply">
                                            <p class="mb-0"><strong>Phản hồi từ Admin:</strong></p>
                                            <p><?php echo htmlspecialchars($review['feedback']); ?></p>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="suggested-products">
                    <h3>Có thể bạn sẽ thích</h3>
                    <div class="marquee-vertical">
                        <div class="marquee-content">
                            <?php
                            $category_ids = array_column($categories, 'category_id');
                            if (!empty($category_ids)) {
                                $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
                                $sqlSuggested = "SELECT p.product_id, p.product_name, p.image_url 
                                                FROM product p 
                                                INNER JOIN product_category pc ON p.product_id = pc.product_id 
                                                WHERE pc.category_id IN ($placeholders) 
                                                AND p.product_id != ? and p.stock_quantity>0 and p.status_id=1
                                                ORDER BY RAND()";
                                $stmt_suggested = $conn->prepare($sqlSuggested);
                                $params = array_merge($category_ids, [$product_id]);
                                $types = str_repeat('i', count($category_ids)) . 'i';
                                $stmt_suggested->bind_param($types, ...$params);
                                $stmt_suggested->execute();
                                $result_suggested = $stmt_suggested->get_result();

                                while ($suggested = $result_suggested->fetch_assoc()) {
                                    echo '<div class="product-item">';
                                    echo '<a href="?page=product_details&id=' . htmlspecialchars($suggested['product_id']) . ' " data-page="product_details&id=' . htmlspecialchars($suggested['product_id']) . '">';
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.getElementById('quantity').addEventListener('input', function () {
            document.getElementById('hidden-quantity').value = this.value;
        });

        const quantityInput = document.getElementById('quantity');
        const maxStock = <?php echo $product['stock_quantity']; ?>;

        quantityInput.addEventListener('blur', function () {
            let val = parseInt(this.value);
            if (isNaN(val) || val < 1) {
                this.value = 1;
                document.getElementById('hidden-quantity').value = 1;
                showWarning("Số lượng phải từ 1 trở lên!");
            } else if (val > maxStock) {
                this.value = maxStock;
                document.getElementById('hidden-quantity').value = maxStock;
                showWarning("Vượt quá số lượng tồn kho! Số lượng tối đa là " + maxStock);
            }
        });

        function showWarning(message) {
            const warningBox = document.getElementById('stock-warning');
            warningBox.innerHTML = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>${message}
                </div>`;
            
            setTimeout(() => {
                warningBox.innerHTML = '';
            }, 3000);
        }
        /// Ajax gửi tới thêm giỏ hàng
        document.getElementById("add-to-cart-form").addEventListener("submit", function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch("../../PublicUI/SanPham/themgiohang.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // ✅ Hiện toast thông báo thành công
                    showToast("Đã thêm vào giỏ hàng!");

                    // ✅ Cập nhật số lượng trong icon giỏ hàng nếu có
                    const cartBadge = document.getElementById("cart-count");
                    if (cartBadge) {
                        cartBadge.innerText = data.cart_count;
                    }

                } else {
                    // ⚠️ Thông báo lỗi
                    showToast(data.message || "Thêm thất bại!", "error");
                }
            });
        });
        /// Toast message
        function showToast(message, type = "success") {
            const toast = document.getElementById("toast");
            toast.textContent = message;

            toast.classList.remove("toast-hidden", "toast-error");
            if (type === "error") {
                toast.classList.add("toast-error");
            }

            toast.style.display = "block";
            toast.style.opacity = "1";

            // Tự động ẩn sau 3 giây
            setTimeout(() => {
                toast.style.opacity = "0";
                setTimeout(() => {
                    toast.style.display = "none";
                    toast.classList.add("toast-hidden");
                }, 300);
            }, 3000);
        }

    </script>
    <div id="toast" class="toast-hidden">Đã thêm vào giỏ hàng!</div>
</body>
</html>