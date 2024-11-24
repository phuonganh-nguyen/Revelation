<?php 
    include 'component/connect.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id - $_COOKIE['khach_id'];
    } else{
        $user_id = '';
    }

    if (isset($_POST['submit'])) {
        $id = userid();
        $name = $_POST['name']; 
        
        $email = $_POST['email'];
        //$pass = sha1($_POST['pass']);//mã hóa mật khẩu
        // $pass = filter_var($pass, FILTER_SANITIZE_STRING);
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];

        $select_user = $conn->prepare("SELECT* FROM `user` WHERE email = ?");
        $select_user->execute([$email]);
        
        if ($select_user->rowCount() > 0) {
            $warning_msg[] = 'Email đã tồn tại!';
            // echo '<script>alert("Email đã tồn tại");</script>';
        } else {
            if ($pass != $cpass) {
                $warning_msg[] = 'Mật khẩu xác nhận không khớp';
            } else {
                // Kiểm tra mật khẩu
                if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/', $pass)) {
                    $warning_msg[] = 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm số và ký tự viết hoa.';
                } else {
                        // Tạo token ngẫu nhiên
                    $token = mt_rand(100000, 999999);

                    $insert_user = $conn->prepare("INSERT INTO `user`(user_id, name, email, pass, vaitro, token) VALUES(?,?,?,?,?,?)");
                    $insert_user->execute([$id, $name, $email, $cpass, 'khach', $token]);

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
                        $mail->Subject = 'Xác nhận đăng ký tài khoản';
                        $mail->Body    = '<p>Chào ' . htmlspecialchars($name) . ', cảm ơn bạn đã đăng ký tài khoản. Mã xác nhận của bạn là: <strong>' . $token . '</strong></p>';
                        $mail->Body .= '<p><a href="http://localhost/web/verified.php?email=' . urlencode($email) . '">Bấm vào liên kết này để nhập mã xác nhận tài khoản</a></p>';

                        $mail->send();
                        header("Location: verified.php?email=" . urlencode($email));
                        exit;
                    } catch (Exception $e) {
                        $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - User registration page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="login-page">
    <?php include 'component/user_header.php' ?>
    <video autoplay muted loop id="bg-video">
        <source src="images/final.mp4" type="video/mp4">
        Trình duyệt của bạn không hỗ trợ video.
    </video>
    <div class="overlay"></div>
    <div class="form-container" style="margin-top: -4rem;">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>Đăng ký</h3>
            <!-- <div class="flex"> -->
                <div class="input-field">
                    <p>Họ và Tên<span>*</span></p>
                    <input type="text" name="name" placeholder="Nhập họ và tên" maxlength="50" require class="box">
                </div>
                <div class="input-field">
                    <p>Email<span>*</span></p>
                    <input type="email" name="email" placeholder="Nhập email" maxlength="50" require class="box">
                </div>
                <div class="input-field">
                    <p>Mật khẩu<span>*</span></p>
                    <div class="password-wrapper">
                        <input type="password" name="pass" placeholder="Nhập mật khẩu" maxlength="50" required class="box" id="password-input">                            <span class="toggle-password" onclick="togglePassword()">
                            <i class="bi bi-eye-fill"></i>
                            </span>
                    </div>
                </div>
                <div class="input-field">
                    <p>Nhập lại mật khẩu<span>*</span></p>
                    <div class="password-wrapper">
                        <input type="password" name="cpass" placeholder="Nhập lại mật khẩu" maxlength="50" required class="box" id="confirm-password-input">                            <span class="toggle-password" onclick="togglePassword('confirm-password-input')">
                            <i class="bi bi-eye-fill"></i>
                        </span>
                    </div>
                </div>
            
            <input type="submit" name="submit" value="Đăng ký" class="btn">
            <p class="link">
                Bạn đã có tài khoản?
                <a href="login.php">Đăng nhập ngay</a>
            </p>
            
        </form>      
    </div>
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