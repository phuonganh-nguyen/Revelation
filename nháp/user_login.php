<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
    }

    if (isset($_POST['submit'])) { //ktra ng dùng đã ấn submit hay chưa
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        
        //kiểm tra xem tài khoản người dùng có tồn tại trong cơ sở dữ liệu
        $select_user = $conn->prepare("SELECT* FROM `user` WHERE email = ? AND pass = ?");
        $select_user->execute([$email, $pass]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);

        if ($select_user->rowCount() > 0) { //kiểm tra xem có bất kỳ tài khoản người dùng nào được tìm thấy hay không
            //tạo một cookie có tên userid và giá trị là ID của người dùng. Cookie này sẽ hết hạn sau 30 ngày.
            setcookie('khach_id', $row['khach_id'], time() + 60*60*24*5, '/');
            header('location:home.php');
        } else {
            $warning_msg[] = 'Email hoặc mật khẩu không đúng';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - User login page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Đăng nhập</h3>
                <div class="input-field">
                    <p>Email<span>*</span></p>
                    <input type="email" name="email" placeholder="Nhập email" maxlength="50" require class="box">
                </div>
                <div class="input-field">
                    <p>Mật khẩu<span>*</span></p>
                    <input type="password" name="pass" placeholder="Nhập mật khẩu" maxlength="50" require class="box">
                </div>
                
            <input type="submit" name="submit" value="Đăng nhập" class="btn">
            <p class="link">
                Bạn đã chưa có tài khoản?
                <a href="user_register.php">Đăng ký ngay</a>
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