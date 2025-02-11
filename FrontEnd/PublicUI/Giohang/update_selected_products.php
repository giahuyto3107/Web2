<?php
session_start();

if (isset($_POST['selectedProducts'])) {
    $_SESSION['selectedProducts'] = json_decode($_POST['selectedProducts'], true);
    $selectedTotal = 0;
    
    foreach ($_SESSION['selectedProducts'] as $product) {
        $selectedTotal += $product['price'];
    }
    $_SESSION['selectedTotal'] = $selectedTotal;    
    $formattedTotal = number_format($selectedTotal, 0, ',', ',') . '₫';

    echo json_encode(["success" => true, "selectedTotal" => $formattedTotal]);
}
?>
