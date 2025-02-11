<?php
session_start();
header('Content-type: text/html; charset=utf-8');

// Tạo session mặc định khi trang được truy cập
if (!isset($_SESSION['payment_status'])) {
    $_SESSION['payment_status'] = 'pending'; // Hoặc giá trị mặc định nào khác mà bạn muốn
}

// Hàm gửi yêu cầu POST
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

// Lấy giá trị selectedTotal từ session
$selectedTotal = isset($_SESSION['selectedTotal']) ? intval($_SESSION['selectedTotal']) : 0;

$amount = $selectedTotal; // Sử dụng selectedTotal làm amount
$orderId = time() . "";
$redirectUrl = "http://localhost/EC/pages/main/thanhtoan.php"; // Đường dẫn đến thanhtoan.php
$ipnUrl = "http://yourdomain.com/ipn.php"; // Đường dẫn IPN nếu cần
$extraData = "";

if (!empty($_POST)) {
    $requestId = time() . "";
    $requestType = "payWithATM";

    // Tạo chữ ký
    $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    // Dữ liệu để gửi
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

    // Gửi yêu cầu POST
    $result = execPostRequest($endpoint, json_encode($data));
    $jsonResult = json_decode($result, true);  // decode json

    // Kiểm tra nội dung của $jsonResult
    if (isset($jsonResult['payUrl'])) {
        // Chuyển hướng đến URL thanh toán
        header('Location: ' . $jsonResult['payUrl']);
        
        exit(); // Thêm exit để ngăn chặn thực thi mã sau khi chuyển hướng
    } else {
        echo "Lỗi: " . (isset($jsonResult['message']) ? $jsonResult['message'] : 'Không có thông tin chi tiết');
        error_log("Kết quả trả về từ MoMo: " . $result);
    }
}

// Kiểm tra trạng thái thanh toán sau khi người dùng quay lại
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $_SESSION['payment_status'] = 'momo_success'; // Lưu trạng thái thanh toán thành công
        echo "Thanh toán thành công!";
    } else {
        $_SESSION['payment_status'] = 'momo_failed'; // Lưu trạng thái thanh toán thất bại
        echo "Thanh toán thất bại!";
    }
}
?>