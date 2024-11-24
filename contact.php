<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = '';
    }

    if (isset($_POST['send_message'])) { //ktra ng dùng đã ấn submit hay chưa
        if ($user_id != '') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $message = $_POST['message'];

            $insert_mess = $conn->prepare("INSERT INTO `message`(user_id, name, email, message) VALUES (?,?,?,?)");
            $insert_mess->execute([$user_id, $name, $email, $message]);

            $success_msg[] = 'Đã gửi thành công';
        }else {
            $warning_msg[] = 'Vui lòng đăng nhập';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - User login page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <div class="service">
        <div class="box-container">
            <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="images/delivey.png" class="img1">
                        <!-- <img src="images/delivey.png" class="img2"> -->
                    </div>
                </div>
                <div class="detail">
                    <h4>Vận chuyển</h4>
                    <span>100% an toàn</span>
                </div>
            </div>

            <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="images/support.png" class="img1">
                        <!-- <img src="images/support.png" class="img2"> -->
                    </div>
                </div>
                <div class="detail">
                    <h4>Hỗ trợ</h4>
                    <span>24/7</span>
                </div>
            </div>

            <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="images/gift.png" class="img1">
                        <!-- <img src="images/gift.png" class="img2"> -->
                    </div>
                </div>
                <div class="detail">
                    <h4>Ưu đãi</h4>
                    <span>Ưu đãi hấp dẫn</span>
                </div>
            </div>

            <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="images/return.png" class="img1">
                        <!-- <img src="images/return.png" class="img2"> -->
                    </div>
                </div>
                <div class="detail">
                    <h4>Hoàn trả</h4>
                    <span>Hoàn tiền 100%</span>
                    
                </div>
            </div>
            
        </div>
    </div>
    <div class="form-container" style="margin-top: -3rem;">
        <div class="heading" style="margin-bottom: 2rem; margin-top: -2rem;">
            <h1>Gửi tin nhắn cho chúng tôi</h1>
        </div>
        <!-- <div class="box-container"> -->
            <form action="" method="post" class="register">
                <div class="input-field">
                    <label>Tên <sup>*</sup></label>
                    <input type="text" name="name" required placeholder="Nhập tên" class="box">
                </div>
                <div class="input-field">
                    <label>Email <sup>*</sup></label>
                    <input type="email" name="email" required placeholder="Nhập email" class="box">
                </div>
                <div class="input-field">
                    <label>Soạn tin <sup>*</sup></label>
                    <textarea name="message" cols="30" rows="10" required placeholder="..." class="box"></textarea>
                </div>
                <button type="submit" name="send_message" class="btn">Gửi</button>
            </form>      
        <!-- </div>          -->
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