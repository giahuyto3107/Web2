<?php
session_start();
include('../../admincp/config/config.php');
require '../../src/Exception.php';
require '../../src/PHPMailer.php';
require '../../src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['id_khachhang'])) {
    header('Location: login.php');
    exit();
}

$id_khachhang = $_SESSION['id_khachhang'];
$sql_user = "SELECT * FROM tbl_user WHERE id_user='$id_khachhang'";
$result_user = mysqli_query($mysqli, $sql_user);
$user_info = mysqli_fetch_array($result_user);

if (!$user_info) {
    echo "Không tìm thấy thông tin người dùng.";
    exit();
}

$email_nguoi_nhan = $user_info['email'];
$ten_nguoi_nhan = $user_info['username'];


$dia_chi_giao_hang = isset($_SESSION['diachi']) ? $_SESSION['diachi'] : '';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_payment'])) {
    $payment_method = 'COD';

    $dia_chi_giao_hang = isset($_POST['use_default']) && $_POST['use_default'] == 1 ? $user_info['diachicuthe'] : $_POST['dia_chi_giao_hang'];


    $insert_cart = "INSERT INTO tbl_cart(id_khachhang, cart_status, cart_date, dia_chi_giao_hang, payment_method) 
                    VALUES ('$id_khachhang', 1, NOW(), '$dia_chi_giao_hang', '$payment_method')";

    if (mysqli_query($mysqli, $insert_cart)) {
        $idcart = mysqli_insert_id($mysqli);
        $products_details = '';


        if (isset($_POST['selected_products'])) {
            foreach ($_POST['selected_products'] as $product_id) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $cart_item = $_SESSION['cart'][$product_id];
                    $soluong = $cart_item['soluong'];
                    $id_sanpham = $cart_item['id_sanpham'];

                    $insert_order_details = "INSERT INTO tbl_cart_details(id_cart_details, id_sanpham, soluongmua) 
                                             VALUES ('$idcart', '$id_sanpham', '$soluong')";
                    mysqli_query($mysqli, $insert_order_details);

                    $sql_product = "SELECT * FROM tbl_sanpham WHERE id_sanpham='$id_sanpham'";
                    $result_product = mysqli_query($mysqli, $sql_product);
                    $product_info = mysqli_fetch_array($result_product);

                    $products_details .= "<li>" . $product_info['tensanpham'] . " - Số lượng: " . $soluong . "</li>";
                    unset($_SESSION['cart'][$product_id]);
                }
            }

            
        }

        // Gửi email xác nhận
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tmd20040101@gmail.com';
            $mail->Password   = 'qlmnlddsqomzbupn';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('tmd20040101@gmail.com', 'DevNguyenStore');
            $mail->addAddress($email_nguoi_nhan, $ten_nguoi_nhan);

            $mail->isHTML(true);
            $mail->Subject = 'Purchase Confirmation';
            $mail->Body    = $mail->isHTML(true);
            $mail->Body = "
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                    }
                    h1 {
                        color: #112D60;
                    }
                    .email-container {
                        border: 1px solid #ddd;
                        padding: 20px;
                        border-radius: 5px;
                        background: #f9f9f9;
                    }
                    .email-footer {
                        margin-top: 20px;
                        font-size: 0.9em;
                        color: #555;
                    }
                    .btn {
                        display: inline-block;
                        margin-top: 10px;
                        padding: 10px 20px;
                        background: #112D60;
                        color: #fff;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 1em;
                    }
                    .btn:hover {
                        background: #0a1d40;
                    }
                </style>
            
                <div class='email-container'>
                    <h1>Xin chào <strong>$ten_nguoi_nhan</strong>,</h1>
                    <p>Cảm ơn bạn đã tin tưởng và lựa chọn <strong>DevNguyenStore</strong> cho nhu cầu mua sắm của bạn. Chúng tôi rất vui được thông báo rằng đơn hàng của bạn đã được ghi nhận thành công.</p>
            
                    <p><strong>Chi tiết đơn hàng:</strong></p>
                    <ul>
                        <li><strong>Địa chỉ giao hàng:</strong> $dia_chi_giao_hang</li>
                        <li><strong>Mã đơn hàng:</strong> #$idcart</li>
                    </ul>
            
                    <p><strong>Sản phẩm trong đơn hàng:</strong></p>
                    <ul>$products_details</ul>
            
                    <p><strong>Phương thức thanh toán:</strong> COD (Thanh toán khi nhận hàng)</p>
            
                    <p><strong>Thông tin quan trọng:</strong></p>
                    <ul>
                        <li>Thời gian xử lý đơn hàng: 1-2 ngày làm việc.</li>
                        <li>Thời gian giao hàng dự kiến: 3-5 ngày làm việc (tùy khu vực).</li>
                    </ul>
            
                    <p>Bạn có thể theo dõi trạng thái đơn hàng của mình trực tiếp qua tài khoản tại <a href='https://devnguyenstore.com'>DevNguyenStore</a>.</p>
            
                    <a class='btn' href='https://devnguyenstore.com/track-order?id=$idcart'>Theo dõi đơn hàng</a>
            
                    <p>Để đảm bảo trải nghiệm mua sắm tốt nhất, nếu có bất kỳ câu hỏi hoặc yêu cầu hỗ trợ nào, vui lòng liên hệ với chúng tôi qua:</p>
                    <ul>
                        <li>Email: <a href='mailto:support@devnguyenstore.com'>support@devnguyenstore.com</a></li>
                        <li>Hotline: <strong>1900-1234</strong></li>
                    </ul>
            
                    <p>Chúng tôi rất mong được phục vụ bạn trong những lần mua sắm tiếp theo!</p>
                    
                    <p>Trân trọng,<br>
                    <strong>Đội ngũ DevNguyenStore</strong></p>
                </div>
            
                <div class='email-footer'>
                    <p>Bạn nhận được email này vì đã đặt hàng tại <strong>DevNguyenStore</strong>. Nếu đây không phải là bạn, vui lòng liên hệ ngay với chúng tôi để được hỗ trợ.</p>
                </div>
            ";
            

            $mail->send();
            // Reset session sau khi đặt hàng

            unset($_SESSION['diachi']); // Xóa địa chỉ khỏi session
            header('Location: ../../index.php?quanly=camon');
            exit(); 
        } catch (Exception $e) {
            echo "Lỗi khi gửi email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Lỗi khi thêm giỏ hàng: " . mysqli_error($mysqli);
        exit();
    }
} else if (isset($_SESSION['payment_status'])) { 
    $payment_method = 'Online';
    if (!empty($_SESSION['cart'])) {
        // Thêm giỏ hàng vào cơ sở dữ liệu
        // Thêm giỏ hàng vào cơ sở dữ liệu với payment_method là Online
        $insert_cart = "INSERT INTO tbl_cart(id_khachhang, cart_status, cart_date, dia_chi_giao_hang, payment_method) 
                        VALUES ('$id_khachhang', 1, NOW(), '$dia_chi_giao_hang', '$payment_method')";

        if (mysqli_query($mysqli, $insert_cart)) {
            $idcart = mysqli_insert_id($mysqli);
            $products_details = '';

            // Lấy sản phẩm từ giỏ hàng và lưu vào tbl_cart_details
            foreach ($_SESSION['cart'] as $product_id => $cart_item) {
                $soluong = $cart_item['soluong'];
                $id_sanpham = $cart_item['id_sanpham'];

                $insert_order_details = "INSERT INTO tbl_cart_details(id_cart_details, id_sanpham, soluongmua) 
                                         VALUES ('$idcart', '$id_sanpham', '$soluong')";
                mysqli_query($mysqli, $insert_order_details);

                // Lấy thông tin sản phẩm
                $sql_product = "SELECT * FROM tbl_sanpham WHERE id_sanpham='$id_sanpham'";
                $result_product = mysqli_query($mysqli, $sql_product);
                $product_info = mysqli_fetch_array($result_product);

                $products_details .= "<li>" . $product_info['tensanpham'] . " - Số lượng: " . $soluong . "</li>";
            }


            // Gửi email xác nhận
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'tmd20040101@gmail.com';
                $mail->Password   = 'qlmnlddsqomzbupn';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('tmd20040101@gmail.com', 'DevNguyenStore');
                $mail->addAddress($email_nguoi_nhan, $ten_nguoi_nhan);

                $mail->isHTML(true);
                $mail->Subject = 'Purchase Confirmation';
                $mail->Body    = $mail->isHTML(true);
                $mail->Body = "
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            color: #333;
                        }
                        h1 {
                            color: #112D60;
                        }
                        .email-container {
                            border: 1px solid #ddd;
                            padding: 20px;
                            border-radius: 5px;
                            background: #f9f9f9;
                        }
                        .email-footer {
                            margin-top: 20px;
                            font-size: 0.9em;
                            color: #555;
                        }
                        .btn {
                            display: inline-block;
                            margin-top: 10px;
                            padding: 10px 20px;
                            background: #112D60;
                            color: #fff;
                            text-decoration: none;
                            border-radius: 5px;
                            font-size: 1em;
                        }
                        .btn:hover {
                            background: #0a1d40;
                        }
                    </style>
                
                    <div class='email-container'>
                        <h1>Xin chào <strong>$ten_nguoi_nhan</strong>,</h1>
                        <p>Cảm ơn bạn đã tin tưởng và lựa chọn <strong>DevNguyenStore</strong> cho nhu cầu mua sắm của bạn. Chúng tôi rất vui được thông báo rằng đơn hàng của bạn đã được ghi nhận thành công.</p>
                
                        <p><strong>Chi tiết đơn hàng:</strong></p>
                        <ul>
                            <li><strong>Địa chỉ giao hàng:</strong> $dia_chi_giao_hang</li>
                            <li><strong>Mã đơn hàng:</strong> #$idcart</li>
                        </ul>
                
                        <p><strong>Sản phẩm trong đơn hàng:</strong></p>
                        <ul>$products_details</ul>
                
                        <p><strong>Phương thức thanh toán:</strong> ATM (Thanh toán qua thẻ ngân hàng)</p>
                
                        <p><strong>Thông tin quan trọng:</strong></p>
                        <ul>
                            <li>Thời gian xử lý đơn hàng: 1-2 ngày làm việc.</li>
                            <li>Thời gian giao hàng dự kiến: 3-5 ngày làm việc (tùy khu vực).</li>
                        </ul>
                
                        <p>Bạn có thể theo dõi trạng thái đơn hàng của mình trực tiếp qua tài khoản tại <a href='https://devnguyenstore.com'>DevNguyenStore</a>.</p>
                
                        <a class='btn' href='https://devnguyenstore.com/track-order?id=$idcart'>Theo dõi đơn hàng</a>
                
                        <p>Để đảm bảo trải nghiệm mua sắm tốt nhất, nếu có bất kỳ câu hỏi hoặc yêu cầu hỗ trợ nào, vui lòng liên hệ với chúng tôi qua:</p>
                        <ul>
                            <li>Email: <a href='mailto:support@devnguyenstore.com'>support@devnguyenstore.com</a></li>
                            <li>Hotline: <strong>1900-1234</strong></li>
                        </ul>
                
                        <p>Chúng tôi rất mong được phục vụ bạn trong những lần mua sắm tiếp theo!</p>
                        
                        <p>Trân trọng,<br>
                        <strong>Đội ngũ DevNguyenStore</strong></p>
                    </div>
                
                    <div class='email-footer'>
                        <p>Bạn nhận được email này vì đã đặt hàng tại <strong>DevNguyenStore</strong>. Nếu đây không phải là bạn, vui lòng liên hệ ngay với chúng tôi để được hỗ trợ.</p>
                    </div>
                ";
                

                $mail->send();
                unset($_SESSION['cart']);
                unset($_SESSION['payment_status']); // Reset trạng thái thanh toán
                unset($_SESSION['diachi']); // Reset địa chỉ sau khi thanh toán

                header('Location: ../../index.php?quanly=camon');
                exit();
            } catch (Exception $e) {
                echo "Lỗi khi gửi email: {$mail->ErrorInfo}";
            }
        } else {
            echo "Lỗi khi thêm giỏ hàng: " . mysqli_error($mysqli);
            exit();
        }
    } else {
        echo "Giỏ hàng rỗng.";
        exit();
    }
}
?>
