<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Đăng nhập</title>
</head>
<body>
<!-- <form action="login.php"method="post">
    <label> Email</label>
    <input type="text" name="email">
    <label> Password</label>
    <input type="password" name="password">
    <button type="submit" name="dangnhap">Đăng nhập</button>
</form> -->
    <section class = "form-container">
        <form method = "post">
            <h1>Đăng nhập</h1>
            <input type="email" name="email" placeholder="Email" require> <!--require: ng dùng nhập dl thì mới gửi được-->
            <input type="password" name="password" placeholder="Mật khẩu" require> <!--require: ng dùng nhập dl thì mới gửi được-->
            <input type="submit" name="dangnhap" value="Đăng nhập" class = "btn">
        </form>
    </section>
</body>
</html>

<?php
    session_start();

    if(isset($_POST['dangnhap'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if($email == 'pa@gmail.com' && $password == '12345'){
            $_SESSION['email'] = $email;
            header ('location:admin.php'); //di chuyển đến trang admin.php
        }
        else{
            echo "Email hoặc mật khẩu sai, hãy thử lại";
        }
    }
?>