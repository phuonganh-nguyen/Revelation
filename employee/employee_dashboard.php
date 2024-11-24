<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Dashboard</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>    
    <div class="main-container">
        <?php include '../component/employee_header.php'; ?>
        <section class="dashboard">
            <div class="heading">
                <h1>Trang chủ</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">               
                <div class="box">
                    <?php 
                        $select_orders = $conn->prepare("SELECT COUNT(*) AS total_orders FROM (SELECT DISTINCT bill_id FROM `bill`) AS unique_orders");
                        $select_orders->execute();
                        $result = $select_orders->fetch(PDO::FETCH_ASSOC);
                        $number_of_orders = $result['total_orders'];
                    ?>
                    <h3><?= $number_of_orders; ?></h3>
                    <p>Tổng số đơn hàng</p>
                    <a href="../admin_panel/admin_order.php" class="btn">Xem</a>
                </div>
                <div class="box">
                    <?php 
                        $select_message = $conn->prepare("SELECT * FROM `message`");
                        $select_message->execute();
                        $number_of_msg = $select_message->rowCount();
                    ?>
                    <h3><?= $number_of_msg; ?></h3>
                    <p>Tin nhắn</p>
                    <a href="../admin_panel/admin_message.php" class="btn">Xem</a>
                </div>
            </div>

        </section>
    </div>

    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>