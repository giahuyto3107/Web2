<?php
include ('../../../BackEnd/Config/config.php');

$query_categories = "SELECT category_id, category_name FROM category WHERE status_id = 1";
$result_categories = mysqli_query($conn, $query_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
?>

<div class="container mx-auto p-4 flex">
    <aside class="w-1/4 bg-white p-4 shadow h-fit">
        <h2 class="text-lg font-bold mb-3">Danh mục</h2>
        <ul>
            <?php foreach ($categories as $category): ?>
                <li class="mb-2">
                    <a href="?category=<?= $category['category_id'] ?>" class="text-blue-500 hover:underline">
                        <?= htmlspecialchars($category['category_name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
    <main class="w-3/4">
        <?php foreach ($categories as $category): ?>
            <?php
            $category_id = $category['category_id'];
            $query_products = "SELECT product_id, product_name, price, image_url FROM product 
                               WHERE status_id = 1 AND category_id = $category_id 
                               ORDER BY created_at DESC LIMIT 4";
            $result_products = mysqli_query($conn, $query_products);
            $products = mysqli_fetch_all($result_products, MYSQLI_ASSOC);
            ?>
            <?php if (!empty($products)): ?>
                <section class="mb-8">
                    <h2 class="text-xl font-bold mb-4"> <?= htmlspecialchars($category['category_name']) ?> </h2>
                    <div class="grid grid-cols-4 gap-4">
                        <?php foreach ($products as $product): ?>
                            <div class="bg-white p-4 shadow rounded">
                                <img src="<?php echo 'http://localhost/Web2/BackEnd/Uploads/Product%20Picture/' . rawurlencode($product['image_url']); ?>" 
                                     alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image mb-2">
                                <h3 class="text-lg font-semibold"> <?= htmlspecialchars($product['product_name']) ?> </h3>
                                <p class="text-red-500 font-bold">$<?= number_format($product['price'], 2) ?></p>
                                <a href="product.php?id=<?= $product['product_id'] ?>" class="text-blue-500 hover:underline">Xem chi tiết</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="category.php?id=<?= $category['category_id'] ?>" class="block text-center text-blue-500 mt-4 hover:underline">Xem thêm</a>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>
    </main>
</div>
