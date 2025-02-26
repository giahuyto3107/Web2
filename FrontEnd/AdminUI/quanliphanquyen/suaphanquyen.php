<?php
    $permission_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($permission_id == 0) {
        die("Invalid permission_id.");
    }

    $sql_suaPQ = "  SELECT p.permission_id,
                    p.permission_name,
                    p.permission_description,
                    st.status_name
                    from permission p
                    join status st on st.id = p.status_id
                    WHERE p.permission_id = ?";

    $stmt = $conn->prepare($sql_suaPQ);
    $stmt->bind_param("i", $permission_id);
    $stmt->execute();
    $query_PQ = $stmt->get_result();

    if (!$query_PQ) {
        die("Query failed: " . $stmt->error);
    }
?>

<div class="form">
    <div class="form-title">
        <h2>Sửa phân quyền</h2>
    </div>

    <?php
        $nameErr = $descriptionErr = "";
        $id = ""; //Store value for later updating
    ?>

    <form id="form-update" method="POST" enctype="multipart/form-data">
        <div class="form-content">
            <?php while ($row = mysqli_fetch_array($query_PQ)) { ?>
            
            <!-- ID -->
            <input name="id" type="hidden" value="<?= $row['permission_id'] ?>"> 

            <h3>Tên phân quyền</h3>
            <input id="name" name="name" type="text" value="<?= $row['permission_name'] ?>">
            <span id="nameErr" class="error">*</span>
            <br>

            <h3>Mô tả</h3>
            <input id="description" name="description" type="text" value="<?= $row['permission_description'] ?>">
            <span id="descriptionErr" class="error">*</span>
            <br>

                        
            <!-- Hidden input to store status -->
            <input type="hidden" name="status" id="statusInput" value="2">

            <h3>Trạng thái</h3>
            <label class="switch">
                <input type="checkbox" id="statusCheckbox" <?= ($row['status_name'] == 'Active') ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <br><br><br>

            <button type="button" id="updateBtn" class="suataikhoan">Sửa thông tin</button>

            <?php } ?>
        </div>
    </form>

    <script>
        // Handle status checkbox
        document.getElementById("statusCheckbox").addEventListener("change", function() {
            document.getElementById("statusInput").value = this.checked ? 1 : 2;
        });

        document.getElementById("updateBtn").addEventListener("click", function (event) {
            event.preventDefault();
            document.getElementById("statusInput").value = document.getElementById("statusCheckbox").checked ? 1 : 2;
            let isValid = true;

            let name = testInput(document.getElementById("name").value);
            let description = testInput(document.getElementById("description").value);
            let id = <?= json_encode($permission_id) ?>;  //Convert php into js variable

            // Reset error messages
            document.getElementById("nameErr").innerText = "*";
            document.getElementById("descriptionErr").innerText = "*";

            // Validation
            if (name === "") {
                document.getElementById("nameErr").innerText = "* Tên phân quyền không được để trống.";
                isValid = false;
            }
            if (description === "") {
                document.getElementById("descriptionErr").innerText = "* Nội dung mô tả không được để trống.";
                isValid = false;
            }
            
            if (isValid) {
                let formData = new FormData(document.getElementById("form-update"));
                let statusValue = document.getElementById("statusInput").value;

                fetch("../../BackEnd/Model/quanliphanquyen/xuliphanquyen.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    window.location.href = `index.php?action=quanliphanquyen&query=sua&id=${id}`;

                })
                .catch(error => console.error("Lỗi:", error));
            }
        });

        function testInput(data) {
            data = data.trim(); // Remove leading and trailing spaces
            data = data.replace(/['"\\]/g, ""); // Remove quotes and backslashes to prevent injection
            return data;
        }
    </script>
</div>

<style>

    body {
            background-color: #f8f9fa;
            display: flex;
            width: 100%;
            
            /* min-height: 100vh; */
            padding: 20px;
        }
    
    .form {
        width: 100%; /* Adjust as needed */
        max-width: 600px; /* Prevents excessive expansion */
        margin: 0;
        background: white;
    
        padding: 50px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .form-title {
        text-align: center;
        width: 100%;
    }

    .form-content {
        background-color: white;
        border-radius: 20px;
        width: auto;
        max-width: 100%;
        /* margin: 10px; */
        /* padding: 20px; */
        text-align: center;
    }

    .form-content input {
        text-align: center;
    }

    table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
    }
    tr, th {
        border: 1px solid #ccc;
        
    }

    th {
        background-color: #f2f2f2;
    }

    td {
        background-color: white;
        text-align: center;
    }

    .active, .inactive, .edit {
        display: inline-block;        /* Để có thể áp dụng padding và border */
        padding: 5px 10px;           /* Khoảng cách bên trong */
        text-decoration: none;        /* Bỏ gạch chân */
        color: white;                 /* Màu chữ trắng */
        border: 1px solid black;      /* Khung bên ngoài màu đen */
        border-radius: 15px;          /* Bo góc nhẹ */
        transition: background-color 0.3s; /* Hiệu ứng chuyển màu nền */
        padding: 6px 12px;
    }

    .inactive, .active, .edit {
        cursor: pointer;
    }

    .active:hover, .inactive:hover {
        opacity: 0.8;                /* Hiệu ứng giảm độ trong suốt khi hover */
    }

    .active {
        background: #28a745; /* Green for approved */
        color: white;
    }

    .active:hover {
        background: #218838;
    }

    .inactive {
        background-color: #008cba;   /* Màu xanh lam cho khôi phục */
    }

    .edit {
        /* border: 1px black solid; */
        background: #007bff; /* Gray */
        color: white;
        font-weight: bold;
    }

    .edit:hover {
        background: #0056b3;
    }

    .suataikhoan {
        background-color: #4CAF50;
        width: 325px;
        height: 50px;
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
    }

    .suataikhoan:hover {
        background-color: #45a049;
    }

    .error {
        color: red;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }

    /* Set width for input field */
    /* input {
        
    } */

    input:checked + .slider {
    background-color: #2196F3;
    }

    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }

    .slider.round:before {
    border-radius: 50%;
    }
</style>