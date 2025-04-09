<?php
include ('../../../BackEnd/Config/config.php');

header('Content-Type: application/json');

$category_type_id = $_GET['category_type_id'] ?? null;


$query = "SELECT * FROM category WHERE status_id = 1"; 
if ($category_type_id) {
    $query .= " AND category_type_id = " . intval($category_type_id);
}

$result = $conn->query($query);
$categories = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}


echo json_encode($categories);

$conn->close();
?>