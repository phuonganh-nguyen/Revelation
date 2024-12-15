<?php
// Kết nối cơ sở dữ liệu
include '../component/connect.php';

// Lấy user_id từ cookie (hoặc session)
if (isset($_COOKIE['khach_id'])) {
    $user_id = $_COOKIE['khach_id'];
} else {
    header('location:login.php');
    exit();
}

// Gọi script Python
$command = escapeshellcmd("python recommendation.py " . escapeshellarg($user_id));
$output = shell_exec($command);

// Kiểm tra kết quả trả về
if ($output === null) {
    echo "Không có kết quả từ script Python.";
} else {
    header('location: ../suggested_results.php');
}
?>

