<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product Management</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <link rel="stylesheet" href="css/data-table.css" />
</head>
<body>
    <?php
    session_start();

    
    if (isset($_SESSION['user_id'])) {
        // User is logged in, include the necessary files
        include 'menu.php'; 
        include '../../BackEnd/Config/config.php';
        include 'chuyenhuong.php';
    } else {
        // User is not logged in, show login form
        include 'login signup/login.php';
    }
    ?>
    <!-- <script src="js/dashboard.js" type="module"></script>
    <script src="js/dashboard-products.js" type="module"></script>
    <script src="js/sidebar.js"></script> -->
    
    <!-- Include module permission script -->
    <script src="js/module_permission.js"></script>
</body>
</html>