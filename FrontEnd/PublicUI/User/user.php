<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Thông tin cá nhân</title>
    <style>
        :root {
            --primary-color: #138086;
            --secondary-color: #534666;
            --background-color: #f8f9fa;
            --text-color: #2c3e50;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
        }

        .profile-nav {
            background: #fff;
            border-radius: 1rem;
            box-shadow: var(--shadow-md);
            padding: 20px;
            margin-bottom: 30px;
            transition: var(--transition);
        }

        .profile-nav:hover {
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .user-heading {
            text-align: center;
            padding: 2rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #0e6c70 100%);
            color: #fff;
            margin-bottom: 2rem;
        }

        .user-heading img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #fff;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .user-heading img:hover {
            transform: scale(1.1);
        }

        .nav-pills .nav-link {
            border-radius: 0.5rem;
            color: var(--text-color);
            padding: 0.75rem 1.25rem;
            margin: 0.25rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
        }

        .nav-pills .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 500;
            box-shadow: 0 4px 6px -1px rgba(19, 128, 134, 0.3);
        }

        .bio-graph-heading {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0e6c70 100%);
            color: #fff;
            text-align: center;
            padding: 1.5rem;
            border-radius: 1rem 1rem 0 0;
            margin-bottom: 0;
            font-weight: 600;
        }

        .bio-graph-info {
            background: #fff;
            border-radius: 0 0 1rem 1rem;
            box-shadow: var(--shadow-sm);
            padding: 2rem;
        }

        .bio-graph-info h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .bio-row {
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            gap: 1rem;
        }

        .bio-row:last-child {
            border-bottom: 0;
        }

        .bio-row strong {
            color: var(--primary-color);
            min-width: 120px;
            font-weight: 500;
        }

        .dont-login {
            background: #fff;
            padding: 4rem 2rem;
            border-radius: 1rem;
            box-shadow: var(--shadow-md);
            text-align: center;
            max-width: 500px;
            margin: 2rem auto;
        }

        .text_dont_login a {
            display: inline-block;
            background-color: var(--primary-color);
            color: #fff !important;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none !important;
            transition: var(--transition);
            margin-top: 1rem;
        }

        .text_dont_login a:hover {
            background-color: #0e6c70;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(19, 128, 134, 0.3);
        }

        @media (max-width: 768px) {
            .user-heading img {
                width: 100px;
                height: 100px;
            }

            .bio-row {
                flex-direction: column;
                gap: 0.5rem;
            }

            .nav-pills .nav-link {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="main_giohang">
            <div class="main_giohang_content">
                <?php  
                    include ('../../../BackEnd/Config/config.php');
                                    
                    $account_id = 1; 

                    $sql_user = "SELECT 
                                    u.full_name, 
                                    u.profile_picture, 
                                    a.email, 
                                    u.date_of_birth
                                FROM user u
                                INNER JOIN account a ON u.account_id = a.account_id
                                WHERE u.account_id = '$account_id'";
                    
                    $result_user = mysqli_query($conn, $sql_user);              
                    if ($row_user_data = mysqli_fetch_array($result_user)) {
                ?>

                <div class="row">
                    <div class="col-md-3">
                        <div class="profile-nav">
                            <div class="user-heading">
                                <img src="../../../BackEnd/Uploads/Profile Picture/<?php echo $row_user_data['profile_picture']; ?>" alt="">
                                <h1><?php echo $row_user_data['full_name']; ?></h1>
                                <p>ID: <?php echo $account_id; ?></p>
                            </div>
                            <ul class="nav nav-pills nav-stacked">
                                <li class="nav-item active"><a class="nav-link" href="#"> <i class="fas fa-user"></i> Profile</a></li>
                                <li class="nav-item"><a class="nav-link" href="index.php?quanly=suauser"> <i class="fas fa-edit"></i> Edit profile</a></li>
                                <li class="nav-item"><a class="nav-link" href="../../PublicUI/Lichsumuahang/listmuahang.php"> <i class="fas fa-bars"></i> Lịch sử mua hàng</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel">
                            <div class="bio-graph-heading">
                                Thông tin khách hàng
                            </div>
                            <div class="panel-body bio-graph-info">
                                <h1>Your Information</h1>
                                <div class="row">
                                    <div class="col-md-6 bio-row">
                                        <p><strong>Tên đầy đủ:</strong> <?php echo $row_user_data['full_name']; ?></p>
                                    </div>
                                    <div class="col-md-6 bio-row">
                                        <p><strong>Email:</strong> <?php echo $row_user_data['email']; ?></p>
                                    </div>
                                    <div class="col-md-6 bio-row">
                                        <p><strong>Ngày sinh:</strong> <?php echo $row_user_data['date_of_birth']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php  
                    } else { 
                ?>
                    <div class="dont-login text-center">
                        <img src="" alt="" class="img-fluid">
                        <div class="text_dont_login"><a href="">Đăng Nhập ngay bây giờ</a></div>
                    </div>
                <?php  
                    } 
                ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>