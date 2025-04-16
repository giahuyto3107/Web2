<?php
include("../../../BackEnd/Config/config.php");

header('Content-Type: application/json');

// Truy vấn để lấy danh sách tài khoản không bị xóa (status_id != 6)
// Kết hợp với bảng user, role và status để lấy thông tin đầy đủ
$sql = "
    SELECT a.account_id, a.account_name, a.email, a.status_id AS account_status_id, a.role_id, 
           a.last_login, a.created_at AS account_created_at, a.updated_at AS account_updated_at,
           u.full_name, u.address, u.profile_picture, u.date_of_birth, 
           u.created_at AS user_created_at, u.updated_at AS user_updated_at,
           r.id AS role_id, r.role_name, r.role_description, r.status_id AS role_status_id,
           s.id AS status_id, s.status_name, s.status_description
    FROM account a 
    LEFT JOIN user u ON a.account_id = u.account_id 
    LEFT JOIN role r ON a.role_id = r.id
    LEFT JOIN status s ON a.status_id = s.id
    WHERE a.status_id != 6
";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi truy vấn dữ liệu: ' . mysqli_error($conn)]);
    exit;
}

$accounts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $accounts[] = [
        'account_id' => $row['account_id'],
        'account_name' => $row['account_name'],
        'email' => $row['email'],
        'account_status_id' => $row['account_status_id'],
        'role_id' => $row['role_id'],
        'last_login' => $row['last_login'],
        'account_created_at' => $row['account_created_at'],
        'account_updated_at' => $row['account_updated_at'],
        'full_name' => $row['full_name'],
        'address' => $row['address'], // Thêm trường address
        'profile_picture' => $row['profile_picture'],
        'date_of_birth' => $row['date_of_birth'],
        'user_created_at' => $row['user_created_at'],
        'user_updated_at' => $row['user_updated_at'],
        'role_name' => $row['role_name'],
        'role_description' => $row['role_description'],
        'role_status_id' => $row['role_status_id'],
        'status_name' => $row['status_name'],
        'status_description' => $row['status_description']
    ];
}

echo json_encode(['status' => 'success', 'data' => $accounts]);
mysqli_close($conn);
?>