<?php 
    include 'component/connect.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }

    if (isset($_POST['submit'])) {
        $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id=? LIMIT 1");
        $select_user->execute([$user_id]);
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

        $prev_pass = $fetch_user['pass'];
        $name = $_POST['name'];
        $email = $_POST['email'];

        //name
        if (!empty($name)) {
            $update_name = $conn->prepare("UPDATE `user` SET name=? WHERE user_id=?");
            $update_name->execute([$name, $user_id]);
            $success_msg[] = 'Tên được cập nhật thành công';
        }

        //email
        if (!empty($email)) {
            $select_email = $conn->prepare("SELECT * FROM `user` WHERE user_id=? AND email=?");
            $select_email->execute([$user_id, $email]);
            if($select_email->rowCount()>0) {
                $warning_msg[] = 'Email đã tồn tại';
            } else {
                $update_email = $conn->prepare("UPDATE `user` SET email=? WHERE user_id=?");
                $update_email->execute([$email, $user_id]);
                $success_msg[] = 'Email được cập nhật thành công';
            }
            
        }

        //password
        $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'; //giá trị băm của chuỗi rỗng
        $oldpass = $_POST['oldpass'];
        $newpass = $_POST['newpass'];
        $cpass = $_POST['cpass'];


        if (!empty($oldpass) || !empty($newpass) || !empty($cpass)) {
            if ($oldpass != $prev_pass) {
                $warning_msg[] = 'Mật khẩu hiện tại không khớp';
            } elseif ($newpass != $cpass) {
                $warning_msg[] = 'Mật khẩu xác nhận không khớp';
            } else {
                $update_pass = $conn->prepare("UPDATE `user` SET pass=? WHERE user_id=?");
                $update_pass->execute([$cpass, $user_id]);
                $success_msg[] = 'Mật khẩu được cập nhật thành công';
            }
        }
    }
    if (isset($_POST['forgot'])) {
        $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id=? LIMIT 1");
        $select_user->execute([$user_id]);
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
        $email = $fetch_user['email'];
        $name = $fetch_user['name'];
        // Tạo token ngẫu nhiên
        $token = mt_rand(100000, 999999);
        $update_code = $conn->prepare("UPDATE `user` SET ma_quen_mk=? WHERE user_id=?");
        $update_code->execute([$token, $user_id]);
        // Gửi email xác nhận
        $mail = new PHPMailer(true);
            try {
                // Cấu hình máy chủ
                $mail->isSMTP();                                            // Set mailer to use SMTP
                $mail->Host       = 'smtp.gmail.com';                   // Specify main and backup SMTP servers
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'loverevelationshop@gmail.com';             // SMTP username
                $mail->Password   = 'g u w j x u t f u p j m c z r p';             // SMTP passwordd
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption, `ssl` also accepted
                $mail->Port       = 587;                                   // TCP port to connect to
                // Nội dung email
                $mail->setFrom('loverevelationshop@gmail.com', 'RÉVÉLATION');
                $mail->addAddress($email, htmlspecialchars($name));                          // Add a recipient
                $mail->isHTML(true);                                       // Set email format to HTML
                $mail->CharSet = 'UTF-8';                                  // Đặt mã hóa thành UTF-8
                $mail->Subject = 'Đặt lại mật khẩu';
                $mail->Body    = '<p>Chào ' . htmlspecialchars($name) . ', mã xác nhận của bạn là: <strong>' . $token . '</strong></p>';
                $mail->Body .= '<p><a href="http://localhost/web/enter-code.php?email=' . urlencode($user_id) . '">Bấm vào liên kết này để nhập mã xác nhận</a></p>';

                $mail->send();
                header("Location: enter_code.php?email=" . urlencode($user_id));
                exit;
            } catch (Exception $e) {
                $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
            }
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Update profile page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <section class="form-container" style="margin-top: -5rem;">
            <div class="heading">
                <h1>Sửa hồ sơ</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <form action="" method="post" enctype="multipart/form-data" class="register">
                <div class="flex">
                    <div class="col">
                        <div class="input-field">
                            <p>Họ và tên</p>
                            <input type="text" name="name" style="text-transform: capitalize;" placeholder="<?= $fetch_profile['name']; ?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Email</p>
                            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Mật khẩu hiện tại</p>
                            <div class="password-wrapper">
                                <input type="password" name="oldpass" placeholder="Nhập mật khẩu hiện tại" class="box" id="password-input">                            <span class="toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye-fill"></i>
                            </div>
                        </div>
                        <div class="input-field">
                            <p>Mật khẩu mới</p>
                            <div class="password-wrapper">
                                <input type="password" name="newpass" placeholder="Nhập mật khẩu mới" class="box"id="password-input">                            <span class="toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye-fill"></i>
                            </div>
                        </div>
                        <div class="input-field">
                            <p>Nhập lại mật khẩu mới</p>
                            <div class="password-wrapper">
                                <input type="password" name="cpass" placeholder="Nhập mật lại khẩu mới" class="box"id="password-input">                            <span class="toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye-fill"></i>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <p class="link">
                    <button type="submit" name="forgot">Quên mật khẩu</button>
                </p>
                <input type="submit" name="submit" value="Cập nhật" class="btn">
            </form>
            
        </section>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId || 'password-input');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
        }

    </script>



    <!--liên kết đến tệp JavaScript sweetalert.min.js, được sử dụng để hiển thị các thông báo cảnh báo.-->
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <!--liên kết đến tệp JavaScript script.js, được sử dụng để chứa mã JavaScript tùy chỉnh của trang web.-->

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>