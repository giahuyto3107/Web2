<?php
include('../../Config/config.php');
header('Content-Type: application/json');

// Hàm trả về lỗi JSON
function sendError($message) {
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

// Xử lý yêu cầu dựa trên method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Invalid request method. Only POST is allowed.');
}

// --- THÊM SẢN PHẨM ---
$product_name = trim($_POST['product_name'] ?? '');
$product_description = trim($_POST['product_description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$stock_quantity = intval($_POST['stock_quantity'] ?? 0);
$status_id = intval($_POST['status_id'] ?? 0);
$category_ids = isset($_POST['category_ids']) && is_array($_POST['category_ids']) ? $_POST['category_ids'] : [];

// Kiểm tra dữ liệu đầu vào
if (empty($product_name)) {
    sendError('Product name is required.');
}
if ($price <= 0) {
    sendError('Price must be greater than 0.');
}
if ($stock_quantity < 0) {
    sendError('Stock quantity cannot be negative.');
}
if ($status_id <= 0 || $status_id > 6) { // Giả sử status_id từ 1-6
    sendError('Invalid status.');
}
if (empty($category_ids)) {
    sendError('At least one category must be selected.');
}

// Xử lý upload file ảnh
$image_url = '';
if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../../Uploads/Product Picture/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES['image_url']['name']);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    // Kiểm tra loại file và kích thước
    if (!in_array($fileType, $allowedTypes)) {
        sendError('Only JPG, JPEG, PNG, and GIF files are allowed.');
    }
    if ($_FILES['image_url']['size'] > $maxFileSize) {
        sendError('File size must be less than 5MB.');
    }

    $targetPath = $uploadDir . time() . '_' . $fileName;
    if (!move_uploaded_file($_FILES['image_url']['tmp_name'], $targetPath)) {
        sendError('Failed to upload image.');
    }
    $image_url = time() . '_' . $fileName;
}

// Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
$conn->begin_transaction();

try {
    // Thêm sản phẩm vào bảng product
    $sql = "INSERT INTO product (product_name, product_description, price, stock_quantity, status_id, image_url, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param("ssdiiis", $product_name, $product_description, $price, $stock_quantity, $status_id, $image_url);
    if (!$stmt->execute()) {
        throw new Exception('Failed to add product: ' . $stmt->error);
    }

    $product_id = $conn->insert_id; // Lấy ID của sản phẩm vừa thêm
    $stmt->close();

    // Thêm danh mục vào bảng product_category
    if (!empty($category_ids)) {
        $sql_category = "INSERT INTO product_category (product_id, category_id) VALUES (?, ?)";
        $stmt_category = $conn->prepare($sql_category);
        if (!$stmt_category) {
            throw new Exception('Failed to prepare statement for product_category: ' . $conn->error);
        }

        foreach ($category_ids as $category_id) {
            $category_id = intval($category_id);
            if ($category_id <= 0) {
                continue; // Bỏ qua category_id không hợp lệ
            }

            // Kiểm tra category_id có tồn tại trong bảng category
            $sql_check = "SELECT category_id FROM category WHERE category_id = ? AND status_id = 1";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $category_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            if ($result->num_rows === 0) {
                $stmt_check->close();
                continue; // Bỏ qua nếu category không tồn tại hoặc không active
            }
            $stmt_check->close();

            $stmt_category->bind_param("ii", $product_id, $category_id);
            if (!$stmt_category->execute()) {
                throw new Exception('Failed to add product category: ' . $stmt_category->error);
            }
        }
        $stmt_category->close();
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Product added successfully']);

} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    // Xóa ảnh nếu đã upload
    if (!empty($image_url) && file_exists($uploadDir . $image_url)) {
        unlink($uploadDir . $image_url);
    }
    sendError($e->getMessage());
}

$conn->close();
?>