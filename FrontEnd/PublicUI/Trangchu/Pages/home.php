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
$best_sellers = $conn->query("
    SELECT p.product_name, p.price, p.image_url, oi.product_id, p.product_name, SUM(oi.quantity) as total_sold
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    INNER JOIN product p ON oi.product_id = p.product_id
    WHERE o.status_id = 5 AND p.status_id = 1
    GROUP BY oi.product_id, p.product_name
    ORDER BY total_sold DESC
    LIMIT 5
");
$new_releases = $conn->query("SELECT * FROM product WHERE status_id = 1 ORDER BY created_at DESC LIMIT 5");
$featured_collection = $conn->query("SELECT p.* FROM product p JOIN product_category pc ON p.product_id = pc.product_id WHERE pc.category_id = 1 AND p.status_id = 1 LIMIT 3");
?>

<style>
    /* Hero Section */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Georgia&display=swap');
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
    }
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
        /* filter: brightness(0.95); */
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
        background:rgb(74, 74, 74);
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
        padding: 120px 8%;
        background: var(--white);
        position: relative;
        overflow: hidden;
    }
    
    .collections-carousel {
        display: flex;
        gap: 30px;
        padding: 20px 0;
        scroll-snap-type: x mandatory;
        overflow-x: auto;
        scrollbar-width: none;
    }
    
    .collections-carousel::-webkit-scrollbar {
        display: none;
    }
    
    .collection-card {
        flex: 0 0 calc(33.333% - 20px);
        scroll-snap-align: start;
        height: 500px;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        transition: var(--transition);
    }
    
    .collection-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .collection-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 40px;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
        color: var(--white);
        transform: translateY(100px);
        opacity: 0;
        transition: var(--transition);
    }
    
    .collection-card h3 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .collection-card p {
        font-size: 1.1rem;
        margin-bottom: 20px;
    }
    
    .collection-card:hover {
        transform: translateY(-10px);
    }
    
    .collection-card:hover img {
        transform: scale(1.1);
    }
    
    .collection-card:hover .collection-overlay {
        transform: translateY(0);
        opacity: 1;
    }

    /* Reviews */
    .reviews-section {
    background: white
    padding: 60px 20px;
}

.reviews-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a2a44;
    text-align: center;
    margin-bottom: 50px;
    letter-spacing: 1px;
}

.reviews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.review-card {
    position: relative;
    text-align: center;
    padding: 30px 25px;
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.4s ease;
}

.review-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 15px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.review-card p {
    font-family: 'Poppins', sans-serif;
    height: 70px;
    font-size: 1rem;
    font-style: italic;
    color: #4a4a4a;
    margin-bottom: 15px;
    line-height: 1.7;
}

.review-card span {
    font-family: 'Poppins', sans-serif;
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a2a44;
    letter-spacing: 0.5px;
}

.review-card .stars {
    margin-top: 10px;
    display: flex;
    justify-content: center;
    gap: 6px;
}

.review-card .stars i {
    font-size: 1.1rem;
    color: #d4a017;
    transition: transform 0.3s ease;
}

.review-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.review-card:hover .review-avatar {
    transform: scale(1.15);
}

.review-card:hover .stars i {
    transform: scale(1.2);
}

/* Responsive */
@media (max-width: 768px) {
    .reviews-section h2 {
        font-size: 2rem;
    }

    .review-card {
        padding: 25px 20px;
    }

    .review-avatar {
        width: 50px;
        height: 50px;
    }

    .review-card p {
        font-size: 0.9rem;
    }

    .review-card span {
        font-size: 1rem;
    }
}
    .product-card .product-link{
        text-decoration: none;
        
    }
/* Responsive Layout Enhancements */
@media (max-width: 1024px) {
    .hero {
        padding: 0 60px;
    }

    .products-grid {
        grid-template-columns: repeat(3, 1fr);
        justify-items: center;
    }

    .collections-carousel {
        padding: 0 60px 20px;
    }

    .categories-section,
    .products-section,
    .collections-section,
    .reviews-section {
        padding: 80px 60px;
    }
}

@media (max-width: 768px) {
    .hero {
        flex-direction: column;
        justify-content: center;
        text-align: center;
        padding: 0 30px;
        height: auto;
        min-height: 80vh;
    }

    .hero h1 {
        font-size: 3rem;
    }

    .hero p {
        font-size: 1.2rem;
    }

    .hero .cta-btn {
        padding: 10px 30px;
        font-size: 1rem;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        justify-items: center;
    }

    .product-card {
        width: 160px;
        height: 320px;
    }

    .product-image {
        width: 130px;
        height: 180px;
    }

    .collections-carousel {
        padding: 0 30px 20px;
        gap: 30px;
    }

    .collection-card {
        flex: 1 1 240px; /* Cho phép co giãn linh hoạt */
        min-width: 220px;
        max-width: 100%;
        height: auto; /* Không cần cố định chiều cao */
    }

    .categories-list {
        flex-wrap: wrap;
        gap: 20px;
    }

    .category-item {
        font-size: 1rem;
        padding: 10px 25px;
    }

    .categories-section,
    .products-section,
    .collections-section,
    .reviews-section {
        padding: 60px 30px;
    }

    h2 {
        font-size: 2rem;
        margin-bottom: 40px;
    }
}

@media (max-width: 480px) {
    .hero h1 {
        font-size: 2.2rem;
    }

    .hero p {
        font-size: 1rem;
    }

    .products-grid {
        grid-template-columns: 1fr;
    }

    .product-card {
        width: 90%;
    }

    .collection-card {
        width: 100%;
        height: auto;
    }

    .collection-card .collection-info {
        width: 90%;
        left: 50%;
        transform: translateX(-50%);
    }

    .categories-list {
        gap: 15px;
        justify-content: flex-start;
    }

    .category-item {
        font-size: 0.9rem;
        padding: 8px 20px;
    }
}
.collections-carousel {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}
</style>

<section class="hero">
    <div class="hero-background">
        <img src="../../../BackEnd/Uploads/Background1.png" alt="Hero">
    </div>
    <div class="hero-content">
        <h1>Góc Sách Nhỏ</h1>
        <p>Khám phá thế giới tri thức</p>
        <a href="?page=product" data-page="product" class="cta-btn">Bộ sưu tập</a>
    </div>
</section>

<!-- <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12 animate-fadeInUp">Danh Mục Phổ Biến</h2>
            <div class="flex flex-wrap justify-center gap-6">
                <?php 
                if ($categories && $categories->num_rows > 0) {
                    while ($category = $categories->fetch_assoc()): ?>
                        <a href="" style="text-decoration: none;" class="px-6 py-3 bg-gray-100 text-gray-900 rounded-md text-lg font-medium hover:bg-gray-900 hover:text-white transition animate-fadeInUp">
                        <?php echo htmlspecialchars($category['category_name']); ?></a>
                    <?php endwhile; 
                } else {
                    echo "<p class='text-gray-600 text-center'>Không có danh mục nào.</p>";
                } ?>
            </div>
        </div>
    </section> -->

<section class="products-section">
    <h2>Sách bán chạy</h2>
    <div class="products-grid">
        <?php 
        if ($best_sellers && $best_sellers->num_rows > 0) {
            while ($product = $best_sellers->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="index.php?page=product_details&id=<?php echo $product['product_id']; ?>" class="product-link">
                    <div class="product-image">
                        <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url'] ?: 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <div class="bookmark"></div>
                    </div>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="price"><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></p>
                    </a>
                    <!-- <a href="#" class="add-to-cart"><i class="fa-solid fa-cart-plus"></i></a> -->
                </div>
            <?php endwhile; 
        } else {
            echo "<p>Không có sách bán chạy.</p>";
        } ?>
    </div>
</section>

<section class="collections-section">
    <div class="section-heading">
        <h2>Bộ Sưu Tập Nổi Bật</h2>
    </div>
    
    <div class="collections-carousel">
        <?php 
        if ($featured_collection && $featured_collection->num_rows > 0) {
            while ($product = $featured_collection->fetch_assoc()): ?>
                <div class="collection-card">
                    <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url'] ?: 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <div class="collection-overlay">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></p>
                        <a style="text-decoration: none;" href="index.php?page=product_details&id=<?php echo $product['product_id']; ?>" class="view-details">Khám phá</a>
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
                    <a href="index.php?page=product_details&id=<?php echo $product['product_id']; ?>" class="product-link">
                    <div class="product-image">
                        <img src="../../../BackEnd/Uploads/Product Picture/<?php echo htmlspecialchars($product['image_url'] ?: 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <span class="new-badge">Mới</span>
                        <div class="bookmark"></div>
                    </div>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="price"><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></p>
                    </a>
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
            <img src="../../../BackEnd/Uploads/xavier.jpg" alt="Xavier" class="review-avatar">
            <p>“यह किताबों की दुकान बहुत विचारशील और सस्ती है।”</p>
            <span>Xavier</span>
            <div class="stars">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
            </div>
        </div>
        <div class="review-card">
            <img src="../../../BackEnd/Uploads/trump.jpg" alt="Donal Trump" class="review-avatar">
            <p>“Tôi sẽ giảm thuế để các bạn nhập hàng vào Mỹ”</p>
            <span>Donal Trump</span>
            <div class="stars">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star-half-alt"></i>
            </div>
        </div>
        <div class="review-card">
            <img src="../../../BackEnd/Uploads/pnv.jpg" alt="Phạm Nhật Vượng" class="review-avatar">
            <p>“Nhờ đọc sách ở đây mà tôi đã có thêm kinh nghiệm để thành lập ra Vingroup”</p>
            <span>Phạm Nhật Vượng</span>
            <div class="stars">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
            </div>
        </div>
    </div>
</section>

<?php
// Đóng kết nối
$conn->close();
?>