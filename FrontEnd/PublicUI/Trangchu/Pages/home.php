<?php
// session_start();
include ('../../../BackEnd/Config/config.php');
$user_name = isset($_SESSION['dangky']) ? $_SESSION['dangky'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null);
if (isset($_GET['page']) && $_GET['page'] === 'logout') {
    session_destroy();
    header("Location: http://localhost/Web2/FrontEnd/PublicUI/Trangchu/index.php?page=home");
    exit();
}

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT SUM(quantity) as total FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $cart_count = $row['total'] ?? 0;
    $stmt->close();
}

$categories = $conn->query("SELECT * FROM category WHERE status_id = 1 LIMIT 4");
$best_sellers = $conn->query("SELECT * FROM product WHERE status_id = 1 ORDER BY stock_quantity ASC LIMIT 5");
$new_releases = $conn->query("SELECT * FROM product WHERE status_id = 1 ORDER BY created_at DESC LIMIT 5");
$featured_collection = $conn->query("SELECT p.* FROM product p JOIN product_category pc ON p.product_id = pc.product_id WHERE pc.category_id = 1 AND p.status_id = 1 LIMIT 3");
?>

<style>
    /* Hero Section */
    .hero {
        height: 90vh;
        position: relative;
        display: flex;
        align-items: center;
        padding: 0 120px;
        
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        background: linear-gradient(to bottom, #fdfbfb, #ebedee);
    }

    .hero-background img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.95);
    }

    .hero-content {
        max-width: 100%;
        padding: 30px;
        border-radius: 15px;
        animation: fadeInUp 1s ease;
    }

    .hero h1 {
        font-family: 'Georgia', serif;
        font-size: 4.5rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #111111;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 1.6rem;
        font-weight: 300;
        color: #666666;
        margin-bottom: 30px;
        font-style: italic;
    }

    .hero .cta-btn {
        padding: 12px 40px;
        background: rgb(0, 0, 0);
        color: #ffffff;
        font-size: 1.2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-decoration: none;
        border-radius: 30px;
        transition: all 0.3s ease;
    }

    .hero .cta-btn:hover {
        background: #5e2d0d;
        transform: translateY(-3px);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Section General */
    .categories-section, .products-section, .collections-section, .reviews-section {
        padding: 120px 120px;
        max-width: 1800px;
        margin: 0 auto;
        position: relative;
    }

    h2 {
        font-family: 'Georgia', serif;
        font-size: 2.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #111111;
        text-align: center;
        margin-bottom: 60px;
        position: relative;
    }

    h2::after {
        content: '';
        width: 60px;
        height: 2px;
        background: rgb(67, 67, 67);
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }

    /* Categories */
    .categories-section {
        background: #f9f9f9;
    }

    .categories-bg {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        opacity: 0.2;
    }

    .categories-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: sepia(0.3);
    }

    .categories-list {
        display: flex;
        justify-content: center;
        gap: 30px;
        padding: 20px 0;
    }

    .category-item {
        padding: 12px 35px;
        background: #ffffff;
        border: 2px solid rgb(0, 0, 0);
        border-radius: 25px;
        font-size: 1.2rem;
        font-weight: 500;
        color: rgb(0, 0, 0);
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .category-item:hover {
        background: rgb(53, 53, 53);
        color: #ffffff;
        transform: scale(1.05);
    }

    /* Products */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 40px;
    }

    .product-card {
        position: relative;
        width: 200px;
        height: 360px;
        text-align: center;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .product-image {
        position: relative;
        width: 150px;
        height: 200px;
        margin: 20px auto 10px;
    }

    .product-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 5px;
        transition: transform 0.3s ease;
    }

    .product-card h3 {
        font-size: 1.1rem;
        font-weight: 500;
        color: #111111;
        margin: 0 10px 5px;
        word-wrap: break-word;
        max-height: 50px;
        line-height: 1.2;
        height: 70px;
        margin-bottom: 30px;
    }

    .product-card .price {
        font-size: 1.2rem;
        font-weight: 600;
        color: rgb(255, 0, 0);
    }

    .product-card .add-to-cart {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 35px;
        height: 35px;
        background: #111111;
        color: #ffffff;
        font-size: 1.2rem;
        text-decoration: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card .new-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgb(255, 0, 0);
        color: #ffffff;
        font-size: 0.8rem;
        padding: 4px 10px;
        border-radius: 15px;
        text-transform: uppercase;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .product-card:hover img {
        transform: scale(1.05);
    }

    .product-card:hover .add-to-cart {
        opacity: 1;
    }

    /* Collections */
    .collections-section {
        background: #ffffff;
        padding: 120px 0;
    }

    .collections-bg {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 60%;
        height: 80%;
        transform: translate(-50%, -50%);
        opacity: 0.1;
    }

    .collections-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: sepia(0.3);
    }

    .collections-carousel {
        display: flex;
        gap: 50px;
        overflow-x: auto;
        padding: 0 120px 20px;
    }

    .collections-carousel::-webkit-scrollbar {
        height: 8px;
    }

    .collections-carousel::-webkit-scrollbar-thumb {
        background: rgb(0, 0, 0);
        border-radius: 4px;
    }

    .collection-card {
        position: relative;
        flex: 0 0 320px;
        width: 320px;
        height: 420px;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .collection-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.85);
        transition: transform 0.5s ease;
    }

    .collection-card .collection-info {
        position: absolute;
        bottom: 20px;
        left: 20px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
    }

    .collection-card h3 {
        font-family: 'Georgia', serif;
        font-size: 1.3rem;
        font-weight: 600;
        color: #111111;
        margin-bottom: 5px;
        word-wrap: break-word;
        max-height: 50px;
        overflow: hidden;
        line-height: 1.2;
    }

    .collection-card p {
        font-size: 1.1rem;
        font-weight: 500;
        color: rgb(0, 0, 0);
    }

    .collection-card:hover img {
        transform: scale(1.1);
    }

    /* Reviews */
    .reviews-section {
        background: #f9f9f9;
    }

    .reviews-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
    }

    .review-card {
        text-align: center;
        padding: 30px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .review-card p {
        font-family: 'Georgia', serif;
        font-size: 1.2rem;
        font-style: italic;
        color: #333333;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .review-card span {
        font-size: 1rem;
        font-weight: 600;
        color: rgb(0, 0, 0);
        letter-spacing: 1px;
    }

    .review-card .stars {
        margin-top: 10px;
        display: flex;
        justify-content: center;
        gap: 6px;
    }

    .review-card .stars i {
        font-size: 1.3rem;
        color: #ffd700;
        transition: transform 0.3s ease;
    }

    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .review-card:hover .stars i {
        transform: scale(1.1);
    }
</style>

<section class="hero">
    <div class="hero-background">
        <!-- <img src="../../../BackEnd/Uploads/Background.jpg" alt="Hero"> -->
    </div>
    <div class="hero-content">
        <h1>Góc Sách Nhỏ</h1>
        <p>Khám phá thế giới tri thức</p>
        <a href="?page=product" data-page="product" class="cta-btn">Khám phá ngay</a>
    </div>
</section>

<section class="categories-section">
    <div class="categories-bg">
        <img src="../../../BackEnd/Uploads/Background1.jpg" alt="Background">
    </div>
    <h2>Danh mục phổ biến</h2>
    <div class="categories-list">
        <?php 
        if ($categories && $categories->num_rows > 0) {
            while ($category = $categories->fetch_assoc()): ?>
                <a href="#" class="category-item"><?php echo htmlspecialchars($category['category_name']); ?></a>
            <?php endwhile; 
        } else {
            echo "<p>Không có danh mục nào.</p>";
        } ?>
    </div>
</section>

<section class="products-section">
    <h2>Sách bán chạy</h2>
    <div class="products-grid">
        <?php 
        if ($best_sellers && $best_sellers->num_rows > 0) {
            while ($product = $best_sellers->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url'] ?: 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <div class="bookmark"></div>
                    </div>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="price"><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></p>
                    <a href="#" class="add-to-cart"><i class="fa-solid fa-cart-plus"></i></a>
                </div>
            <?php endwhile; 
        } else {
            echo "<p>Không có sách bán chạy.</p>";
        } ?>
    </div>
</section>

<section class="collections-section">
    <div class="collections-bg">
        <!-- <img src="../../../BackEnd/Uploads/Product Picture/book-bg2.jpg" alt="Background"> -->
    </div>
    <h2>Bộ sưu tập nổi bật</h2>
    <div class="collections-carousel">
        <?php 
        if ($featured_collection && $featured_collection->num_rows > 0) {
            while ($product = $featured_collection->fetch_assoc()): ?>
                <div class="collection-card">
                    <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url'] ?: 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <div class="collection-info">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></p>
                    </div>
                </div>
            <?php endwhile; 
        } else {
            echo "<p>Không có bộ sưu tập nổi bật.</p>";
        } ?>
    </div>
</section>

<section class="products-section">
    <h2>Sách mới phát hành</h2>
    <div class="products-grid">
        <?php 
        if ($new_releases && $new_releases->num_rows > 0) {
            while ($product = $new_releases->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url'] ?: 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <span class="new-badge">Mới</span>
                        <div class="bookmark"></div>
                    </div>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="price"><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></p>
                </div>
            <?php endwhile; 
        } else {
            echo "<p>Không có sách mới phát hành.</p>";
        } ?>
    </div>
</section>

<section class="reviews-section">
    <h2>Khách hàng nói gì</h2>
    <div class="reviews-grid">
        <div class="review-card">
            <p>“Giao hàng nhanh, sách đẹp và chất lượng”</p>
            <span>Anh da đen</span>
            <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="review-card">
            <p>“Nhiều sách hay, giá cả hợp lý.”</p>
            <span>Trần B</span>
            <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-alt"></i></div>
        </div>
        <div class="review-card">
            <p>“Trải nghiệm mua sắm tuyệt vời!”</p>
            <span>Lê C</span>
            <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        </div>
    </div>
</section>

<?php
// Đóng kết nối
$conn->close();
?>