<?php 
    include 'component/connect.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = 'location:login.php';
    }

    if (isset($_POST['submit'])) {
        $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id=? LIMIT 1");
        $select_user->execute([$user_id]);
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

        $prev_pass = $fetch_user['pass'];
        //password
        $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'; //giá trị băm của chuỗi rỗng
        $newpass = $_POST['newpass'];
        $cpass = $_POST['cpass'];


        if (!empty($oldpass) || !empty($newpass) || !empty($cpass)) {
            if ($newpass != $cpass) {
                $warning_msg[] = 'Mật khẩu xác nhận không khớp';
            } else {
                $update_pass = $conn->prepare("UPDATE `user` SET pass=? WHERE user_id=?");
                $update_pass->execute([$cpass, $user_id]);
                $success_msg[] = 'Mật khẩu được cập nhật thành công';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Reset password</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <section class="form-container" style="margin-top: -10rem; margin-bottom: -5rem;">
            <div class="heading" style="margin-bottom: 1rem;">
                <h1>Đặt lại mật khẩu</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <form action="" method="post" enctype="multipart/form-data" class="register">
                <div class="flex">
                    <div class="col">
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