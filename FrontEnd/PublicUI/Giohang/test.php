<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>MoMo Sandbox</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./statics/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css"/>
    <!-- CSS -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('https://cellphones.com.vn/sforum/wp-content/uploads/2021/07/Vi%CC%81-MOMO.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        /* Định dạng panel */
        .panel {
            background: rgba(0, 0, 0, 0.1); 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 20px;
        }

        .panel-heading {
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
        }

        /* Nút tùy chỉnh */
        .btn {
            font-size: 18px;
            padding: 15px;
            border-radius: 5px;
            transition: background-color 0.3s, box-shadow 0.3s;
            background-color: #e83e8c;
            color: #ffffff;
            border: none;
            width: 100%;
        }

        .btn:hover {
            background-color: #c03976;
            box-shadow: 0px 4px 15px rgba(232, 62, 140, 0.4);
        }

        /* Ẩn các trường input */
        input[type="text"] {
            display: none;
        }

        .form-group {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="panel panel-default">
                <!-- <div class="panel-heading">
                    <h3 class="panel-title">Chúng tôi đang khởi tạo thanh toán qua ATM vui lòng click button bên dưới để tiếp tục</h3>
                </div> -->
                <div class="panel-body">
                    <form class="" method="POST" target="_blank" enctype="application/x-www-form-urlencoded"
                          action="thanhtoanmomo.php">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">PartnerCode</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' name="partnerCode" value="<?php echo $partnerCode; ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">AccessKey</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' name="accessKey" value="<?php echo $accessKey;?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">SecretKey</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' name="secretKey" value="<?php echo $secretKey; ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">OrderId</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' name="orderId" value="<?php echo $orderId; ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">ExtraData</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' type="text" name="extraData" value="<?php echo $extraData?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">OrderInfo</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' name="orderInfo" value="<?php echo $orderInfo; ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">Amount</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' name="amount" value="<?php echo $amount; ?>" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">IpnUrl</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' name="ipnUrl" value="<?php echo $ipnUrl; ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fxRate" class="col-form-label">RedirectUrl</label>
                                    <div class='input-group date' id='fxRate'>
                                        <input type='text' name="redirectUrl" value="<?php echo $redirectUrl; ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p>
                        <div style="margin-top: 1em;">
                            <button type="submit" class="btn btn-primary btn-block">Start MoMo payment....</button>
                        </div>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript" src="./statics/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="./statics/moment/min/moment.min.js"></script>
</html>