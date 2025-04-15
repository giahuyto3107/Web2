<?php
include ('../../../BackEnd/Config/config.php');
session_start();

?> 
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
    --primary-color: #000000; /* Đen */
    --secondary-color: #ffffff; /* Trắng */
    --background-color: #ffffff; /* Nền trắng */
    --text-color: #000000; /* Chữ đen */
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
    background: var(--secondary-color);
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
    background: var(--primary-color);
    color: var(--secondary-color);
    margin-bottom: 2rem;
}

.user-heading img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid var(--secondary-color);
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
    background-color: #00000020; /* Đen nhạt */
    color: var(--primary-color);
    transform: translateX(5px);
}

.nav-pills .nav-link.active {
    background-color: var(--primary-color);
    color: var(--secondary-color);
    font-weight: 500;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
}

.bio-graph-heading {
    background: var(--primary-color);
    color: var(--secondary-color);
    text-align: center;
    padding: 1.5rem;
    border-radius: 1rem 1rem 0 0;
    margin-bottom: 0;
    font-weight: 600;
}

.bio-graph-info {
    background: var(--secondary-color);
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
    border-bottom: 2px solid var(--primary-color);
}

.bio-row {
    padding: 1rem 0;
    border-bottom: 1px solid #00000020; /* Đen nhạt */
    display: flex;
    gap: 1rem;
}

/* .bio-row:last-child {
    border-bottom: 0;
} */

.bio-row strong {
    color: var(--primary-color);
    min-width: 120px;
    font-weight: 500;
}

.dont-login {
    background: var(--secondary-color);
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
    color: var(--secondary-color);
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: var(--transition);
    margin-top: 1rem;
}

.text_dont_login a:hover {
    background-color: #333333; /* Đen đậm hơn */
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
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

.chart-container {
    width: 80%;
    max-width: 600px;
    margin: 20px auto;
}

/* Modal Styles */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.6);
}

.modal-dialog {
    max-width: 500px;
}

.modal-content {
    border-radius: 15px;
    border: none;
    overflow: hidden;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    background: var(--primary-color);
    color: var(--secondary-color);
    border-bottom: 2px solid var(--secondary-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
}

.modal-header h5 {
    font-size: 18px;
    font-weight: bold;
}

.modal-header .close {
    color: var(--secondary-color);
    font-size: 24px;
    opacity: 0.8;
    transition: 0.3s;
}

.modal-header .close:hover {
    opacity: 1;
    transform: rotate(90deg);
}

.modal-body {
    padding: 25px;
    background-color: var(--secondary-color);
}

.modal-body .form-group {
    margin-bottom: 15px;
}

.modal-body .form-group label {
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 5px;
    display: block;
}

.modal-body .form-control {
    border-radius: 10px;
    border: 1px solid var(--primary-color);
    padding: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    background-color: var(--secondary-color);
}

.modal-body .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
}

.modal-body .row {
    display: flex;
    gap: 10px;
}

.modal-body .col {
    flex: 1;
}

.modal-footer {
    padding: 15px;
    background-color: var(--secondary-color);
    border-top: 2px solid var(--primary-color);
}

.modal-footer .btn-primary {
    background: var(--primary-color);
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-size: 16px;
    width: 100%;
    transition: all 0.3s ease-in-out;
    color: var(--secondary-color);
    font-weight: bold;
}

.modal-footer .btn-primary:hover {
    background: #333333; /* Đen đậm hơn */
    transform: scale(1.05);
}

.btn-primary:disabled {
    background: #666666;
    cursor: not-allowed;
    opacity: 0.6;
}

@media (max-width: 576px) {
    .modal-dialog {
        max-width: 90%;
    }
    .modal-body .row {
        flex-direction: column;
    }
}

.stats-container{
    margin-top: 20px;
    padding: 20px;
}

    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="main_giohang">
            <div class="main_giohang_content">
                <?php  
                    include ('../../../BackEnd/Config/config.php');
                    
                    $account_id = $_SESSION['user_id']; 

                    $sql_user = "SELECT 
                                    u.full_name, 
                                    u.profile_picture, 
                                    a.email, 
                                    u.date_of_birth,
                                    u.address
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
                            </div>
                            <ul class="nav nav-pills nav-stacked">
                                <li class="nav-item active"><a class="nav-link" href="#"> <i class="fas fa-user"></i> Profile</a></li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-toggle="modal" data-target="#editProfileModal">
                                        <i class="fas fa-edit"></i> Edit profile
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="?page=orders" data-page="orders">
                                        <i class="fa-solid fa-box"></i> Lịch sử mua hàng
                                    </a>
                                </li>
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
                                    <div class="col-md-6 bio-row">
                                        <p><strong>Địa chỉ:</strong> <?php echo $row_user_data['address']; ?></p>
                                    </div>
                                </div>
                            </div>

                            <?php  
                                $sql_stats = "SELECT 
                                                SUM(oi.quantity) AS total_products,
                                                SUM(o.total_amount) AS total_spent
                                            FROM orders o
                                            LEFT JOIN order_items oi ON o.order_id = oi.order_id
                                            WHERE o.user_id = '$account_id' AND o.status_id = 5";
                                $result_stats = mysqli_query($conn, $sql_stats);
                                $stats = mysqli_fetch_assoc($result_stats);

                                $total_products = $stats['total_products'] ?? 0; 
                                $total_spent = $stats['total_spent'] ?? 0;
                            ?>

                            <div class="stats-container">
                                <h1 style="text-align: center;">Thống kê mua sắm</h1>
                                <div class="row">
                                    <div class="col-md-6 bio-row">
                                        <p><strong>Tổng đơn hàng:</strong> <?= number_format($total_products, 0, ',', '.') ?></p>
                                    </div>
                                    <div class="col-md-6 bio-row">
                                        <p><strong>Tổng số tiền đã chi:</strong> <?= number_format($total_spent, 0, ',', '.') ?> đ</p>
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


    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileLabel">Chỉnh sửa thông tin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
    <form id="editProfileForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="fullName">Tên đầy đủ:</label>
            <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo $row_user_data['full_name']; ?>" required>
            <span class="error-message text-danger"></span>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $row_user_data['email']; ?>" required>
            <span class="error-message text-danger"></span>
        </div>
        <div class="form-group">
            <label for="dob">Ngày sinh:</label>
            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $row_user_data['date_of_birth']; ?>" required>
            <span class="error-message text-danger"></span>
        </div>
        <div class="form-group">
            <label for="address">Địa chỉ:</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $row_user_data['address']; ?>" required>
            <span class="error-message text-danger"></span>
        </div>
        <div class="form-group">
            <label for="profilePicture">Ảnh đại diện:</label>
            <input type="file" class="form-control" id="profilePicture" name="profilePicture" accept="image/*">
            <span class="error-message text-danger"></span>
        </div>

        <div id="errorContainer" class="mt-2"></div>

        <button type="submit" id="saveChanges" class="btn btn-primary w-100" disabled>💾 Lưu thay đổi</button>
    </form>
</div>
        </div>
    </div>
</div>

    
    <style>
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.6);
        }


        .modal-dialog {
            max-width: 500px;
        }
        .modal-content {
            border-radius: 15px;
            border: none;
            overflow: hidden;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease-in-out;
        }

        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0e6c70 100%);
            color: white;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }
        .modal-header h5 {
            font-size: 18px;
            font-weight: bold;
        }

        .modal-header .close {
            color: white;
            font-size: 24px;
            opacity: 0.8;
            transition: 0.3s;
        }
        .modal-header .close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 25px;
            background-color: #f4f4f4;
        }

        .modal-body .form-group {
            margin-bottom: 15px;
        }
        .modal-body .form-group label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }
        .modal-body .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: white;
        }

        .modal-body .form-control:focus {
            border-color: #0e6c70;
            box-shadow: 0 0 8px rgba(14, 108, 112, 0.3);
        }

        .modal-body .row {
            display: flex;
            gap: 10px;
        }
        .modal-body .col {
            flex: 1;
        }

        .modal-footer {
            padding: 15px;
            background-color: #e8e8e8;
            border-top: 2px solid rgba(0, 0, 0, 0.1);
        }
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0e6c70 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease-in-out;
            color: white;
            font-weight: bold;
        }
        .modal-footer .btn-primary:hover {
            background: linear-gradient(135deg, #0e6c70 0%, var(--primary-color) 100%);
            transform: scale(1.05);
        }

        .btn-primary:disabled {
            background: #ccc !important; 
            cursor: not-allowed; 
            opacity: 0.6; 
        }

        @media (max-width: 576px) {
            .modal-dialog {
                max-width: 90%;
            }
            .modal-body .row {
                flex-direction: column;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
    $(document).ready(function () {
        let fullName = $("#fullName");
        let email = $("#email");
        let dob = $("#dob");
        let saveBtn = $("#saveChanges");
        let address = $("#address");
        let profilePicture = $("#profilePicture");
        let errorContainer = $("#errorContainer"); 

        function validateForm() {
            let isValid = true;

            let fullNameVal = $("#fullName").val().trim();
            let emailVal = $("#email").val().trim();
            let dobVal = $("#dob").val().trim(); 
            let addressVal = $("#address").val().trim();

            let nameRegex = /^[a-zA-ZÀ-Ỹà-ỹ\s]{3,50}$/;
            let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            let dobRegex = /^\d{4}-\d{2}-\d{2}$/;

            let imageRegex = /\.(jpg|jpeg|png|gif)$/i;
            let allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            let maxFileSize = 5 * 1024 * 1024;

            if (!nameRegex.test(fullNameVal)) {
                $("#fullName").next(".error-message").text("❌ Họ tên không hợp lệ (3-50 chữ).");
                isValid = false;
            } else {
                $("#fullName").next(".error-message").text(""); 
            }

            if (!emailRegex.test(emailVal)) {
                $("#email").next(".error-message").text("❌ Email không hợp lệ!");
                isValid = false;
            } else {
                $("#email").next(".error-message").text("");
            }

            let today = new Date().toISOString().split("T")[0]; 
            
            if (!dobRegex.test(dobVal) || dobVal >= today) {
                $("#dob").next(".error-message").text("❌ Ngày sinh không hợp lệ hoặc lớn hơn hôm nay!");
                isValid = false;
            } else {
                $("#dob").next(".error-message").text("");
            }

            

            $("#saveChanges").prop("disabled", !isValid);
        }

        $("#editProfileForm input").on("input", function () {
            validateForm();
        });

        $("#editProfileForm").on("submit", function (e) {
            e.preventDefault();


            let formData = new FormData();


            let fullName = $("#fullName").val().trim(); 
            let email = $("#email").val().trim();
            let dob = $("#dob").val().trim();
            let address = $("#address").val().trim();
            let profilePicture = $("#profilePicture")[0].files[0]; 


            formData.append("fullName", fullName);
            formData.append("email", email);
            formData.append("dob", dob);
            formData.append("address", address);
            if (profilePicture) {
                formData.append("profilePicture", profilePicture);
            }

            $.ajax({
                url: "http://localhost/Web2/FrontEnd/PublicUI/User/update_profile.php",
                type: "POST",
                data: formData,
                contentType: false, // Không tự set Content-Type
                processData: false, // Không serialize dữ liệu
                success: function (response) {
                    console.log("Server response:", response);
                    try {
                        let result = JSON.parse(response);
                        if (result.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "Thành công!",
                                text: "Thông tin của bạn đã được cập nhật.",
                                confirmButtonText: "OK",
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Lỗi!",
                                text: result.message,
                                confirmButtonText: "Thử lại",
                            });
                        }
                    } catch (e) {
                        console.error("Lỗi parse JSON:", e);
                        Swal.fire({
                            icon: "error",
                            title: "Lỗi!",
                            text: "Phản hồi từ server không hợp lệ.",
                            confirmButtonText: "OK",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", status, error);
                    Swal.fire({
                        icon: "error",
                        title: "Lỗi hệ thống!",
                        text: "Không thể gửi dữ liệu, vui lòng thử lại sau.",
                        confirmButtonText: "OK",
                    });
                },
            });
        });

    });
</script>

</body>
</html>