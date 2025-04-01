<?php
header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

$sql = "SELECT permission_id AS id, permission_name AS name
        FROM permission 
        WHERE status_id != 6";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$permissions = [];
while ($row = $result->fetch_assoc()) {
    $permissions[] = $row;
}

echo json_encode([
    'status' => 'success',
    'data' => $permissions
]);

$stmt->close();
$conn->close();
?>