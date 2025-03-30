<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $review_id = $_POST['review_id'] ?? null;
        $review_text = trim($_POST['review_text'] ?? '');
        $feedback = trim($_POST['feedback'] ?? '');
        $status_id = (int)($_POST['status_id'] ?? 1);

        if ($review_id) {
            // Kiểm tra đánh giá tồn tại
            $check_id_sql = "SELECT review_id FROM review WHERE review_id = ?";
            $stmt = $conn->prepare($check_id_sql);
            $stmt->bind_param("i", $review_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Đánh giá không tồn tại!");
            }

            if (isset($_POST['status_id']) && $_POST['status_id'] == 6) {
                // Xóa đánh giá (đánh dấu status_id = 6)
                $update_sql = "UPDATE review SET status_id = ? WHERE review_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ii", $status_id, $review_id);

                if ($stmt->execute()) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Đánh giá đã được đánh dấu xóa!',
                        'data' => []
                    ]);
                } else {
                    throw new Exception("Có lỗi khi đánh dấu xóa: " . $conn->error);
                }
            } else {
                // Cập nhật thông tin đánh giá
                if (empty($review_text)) {
                    throw new Exception("Nội dung đánh giá không được để trống!");
                }

                // Chỉ cập nhật review_text, feedback, và status_id
                $update_sql = "UPDATE review SET review_text = ?, feedback = ?, status_id = ? WHERE review_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ssii", $review_text, $feedback, $status_id, $review_id);

                if ($stmt->execute()) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Đánh giá đã được cập nhật thành công!',
                        'data' => []
                    ]);
                } else {
                    throw new Exception("Có lỗi khi cập nhật đánh giá: " . $conn->error);
                }
            }
            $stmt->close();
        } else {
            throw new Exception("Yêu cầu không hợp lệ!");
        }
    } else {
        throw new Exception("Phương thức không được hỗ trợ!");
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'data' => []
    ]);
} finally {
    $conn->close();
}
?>