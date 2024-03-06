<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Đăng ký tài khoản</title>
</head>
<body>
    <section class="container">
        <form method="post">
            <h1>Đăng ký tài khoản</h1>
            <div class="form-group">
                <label for="name">Tên đăng nhập</label>
                <input type="text" name="name" id="name" class="form-control" required>
                <span class="error">Tên đăng nhập không được để trống</span>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
                <span class="error">Số điện thoại không được để trống</span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
                <span class="error">Email không được để trống</span>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" class="form-control" required>
                <span class="error">Mật khẩu không được để trống</span>
            </div>
            <div class="form-group">
                <label for="cpassword">Nhập lại mật khẩu</label>
                <input type="password" name="cpassword" id="cpassword" class="form-control" required>
                <span class="error">Mật khẩu không khớp</span>
            </div>
            <input type="submit" name="submit" value="Đăng ký" class="btn btn-primary">
            <p class="float-right">
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
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Kiểm tra dữ liệu
    if (empty($name)) {
        echo '<script>alert("Vui lòng nhập tên đăng nhập");</script>';
    } else if (empty($phone)){
        echo '<script>alert("Vui lòng nhập số điện thoại");</script>';
    } else if (empty($password)) {
        echo '<script>alert("Vui lòng nhập email");</script>';
    } else if (empty($email)) {
        echo '<script>alert("Vui lòng nhập mật khẩu");</script>';
    } else if ($password != $cpassword) {
        echo '<script>alert("Mật khẩu và mật khẩu xác nhận không khớp");</script>';
    } else {
        // Thêm dữ liệu vào database
        $sql = "INSERT INTO user (name, phone, email, password)
                VALUES ('$name', '$phone', '$email', '$password')";
        mysqli_query($conn, $sql);

        // Thông báo đăng ký thành công
        echo '<script>alert("Đăng ký thành công");</script>';

        // Redirect đến trang đăng nhập
        header("Location: login.php");
    }
}

// Đóng kết nối database
mysqli_close($conn);

?>
