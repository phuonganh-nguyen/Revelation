<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = '';
    } 
    if (isset($_POST['submit'])) { //ktra ng dùng đã ấn submit hay chưa
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        
        //kiểm tra xem tài khoản người dùng có tồn tại trong cơ sở dữ liệu
        $select_user = $conn->prepare("SELECT * FROM `user` WHERE email = ? AND pass = ?");
        $select_user->execute([$email, $pass]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);

        if ($select_user->rowCount() > 0) { // Kiểm tra xem có bất kỳ tài khoản người dùng nào được tìm thấy hay không
            // Nếu vai trò là "admin", chuyển hướng đến trang dashboard và đặt cookie với tên 'user_id'
            // Nếu vai trò là "khach", chuyển hướng đến trang home và đặt cookie với tên 'khach'
            // Nếu vai trò là "kho", chuyển hướng đến trang index và đặt cookie với tên 'kho'
            if ($row['vaitro'] == 'admin') {
                setcookie('user_id', $row['user_id'], time() + 60*60*24*30, '/');
                header('location: admin_panel/dashboard.php');
            } elseif ($row['vaitro'] == 'khach' && $row['is_verified'] == '1') {
                setcookie('khach_id', $row['user_id'], time() + 60*60*24*30, '/');
                header('location: home.php');
            } elseif ($row['vaitro'] == 'khach' && $row['is_verified'] != '1') {
                $warning_msg[] = 'Tài khoản của bạn chưa được xác nhận. Vui lòng kiểm tra email để xác nhận tài khoản.';
            } elseif ($row['vaitro'] == 'kho') {
                if ($row['trangthai'] == 1) {
                    setcookie('kho_id', $row['user_id'], time() + 60*60*24*30, '/');
                    header('location: storekeeper/task.php');
                } else {
                    $warning_msg[] = 'Bạn không có quyền truy cập!';
                }
                
            } elseif ($row['vaitro'] == 'nhanvien') {
                if ($row['trangthai'] == 1) {
                    setcookie('nv_id', $row['user_id'], time() + 60*60*24*30, '/');
                    header('location: employee/employee_order.php');
                } else {
                    $warning_msg[] = 'Bạn không có quyền truy cập!';
                }
            }
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
    <title>révélation - Login page</title>
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
    <div class="form-container" style="margin-top: -3rem; margin-bottom: -4rem;">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Đăng nhập</h3>
                <div class="input-field">
                    <p>Email<span>*</span></p>
                    <input type="email" name="email" placeholder="Nhập email" maxlength="50" require class="box">
                </div>
                <!-- <div class="input-field">
                    <p>Mật khẩu<span>*</span></p>
                    <input type="password" name="pass" placeholder="Nhập mật khẩu" maxlength="50" require class="box">
                </div> -->
                <div class="input-field">
                    <p>Mật khẩu<span>*</span></p>
                    <div class="password-wrapper">
                        <input type="password" name="pass" placeholder="Nhập mật khẩu" maxlength="50" required class="box" id="password-input">                            
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="bi bi-eye-fill"></i>
                        </span>
                    </div>
                </div>
                
            <input type="submit" name="submit" value="Đăng nhập" class="btn">
            <!-- <p class="link">
                Bạn đã chưa có tài khoản?
                <a href="register.php">Đăng ký ngay</a>
            </p> -->
            
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
    <script src="js/script.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>