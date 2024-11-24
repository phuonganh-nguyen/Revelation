<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    if (isset($_POST['submit'])) {
        $id = userid();
        $name = $_POST['name']; 
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];
        $role = $_POST['type']; // Lấy vai trò từ form
    
        $select_user = $conn->prepare("SELECT* FROM `user` WHERE email = ?");
        $select_user->execute([$email]);
        
        if ($select_user->rowCount() > 0) {
            $warning_msg[] = 'Email đã tồn tại!';
        } else {
            if ($pass != $cpass) {
                $warning_msg[] = 'Mật khẩu xác nhận không khớp';
            } else {
                // Kiểm tra mật khẩu
                if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/', $pass)) {
                    $warning_msg[] = 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm số và ký tự viết hoa.';
                } else {
                    $insert_user = $conn->prepare("INSERT INTO `user`(user_id, name, email, pass, vaitro) VALUES(?,?,?,?,?)");
                    $insert_user->execute([$id, $name, $email, $cpass, $role]);
                    $success_msg[] = 'Tạo tài khoản thành công!';
                    header('location: admin_accounts.php');
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
    <title>révélation - Add employee page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page"> 
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?> 
        <section class="user-container">
            <div class="box-container">
                <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="add-user">
                    <h3>Thêm tài khoản nhân viên</h3>
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
                            <p>Vai trò<span>*</span></p>
                            <select name="type" style="text-transform: capitalize;"> 
                                <option value="nhanvien" style="text-transform: capitalize;">Nhân viên</option>
                                <option value="kho" style="text-transform: capitalize;">Kho</option> 
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Mật khẩu<span>*</span></p>
                            <div class="password-wrapper">
                                <input type="password" name="pass" placeholder="Nhập mật khẩu" maxlength="50" required class="box" id="password-input">
                                <span class="toggle-password" onclick="togglePassword()">
                                    <i class="bi bi-eye-fill"></i>
                                </span>
                            </div>
                        </div>
                        <div class="input-field">
                            <p>Nhập lại mật khẩu<span>*</span></p>
                            <div class="password-wrapper">
                                <input type="password" name="cpass" placeholder="Nhập lại mật khẩu" maxlength="50" required class="box" id="confirm-password-input">
                                <span class="toggle-password" onclick="togglePassword('confirm-password-input')">
                                    <i class="bi bi-eye-fill"></i>
                                </span>
                            </div>
                        </div>
                        <input type="submit" name="submit" value="Tạo" class="btn">
                </form>                     
                </div>
            </div>
        </section>
    </div>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId || 'password-input');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
        }

    </script>
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>