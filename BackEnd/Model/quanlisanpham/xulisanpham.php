<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Đường dẫn lưu hình ảnh
    $uploadDir = '../../../BackEnd/Uploads/Product Picture/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if ($action === 'add') {
        // Thêm sản phẩm mới
        $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
        $product_description = isset($_POST['product_description']) ? trim($_POST['product_description']) : '';
        $price = 0;
        $stock_quantity = 0;
        $status_id = isset($_POST['status_id']) ? intval($_POST['status_id']) : 1;
        $categories = isset($_POST['categories']) ? $_POST['categories'] : [];

        if (empty($product_name) || empty($product_description)) {
            throw new Exception('Dữ liệu không hợp lệ');
        }

        // Xử lý upload hình ảnh
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = uniqid() . '-' . basename($_FILES['image']['name']);
            $image_path = $uploadDir . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                throw new Exception('Lỗi khi upload hình ảnh');
            }
            $image_url = $image_name;
        }

        // Thêm sản phẩm vào bảng product
        $sql = "INSERT INTO product (product_name, product_description, price, stock_quantity, status_id, image_url) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiss", $product_name, $product_description, $price, $stock_quantity, $status_id, $image_url);
        $stmt->execute();

        $product_id = $conn->insert_id;
        $stmt->close();

        // Thêm thể loại vào bảng product_category
        if (!empty($categories)) {
            $sql = "INSERT INTO product_category (product_id, category_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($categories as $category_id) {
                $stmt->bind_param("ii", $product_id, $category_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Sản phẩm đã được thêm thành công'
        ]);
    } elseif ($action === 'edit') {
        // Sửa sản phẩm
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
        $product_description = isset($_POST['product_description']) ? trim($_POST['product_description']) : '';
        $status_id = isset($_POST['status_id']) ? intval($_POST['status_id']) : 1;
        $categories = isset($_POST['categories']) ? $_POST['categories'] : [];

        if ($product_id <= 0 || empty($product_name) || empty($product_description)) {
            throw new Exception('Dữ liệu không hợp lệ');
        }

        // Lấy thông tin sản phẩm hiện tại để giữ nguyên price, stock_quantity và kiểm tra hình ảnh cũ
        $sql = "SELECT price, stock_quantity, image_url FROM product WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_product = $result->fetch_assoc();
        $stmt->close();

        if (!$current_product) {
            throw new Exception('Sản phẩm không tồn tại');
        }

        // Giữ nguyên price và stock_quantity từ cơ sở dữ liệu
        $price = $current_product['price'];
        $stock_quantity = $current_product['stock_quantity'];
        $image_url = $current_product['image_url'];

        // Xử lý upload hình ảnh mới (nếu có)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Xóa hình ảnh cũ nếu có
            if ($image_url && file_exists('../../../' . $image_url)) {
                unlink('../../../' . $image_url);
            }

            $image_name = uniqid() . '-' . basename($_FILES['image']['name']);
            $image_path = $uploadDir . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                throw new Exception('Lỗi khi upload hình ảnh');
            }
            $image_url = 'BackEnd/Uploads/Product Picture/' . $image_name;
        }

        // Cập nhật sản phẩm mà không thay đổi price và stock_quantity
        $sql = "UPDATE product SET product_name = ?, product_description = ?, status_id = ?, image_url = ? 
                WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $product_name, $product_description, $status_id, $image_url, $product_id);
        $stmt->execute();
        $stmt->close();

        // Xóa các thể loại cũ
        $sql = "DELETE FROM product_category WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();

        // Thêm thể loại mới
        if (!empty($categories)) {
            $sql = "INSERT INTO product_category (product_id, category_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($categories as $category_id) {
                $stmt->bind_param("ii", $product_id, $category_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Sản phẩm đã được cập nhật'
        ]);
    } elseif ($action === 'delete') {
        // Xóa sản phẩm (cập nhật status_id thành 6)
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $status_id = 6;

        if ($product_id <= 0) {
            throw new Exception('Invalid product ID');
        }
        // Kiểm tra stock_quantity
        $sql = "SELECT stock_quantity FROM product WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if ($product['stock_quantity'] > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Không thể xóa sản phẩm khi số lượng tồn kho lớn hơn 0'
            ]);
            exit;
        }
        
        $sql = "UPDATE product SET status_id = ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $status_id, $product_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Sản phẩm đã được đánh dấu xóa'
            ]);
        } else {
            throw new Exception('Không thể xóa sản phẩm');
        }

        $stmt->close();
    } else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>