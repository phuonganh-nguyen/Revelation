<?php
    include 'component/connect.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = '';
    }

    $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id=? LIMIT 1");
    $select_user->execute([$user_id]);
    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
    $email = $fetch_user['email'];


    if (isset($_POST['verify'])) {
        $token = $_POST['token'] ?? ''; // Lấy mã xác nhận từ người dùng
    
        if (empty($token)) {
            $warning_msg[] = 'Vui lòng nhập mã xác nhận';
        } else {
            // Kiểm tra mã xác nhận trong cơ sở dữ liệu
            $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id = ? AND ma_quen_mk = ?"); // Sử dụng cột ma_quen_mk để kiểm tra
            $select_user->execute([$user_id, $token]);

            if ($select_user->rowCount() > 0) {
                // Cập nhật trạng thái tài khoản là đã xác nhận
                $update_user = $conn->prepare("UPDATE `user` SET xacnhan = ? WHERE user_id = ?");
                $update_user->execute(['1', $user_id]);
                header('Location: reset-password.php'); // Chuyển hướng sang trang đặt lại mật khẩu
                exit;
            } else {
                $warning_msg[] = 'Mã xác nhận không đúng! Vui lòng kiểm tra lại';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Tài Khoản</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <div class="form-container" style="margin-top: -12rem; margin-bottom: -10rem;">
        <form action="" method="post" class="verify">
            <h3>Xác nhận tài khoản</h3>
            <?php
                
            ?>
            <div class="input-field">
                <p style="font-size: 1.1rem;">Vui lòng nhập mã xác nhận vừa được gửi về email <strong><?= htmlspecialchars($email); ?></strong> <span>*</span></p>
                <input type="text" name="token" placeholder="Nhập mã xác nhận" maxlength="6" required class="box">
            </div>
            <input type="submit" name="verify" value="Xác Nhận" class="btn">
        </form>
    </div>

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>
