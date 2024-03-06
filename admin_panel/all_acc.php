<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Beauty - Registered users page</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <!--liên kết đến tệp CSS all.min.css của Font Awesome, được sử dụng để thêm các biểu tượng vào trang web.-->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="user-container">
            <div class="heading">
                <h1>Danh sách khách hàng</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">
                <?php
                    $select_users = $conn->prepare("SELECT * FROM `user`");
                    $select_users->execute();

                    if ($select_users->rowCount() > 0) {
                        while ($fetch_user = $select_users->fetch(PDO::FETCH_ASSOC)){
                            $user_id = $fetch_user['user_id'];
              
                    ?>
                    <div class="box" style="margin-bottom: 1rem;">
                        <p>ID: <span><?= $user_id; ?></span></p>
                        <p>Tên: <span style="text-transform: capitalize;"><?= $fetch_user['name']; ?></span></p>
                        <p>Email: <span><?= $fetch_user['email']; ?></span></p>
                    </div>
                    <?php
                       }
                    } else{
                        echo '
                            <div class="empty">
                                <p>Không có người dùng!</p>
                            </div>
                        ';
                    }
                ?>
            </div>

            <div class="heading" style="margin-top: 2rem;">
                <h1>Danh sách admin</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">
                <?php
                    $select_admin = $conn->prepare("SELECT * FROM `admin`");
                    $select_admin->execute();

                    if ($select_admin->rowCount() > 0) {
                        while ($fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC)){
                            $admin_id = $fetch_admin['admin_id'];
              
                    ?>
                    <div class="box" style="margin-bottom: 1rem;">
                        <p>ID: <span><?= $admin_id; ?></span></p>
                        <p>Tên: <span style="text-transform: capitalize;"><?= $fetch_admin['name']; ?></span></p>
                    </div>
                    <?php
                       }
                    } else{
                        echo '
                            <div class="empty">
                                <p>Không có người dùng!</p>
                            </div>
                        ';
                    }
                ?>
            </div>
        </section>
    </div>

    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>