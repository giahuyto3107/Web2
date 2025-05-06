<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include('../../../BackEnd/Config/config.php');

$response = ['success' => false, 'results' => []];

if (isset($_POST['query']) && !empty($_POST['query'])) {
    $query = '%' . $_POST['query'] . '%';
    $sql = "SELECT product_id, product_name, image_url FROM product WHERE product_name LIKE ? AND status_id = 1 AND stock_quantity > 0";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $response['error'] = 'Prepare failed: ' . $conn->error;
        ob_clean();
        echo json_encode($response);
        exit;
    }
    
    $stmt->bind_param('s', $query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = [
            'product_id' => $row['product_id'],
            'product_name' => htmlspecialchars($row['product_name']),
            'image_url' => $row['image_url']
        ];
    }
    
    $response['success'] = true;
    $response['results'] = $results;
    
    $stmt->close();
}

ob_clean(); 
echo json_encode($response);
$conn->close();
?>