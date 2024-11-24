<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }
    $select_bill = $conn->prepare("SELECT COUNT(DISTINCT bill_id) AS total_orders FROM `bill` WHERE user_id=?");
    $select_bill->execute([$user_id]);
    $result = $select_bill->fetch(PDO::FETCH_ASSOC);
    $number_of_bill = $result['total_orders'];


    $select_message = $conn->prepare("SELECT * FROM `message` WHERE user_id=?");
    $select_message->execute([$user_id]);
    $total_message = $select_message->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - User profile page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <section class="profile">
        <div class="heading">
            <h1>Hồ sơ của tôi</h1>
        </div>
        <div class="details">
            <div class="user">
                <h3><?= $fetch_profile['name']; ?></h3>
                <p>Tổng chi tiêu: <?=number_format($fetch_profile['tiendamua'], 0, ',', '.'); ?> VNĐ</p>
                <p style="margin-bottom: 2rem;">Điểm thưởng hiện có: <?= $fetch_profile['diem']; ?></p>
                
                <a href="user_update.php" class="btn">Sửa hồ sơ</a>
            </div>
            <div class="box-container">
                <div class="box">
                    <div class="flex">
                        <i class="fa-solid fa-folder-minus"></i>
                        <h3><?= $number_of_bill; ?></h3>
                    </div>
                    <a href="order.php" class="btn">Xem đơn hàng</a>
                </div>
                <div class="box">
                    <div class="flex">
                        <i class="fa-solid fa-message"></i>
                        <h3><?= $total_message; ?></h3>
                    </div>
                    <a href="message.php" class="btn">Xem tin nhắn</a>
                </div>
            </div>
        </div>
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