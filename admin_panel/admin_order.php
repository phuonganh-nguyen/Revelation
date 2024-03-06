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
        if ($update_payment == 'Xác nhận'){
            $update_status->execute(['Đã xác nhận', $hoadon_id]);
        } elseif ($update_payment == 'Giao hàng'){
            $update_status->execute(['Đang giao hàng', $hoadon_id]);
        }
        
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
            <form action="search_order.php" method="post" class="search-form"> 
                    <input type="text" name="search_order" placeholder="Tìm kiếm hóa đơn" required maxlength="100">
                    <button type="submit" class="fas fa-search" id="search_product_btn"></button>
            </form>
            <div class="heading">
                <h1>Tổng số đơn hàng</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">
                <?php
                    $select_order = $conn->prepare("SELECT * FROM `hoadon`");
                    $select_order->execute();

                    if ($select_order->rowCount()>0) {
                        while ($fetch_order = $select_order->fetch(PDO::FETCH_ASSOC)) {
                        
                ?>
                <div class="box">
                    <div class="status" style="color: <?php 
                        if($fetch_order['trangthai'] == 'Chờ xác nhận') {echo "orange";}
                        elseif($fetch_order['trangthai'] == 'Đã xác nhận' || $fetch_order['trangthai'] == 'Đang giao hàng') {echo "limegreen";} 
                        else {echo "red";}?>">
                        <?= $fetch_order['trangthai']; ?>
                    </div>
                    <div class="details">
                        <p>ID đơn hàng: <span><?= $fetch_order['hoadon_id']; ?></span></p>
                        <p>ID sản phẩm: <span><?= $fetch_order['sanpham_id']; ?></span></p>
                        <p>ID khách: <span><?= $fetch_order['user_id']; ?></span></p>
                        <p>Tên người nhận: <span style="text-transform: capitalize;"><?= $fetch_order['name']; ?></span></p>
                        <p>Ngày đặt: <span><?= $fetch_order['ngaydat']; ?></span></p>
                        <p>Số điện thoại: <span>+84<?= $fetch_order['phone']; ?></span></p>
                        <p>Tổng số tiền: <span><?= $fetch_order['price']; ?> x <?=$fetch_order['qty']; ?></span></p>
                        <p>Phương thức thanh toán: <span><?= $fetch_order['phuongthucthanhtoan']; ?></span></p>
                        <p>Địa chỉ: <span style="text-transform: capitalize;"><?= $fetch_order['address']; ?></span></p>
                    </div> 
                    <form action="" method="post">
                        <?php if ($fetch_order['trangthai'] == 'Chờ xác nhận' || $fetch_order['trangthai'] == 'Đã xác nhận') {?>
                            <input type="hidden" name="hoadon_id" value="<?= $fetch_order['hoadon_id']; ?>">
                            <select name="update_payment" class="box" style="width: 90%;">
                                <option disabled selected>
                                    <?= $fetch_order['tinhtrangthanhtoan']; ?>  
                                </option>
                                <option value="Chờ xác nhận">Chờ xác nhận</option>
                                <option value="Xác nhận">Xác nhận</option>
                                <option value="Giao hàng">Giao hàng</option>
                            </select>
                            <div class="flex-btn">                            
                                <input type="submit" name="update_order" value="Cập nhật đơn hàng" class="btn">
                                <input type="submit" name="delete_order" value="Xóa" class="btn">
                            </div>
                        
                        <?php } else {?>
                            <input type="hidden" name="hoadon_id" value="<?= $fetch_order['hoadon_id']; ?>">
                            
                            <div class="flex-btn">                            
                                <input type="submit" name="delete_order" value="Xóa" class="btn">
                            </div>
                        <?php } ?>
                    </form>
                </div>
                <?php
                        }
                    }else{
                        echo '
                            <div class="empty">
                                <p>Chưa có đơn hàng nào</p>
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