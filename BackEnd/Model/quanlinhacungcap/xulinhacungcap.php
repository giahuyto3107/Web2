<?php
include('../../../BackEnd/Config/config.php');
header('Content-Type: application/json');

$supplier_id = $_POST['supplier_id'] ?? null;
$supplier_name = trim($_POST['supplier_name'] ?? '');
$contact_phone = trim($_POST['contact_phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$publisher = trim($_POST['publisher'] ?? '');
$status_id = (int)($_POST['status_id'] ?? 1);

if ($supplier_id) {
    $check_id_sql = "SELECT supplier_name FROM supplier WHERE supplier_id = ?";
    $stmt = $conn->prepare($check_id_sql);
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Nhà cung cấp không tồn tại!']);
        exit;
    }

    if (isset($_POST['status_id']) && $_POST['status_id'] == 6) {
        $sql = "UPDATE supplier SET status_id = ? WHERE supplier_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $status_id, $supplier_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Nhà cung cấp đã được đánh dấu xóa!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi đánh dấu xóa: ' . $conn->error]);
        }
        $stmt->close();
    } else {
        if (empty($supplier_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên nhà cung cấp không được để trống!']);
            exit;
        }

        $current_name = $result->fetch_assoc()['supplier_name'];
        if ($supplier_name !== $current_name) {
            $check_sql = "SELECT supplier_name FROM supplier WHERE supplier_name = ? AND supplier_id != ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("si", $supplier_name, $supplier_id);
            $stmt->execute();
            $check_result = $stmt->get_result();

            if ($check_result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Tên nhà cung cấp đã tồn tại!']);
                exit;
            }
        }

        $sql = "UPDATE supplier SET supplier_name = ?, contact_phone = ?, address = ?, publisher = ?, status_id = ? WHERE supplier_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $supplier_name, $contact_phone, $address, $publisher, $status_id, $supplier_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Nhà cung cấp đã được cập nhật thành công!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi cập nhật nhà cung cấp: ' . $conn->error]);
        }
        $stmt->close();
    }
} else {
    if (empty($supplier_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Tên nhà cung cấp không được để trống!']);
        exit;
    }

    $check_sql = "SELECT supplier_name FROM supplier WHERE supplier_name = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $supplier_name);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Tên nhà cung cấp đã tồn tại!']);
        exit;
    }

    $sql = "INSERT INTO supplier (supplier_name, contact_phone, address, publisher, status_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $supplier_name, $contact_phone, $address, $publisher, $status_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Nhà cung cấp đã được thêm thành công!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Có lỗi khi thêm nhà cung cấp: ' . $conn->error]);
    }
    $stmt->close();
}

$conn->close();
?>