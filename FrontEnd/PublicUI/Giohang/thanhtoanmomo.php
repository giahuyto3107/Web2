<?php
session_start();
header('Content-type: text/html; charset=utf-8');

if (isset($_SESSION['payment_status'])) {
    unset($_SESSION['payment_status']);
}

function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
$orderInfo = "Thanh toán qua MoMo";

// Lấy giá trị từ session
$selectedTotal = isset($_SESSION['selectedTotal']) ? intval($_SESSION['selectedTotal']) : 0;
$amount = $selectedTotal;
$orderId = time() . "";
$redirectUrl = "http://localhost/Web2/FrontEnd/PublicUI/Giohang/xulythanhtoan.php";
$ipnUrl = "http://localhost/Web2/BackEnd/momo_callback.php"; 
$extraData = "";

if (!empty($_POST)) {
    $requestId = time() . "";
    $requestType = "payWithATM";

    // Tạo chữ ký bảo mật
    $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    // Dữ liệu gửi đến MoMo
    $data = array(
        'partnerCode' => $partnerCode,
        'partnerName' => "Test",
        "storeId" => "MomoTestStore",
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'lang' => 'vi',
        'extraData' => $extraData,
        'requestType' => $requestType,
        'signature' => $signature
    );

    // Gửi yêu cầu thanh toán đến MoMo
    $result = execPostRequest($endpoint, json_encode($data));
    $jsonResult = json_decode($result, true);

    // Kiểm tra kết quả phản hồi
    if (isset($jsonResult['payUrl'])) {
        header('Location: ' . $jsonResult['payUrl']);
        exit();
    } else {
        echo "Lỗi: " . (isset($jsonResult['message']) ? $jsonResult['message'] : 'Không có thông tin chi tiết');
        error_log("Kết quả trả về từ MoMo: " . $result);
    }
}

// Kiểm tra trạng thái thanh toán khi người dùng quay lại
if (isset($_GET['resultCode'])) {
    $_SESSION['payment_status'] = 'ok'; // Luôn đặt là 'ok' dù thanh toán thành công hay thất bại

    if ($_GET['resultCode'] == '0') {
        $_SESSION['payment_message'] = "Thanh toán Momo thành công!";
    } else {
        $_SESSION['payment_message'] = "Thanh toán Momo thất bại, vui lòng thử lại!";
    }

    // Chuyển hướng về trang xử lý thanh toán
    header('Location: http://localhost/Web2/FrontEnd/PublicUI/Giohang/xulythanhtoan.php');
    exit();
}
?>
