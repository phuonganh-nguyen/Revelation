<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id - $_COOKIE['user_id'];
    } else{
        $user_id = '';
    }

    if (isset($_POST['submit'])) {
        $id = unique_id();
        $name = $_POST['name'];
        // $name = filter_var($name, FILTER_SANITIZE_STRING);
        
        $email = $_POST['email'];
        // $email = filter_var($email, FILTER_SANITIZE_STRING);

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
            if($pass != $cpass) {
                $warning_msg[] = 'Mật khẩu xác nhận không khớp';
                // echo '<script>alert("Mật khẩu xác nhận không khớp");</script>';
            } else {
                $insert_user = $conn->prepare("INSERT INTO `user`(user_id, name, email, pass) VALUES(?,?,?,?)");
                $insert_user->execute([$id, $name, $email, $cpass]);
                $success_msg[] = 'Đăng ký thành công! Vui lòng đăng nhập ngay';
                // header('location:login.php');
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - User registration page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <div class="form-container">
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
                    <input type="password" name="pass" placeholder="Nhập mật khẩu" maxlength="50" require class="box">
                </div>
                <div class="input-field">
                    <p>Nhập lại mật khẩu<span>*</span></p>
                    <input type="password" name="cpass" placeholder="Nhập lại mật khẩu" maxlength="50" require class="box">
                </div>
                
                <!-- <div class="input-field">
                    <p>User type<span>*</span></p>
                    <select name="user_type">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div> -->
            <!-- </div> -->
            
            <input type="submit" name="submit" value="Đăng ký" class="btn">
            <p class="link">
                Bạn đã có tài khoản?
                <a href="user_login.php">Đăng nhập ngay</a>
            </p>
            
        </form>      
    </div>
    

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