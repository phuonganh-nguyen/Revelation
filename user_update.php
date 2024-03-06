<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else{
        $user_id = 'location:login.php';
    }

    if (isset($_POST['submit'])) {
        $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id=? LIMIT 1");
        $select_user->execute([$user_id]);
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

        $prev_pass = $fetch_user['pass'];
        $name = $_POST['name'];
        $email = $_POST['email'];

        //name
        if (!empty($name)) {
            $update_name = $conn->prepare("UPDATE `user` SET name=? WHERE user_id=?");
            $update_name->execute([$name, $user_id]);
            $success_msg[] = 'Tên được cập nhật thành công';
        }

        //email
        if (!empty($email)) {
            $select_email = $conn->prepare("SELECT * FROM `user` WHERE user_id=? AND email=?");
            $select_email->execute([$user_id, $email]);
            if($select_email->rowCount()>0) {
                $warning_msg[] = 'Email đã tồn tại';
            } else {
                $update_email = $conn->prepare("UPDATE `user` SET email=? WHERE user_id=?");
                $update_email->execute([$email, $user_id]);
                $success_msg[] = 'Email được cập nhật thành công';
            }
            
        }

        //password
        $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'; //giá trị băm của chuỗi rỗng
        $oldpass = $_POST['oldpass'];
        $newpass = $_POST['newpass'];
        $cpass = $_POST['cpass'];

        // if ($oldpass != $empty_pass) {
        //     if ($oldpass != $prev_pass) {
        //         $warning_msg[] = 'Mật khẩu hiện tại không khớp';

        //     } elseif ($newpass != $cpass) {
        //         $warning_msg[] = 'Mật khẩu xác nhận không khớp';
        //     } else {
        //         if ($newpass != $empty_pass) {
        //             $update_pass = $conn->prepare("UPDATE `user` SET pass=? WHERE user_id=?");
        //             $update_pass->execute([$cpass, $user_id]);
        //             $success_msg[] = 'Mật khẩu được cập nhật thành công';
        //         } else {
        //             $warning_msg[] = 'Vui lòng nhập mật khẩu!';
        //         }
        //     }
        // }

        if (!empty($oldpass) || !empty($newpass) || !empty($cpass)) {
            if ($oldpass != $prev_pass) {
                $warning_msg[] = 'Mật khẩu hiện tại không khớp';
            } elseif ($newpass != $cpass) {
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
    <title>Secrect Beauty - Update profile page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <section class="form-container">
            <div class="heading">
                <h1>Sửa hồ sơ</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <form action="" method="post" enctype="multipart/form-data" class="register">
                <div class="flex">
                    <div class="col">
                        <div class="input-field">
                            <p>Họ và tên</p>
                            <input type="text" name="name" style="text-transform: capitalize;" placeholder="<?= $fetch_profile['name']; ?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Email</p>
                            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Mật khẩu hiện tại</p>
                            <input type="password" name="oldpass" placeholder="Nhập mật khẩu hiện tại" class="box">
                        </div>
                        <div class="input-field">
                            <p>Mật khẩu mới</p>
                            <input type="password" name="newpass" placeholder="Nhập mật khẩu mới" class="box">
                        </div>
                        <div class="input-field">
                            <p>Nhập lại mật khẩu mới</p>
                            <input type="password" name="cpass" placeholder="Nhập mật lại khẩu mới" class="box">
                        </div>
                    </div>
        
                </div>
                <input type="submit" name="submit" value="Cập nhật" class="btn">
            </form>
            
        </section>





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