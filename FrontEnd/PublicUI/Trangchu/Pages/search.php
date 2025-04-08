<?php
// Web2/FrontEnd/PublicUI/Trangchu/Pages/search.php
include ('/Web2/BackEnd/Config/config.php');

if (isset($_GET['query'])) {
    $search_query = $_GET['query'];
    $search_query = "%" . $conn->real_escape_string($search_query) . "%";

    $query = "SELECT * FROM product WHERE product_name LIKE ? AND status_id = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $search_query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<h2>Kết quả tìm kiếm cho: "' . htmlspecialchars($_GET['query']) . '"</h2>';
        echo '<ul>';
        while ($row = $result->fetch_assoc()) {
            echo '<li>' . htmlspecialchars($row['product_name']) . ' - Giá: ' . number_format($row['price']) . ' VNĐ</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>Không tìm thấy sách nào phù hợp.</p>';
    }
    $stmt->close();
} else {
    echo '<p>Vui lòng nhập từ khóa để tìm kiếm.</p>';
}
?>