<?php
include ('../../../BackEnd/Config/config.php');

header('Content-Type: application/json');


$query = "SELECT * FROM category_type";
$result = $conn->query($query);
$category_types = [];

while ($row = $result->fetch_assoc()) {
    $category_types[] = $row;
}

// Trả về JSON
echo json_encode($category_types);

$conn->close();
?>