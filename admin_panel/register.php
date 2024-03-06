<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    // if (isset($_POST['submit'])) {
    //     $name = mysqli_real_escape_string($conn, $_POST['name']);
    //     $email = mysqli_real_escape_string($conn, $_POST['email']);
    //     $password = ($_POST['pass']);
    //     $cpassword = ($_POST['cpass']);
    //     $user_type = $_POST['user_type'];

    //     $select_user = "SELECT * FROM user WHERE email='$email' && pass='password' ";
    //     $result = mysqli_query($conn, $select_user);

    //     if(mysqli_num_rows($result) > 0) {
    //         echo '<script>alert("Người dùng đã tồn tại");</script>';
    //     } else{
    //         if ($password != $cpassword) {
    //             echo '<script>alert("Mật khẩu xác nhận không khớp");</script>';
    //         } else {
    //             $insert = "INSERT INTO user (name, email, pass, user_type) VALUES ('$name', '$email', '$password', '$user_type')";
    //             mysqli_query($conn, $insert);
    //             header('location:login.php');
    //         }
    //     }
    // }

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

        $select_admin = $conn->prepare("SELECT* FROM `admin` WHERE email = ?");
        $select_admin->execute([$email]);
        
        if ($select_admin->rowCount() > 0) {
            $warning_msg[] = 'Email đã tồn tại!';
            // echo '<script>alert("Email đã tồn tại");</script>';
        } else {
            if($pass != $cpass) {
                $warning_msg[] = 'Mật khẩu xác nhận không khớp';
                // echo '<script>alert("Mật khẩu xác nhận không khớp");</script>';
            } else {
                $insert_admin = $conn->prepare("INSERT INTO `admin`(admin_id, name, email, pass) VALUES(?,?,?,?)");
                $insert_admin->execute([$id, $name, $email, $cpass]);
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
    <title>Secret Beauty - Admin registeration page</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <!--liên kết đến tệp CSS all.min.css của Font Awesome, được sử dụng để thêm các biểu tượng vào trang web.-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>Đăng ký Admin</h3>
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
                <a href="login.php">Đăng nhập ngay</a>
            </p>
            
        </form>      
    </div>
    

    <!--liên kết đến tệp JavaScript sweetalert.min.js, được sử dụng để hiển thị các thông báo cảnh báo.-->
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <!--liên kết đến tệp JavaScript script.js, được sử dụng để chứa mã JavaScript tùy chỉnh của trang web.-->
    <script src="../js/script.js"></script>

    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>