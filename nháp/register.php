<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Đăng ký</title>
</head>
<body>
    <section class = "form-container">
        <form method = "post">
            <h1>Đăng ký</h1>
            <input type="text" name="name" placeholder="Tên" require> <!--require: ng dùng nhập dl thì mới gửi được-->
            <input type="text" name="phone" placeholder="Số điện thoại" require> <!--require: ng dùng nhập dl thì mới gửi được-->
            <input type="email" name="email" placeholder="Email" require> <!--require: ng dùng nhập dl thì mới gửi được-->
            <input type="address" name="address" placeholder="Địa chỉ" require> <!--require: ng dùng nhập dl thì mới gửi được-->
            <input type="password" name="password" placeholder="Mật khẩu" require> <!--require: ng dùng nhập dl thì mới gửi được-->
            <input type="password" name="cpassword" placeholder="Nhập lại mật khẩu" require> <!--require: ng dùng nhập dl thì mới gửi được-->
            <input type="submit" name="submit-btn" value="Đăng ký" class = "btn">
            <p>
                Bạn đã có tài khoản?
                <a href="login.php">Đăng nhập ngay</a>
            </p>
        </form>
    </section>
</body>
</html>

<?php

    // Kết nối database
    $conn = mysqli_connect('localhost', 'root', '', 'csdl1');

    // Kiểm tra nếu form đã được gửi
    if (isset($_POST['submit'])) {

        // Lấy dữ liệu từ form
        $user_id = "";
        $username = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $address = $_POST['address'];
        $user_type = '';

        // Kiểm tra dữ liệu
        if (empty($username)) {
            echo '<script>alert("Vui lòng nhập tên đăng nhập");</script>';
        } else if (empty($phone)){
            echo '<script>alert("Vui lòng nhập số điện thoại");</script>';
        } else if (empty($password)) {
            echo '<script>alert("Vui lòng nhập email");</script>';
        } else if (empty($address)){
            echo '<script>alert("Vui lòng nhập địa chỉ");</script>';
        } else if (empty($email)) {
            echo '<script>alert("Vui lòng nhập mật khẩu");</script>';
        } else if ($password != $cpassword) {
            echo '<script>alert("Mật khẩu và mật khẩu xác nhận không khớp");</script>';
        } else {
            // Thêm dữ liệu vào database
            $sql = "INSERT INTO user (USER_ID, PHONE, EMAIL, NAME, ADDRESS, PASS, USER_TYPE)
                    VALUES ('$user_id','$name', '$phone', '$email', '$address', '$password', '$user_type')";
            mysqli_query($conn, $sql);

            // Thông báo đăng ký thành công
            echo '<script>alert("Đăng ký thành công");</script>';

            //đến trang đăng nhập
            header ("Location: login.php");
        }
    }
    mysqli_close($conn);
?>