<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';

    if (isset($_POST['submit'])) { //ktra ng dùng đã ấn submit hay chưa
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        
        //kiểm tra xem tài khoản người dùng có tồn tại trong cơ sở dữ liệu
        $select_admin = $conn->prepare("SELECT* FROM `admin` WHERE email = ? AND pass = ?");
        $select_admin->execute([$email, $pass]);
        $row = $select_admin->fetch(PDO::FETCH_ASSOC);

        if ($select_admin->rowCount() > 0) { //kiểm tra xem có bất kỳ tài khoản người dùng nào được tìm thấy hay không
            //tạo một cookie có tên userid và giá trị là ID của người dùng. Cookie này sẽ hết hạn sau 30 ngày.
            setcookie('admin_id', $row['admin_id'], time() + 60*60*24*30, '/');
            header('location:dashboard.php');
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
    <title>Secret Beauty - Admin login page</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <!--liên kết đến tệp CSS all.min.css của Font Awesome, được sử dụng để thêm các biểu tượng vào trang web.-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Đăng nhập Admin</h3>
                <div class="input-field">
                    <p>Email<span>*</span></p>
                    <input type="email" name="email" placeholder="Nhập email" maxlength="50" require class="box">
                </div>
                <div class="input-field">
                    <p>Mật khẩu<span>*</span></p>
                    <input type="password" name="pass" placeholder="Nhập mật khẩu" maxlength="50" require class="box">
                </div>
                
            <input type="submit" name="submit" value="Đăng nhập" class="btn">
            <!-- <p class="link">
                Bạn đã chưa có tài khoản?
                <a href="register.php">Đăng ký ngay</a>
            </p> -->
            
        </form>      
    </div>
    

    <!--liên kết đến tệp JavaScript sweetalert.min.js, được sử dụng để hiển thị các thông báo cảnh báo.-->
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <!--liên kết đến tệp JavaScript script.js, được sử dụng để chứa mã JavaScript tùy chỉnh của trang web.-->
    
    <script src="../js/sweetalert.js"></script>
    <script src="../js/script.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>