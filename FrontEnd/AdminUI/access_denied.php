<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin.css">
    
    <style>
        .access-denied-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 80vh;
            text-align: center;
        }
        
        .access-denied-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }
        
        .access-denied-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .access-denied-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
        }
        
        .module-name {
            font-weight: bold;
            color: #0d6efd;
        }
        
        .action-name {
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="index.php">
                                <i class="fas fa-home me-2"></i>
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="index.php">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Dashboard
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="access-denied-container">
                    <i class="fas fa-exclamation-triangle access-denied-icon"></i>
                    <h1 class="access-denied-title">Access Denied</h1>
                    
                    <?php
                    // Get module and action from URL parameters
                    $module = isset($_GET['module']) ? htmlspecialchars($_GET['module']) : '';
                    $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
                    
                    // Display specific message based on what was denied
                    if (!empty($module) && !empty($action)) {
                        echo '<p class="access-denied-message">';
                        echo 'Bạn không có quyền thực hiện hành động <span class="action-name">' . $action . '</span> trong module <span class="module-name">' . $module . '</span>.';
                        echo '</p>';
                    } elseif (!empty($module)) {
                        echo '<p class="access-denied-message">';
                        echo 'Bạn không có quyền truy cập vào module <span class="module-name">' . $module . '</span>.';
                        echo '</p>';
                    } else {
                        echo '<p class="access-denied-message">';
                        echo 'Bạn không có quyền truy cập vào trang này. Vui lòng liên hệ với quản trị viên nếu bạn nghĩ đây là lỗi.';
                        echo '</p>';
                    }
                    ?>
                    
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Return to Dashboard
                    </a>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 