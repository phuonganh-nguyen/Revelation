<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
    }

    //update order from database
    if (isset($_POST['update_order'])) {
        $hoadon_id = $_POST['hoadon_id'];
        $update_payment = $_POST['update_payment'];
        $update_pay = $conn->prepare("UPDATE `hoadon` SET tinhtrangthanhtoan=? WHERE hoadon_id=?");
        $update_pay->execute([$update_payment, $hoadon_id]);

        $new_status = $_POST['update_payment'];
        $update_status = $conn->prepare("UPDATE `hoadon` SET trangthai=? WHERE hoadon_id=?");
        $update_status->execute(['Đã xác nhận', $hoadon_id]);
        $success_msg[] = 'Trạng thái đơn hàng đã được cập nhật';
    }

    if (isset($_POST['delete_order'])) {
        $delete_id = $_POST['hoadon_id'];

        $verify_delete = $conn->prepare("SELECT * FROM `hoadon` WHERE hoadon_id=?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_order = $conn->prepare("DELETE FROM `hoadon` WHERE hoadon_id=?");
            $delete_order->execute([$delete_id]);
            $success_msg[] = 'Đơn hàng đã được xóa thành công!';
        } else {
            $warning_msg[] = 'Đơn hàng đã bị xóa';
        }
    }
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Beauty - Admin message</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <!--liên kết đến tệp CSS all.min.css của Font Awesome, được sử dụng để thêm các biểu tượng vào trang web.-->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="order-container">
            <div class="heading">
                <h1>Số đơn hàng đã hủy</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">
                <?php
                    $select_order = $conn->prepare("SELECT * FROM `hoadon` WHERE trangthai=?");
                    $select_order->execute(['Đã hủy']);

                    if ($select_order->rowCount()>0) {
                        while ($fetch_order = $select_order->fetch(PDO::FETCH_ASSOC)) {
                        
                ?>
                <div class="box">
                    <div class="status" style="color: <?php 
                        if($fetch_order['trangthai'] == 'Chờ xác nhận') {echo "orange";}
                        elseif($fetch_order['trangthai'] == 'Đã xác nhận') {echo "limegreen";} 
                        else {echo "red";}?>">
                        <?= $fetch_order['trangthai']; ?>
                    </div>
                    <div class="details">
                        <p>ID khách: <span><?= $fetch_order['user_id']; ?></span></p>
                        <p>Tên người nhận: <span style="text-transform: capitalize;"><?= $fetch_order['name']; ?></span></p>
                        <p>Ngày đặt: <span><?= $fetch_order['ngaydat']; ?></span></p>
                        <p>Số điện thoại: <span>+84<?= $fetch_order['phone']; ?></span></p>
                        <p>Tổng số tiền: <span><?= $fetch_order['price']; ?></span></p>
                        <p>Phương thức thanh toán: <span><?= $fetch_order['phuongthucthanhtoan']; ?></span></p>
                        <p>Địa chỉ: <span style="text-transform: capitalize;"><?= $fetch_order['address']; ?></span></p>
                    </div>
                    
                </div>
                <?php
                        }
                    }else{
                        echo '
                            <div class="empty">
                                <p>Không có đơn hàng bị hủy</p>
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