<?php
session_start();
include('../../../BackEnd/Config/config.php');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$account_id = $_SESSION['user_id'];
$sql = "SELECT address FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $account_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'address' => $row['address']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No address found']);
}

$stmt->close();
$conn->close();
?>