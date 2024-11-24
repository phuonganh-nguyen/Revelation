<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    //update order from database
    if (isset($_POST['update_order'])) {
        $hoadon_id = $_POST['hoadon_id'];
        $new_status = $_POST['update_status']; // Lấy giá trị của trạng thái mới từ biểu mẫu
        if($new_status == 'Xác nhận'){
            $status = 'Đã xác nhận';
        }elseif ($new_status == 'Giao hàng') {
            $status = 'Đang giao hàng';
        }
        // Cập nhật trạng thái của đơn hàng trong cơ sở dữ liệu
        $update_status = $conn->prepare("UPDATE `hoadon` SET trangthai=? WHERE hoadon_id=?");
        $update_status->execute([$status, $hoadon_id]);

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
    <title>révélation - Admin message</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
                <h1>Số đơn hàng đã xác nhận</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">
                <?php
                    $select_order = $conn->prepare("SELECT * FROM `hoadon` WHERE trangthai IN ('Đã xác nhận', 'Đang giao hàng', 'Đã nhận hàng') ORDER BY ngaydat DESC");
                    $select_order->execute();
                    $ordersById = array();
                    if ($select_order->rowCount() > 0) { 
                        while ($fetch_order = $select_order->fetch(PDO::FETCH_ASSOC)) {
                            $orderId = $fetch_order['hoadon_id'];
        
                            if (!isset($ordersById[$orderId])) {
                                $ordersById[$orderId] = array();
                            }
        
                            $ordersById[$orderId][] = $fetch_order;
                        }foreach ($ordersById as $orderId => $orders) {
                            // Tổng giá tiền của các đơn hàng cùng ID
                            $totalPrice = 0;
                            // Số lượng sản phẩm trong đơn hàng
                            $quantity = 0;
                            foreach ($orders as $fetch_order) {
                                $product_id = $fetch_order['sanpham_id'];
                                
                                $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                                $select_products->execute([$product_id]);
        
                                if ($select_products->rowCount() > 0) {
                                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                                    
                                }
                                $totalPrice += ($fetch_order['price'] * $fetch_order['qty']);
                                // Tăng số lượng sản phẩm
                            $quantity += $fetch_order['qty'];
                            }
                ?>
                <div class="box">
                    <div class="status" style="color: <?php 
                        if($fetch_order['trangthai'] == 'Chờ xác nhận') {echo "orange";}
                        elseif($fetch_order['trangthai'] == 'Đã xác nhận' || $fetch_order['trangthai'] == 'Đang giao hàng' || $fetch_order['trangthai'] == 'Đã nhận hàng') {echo "limegreen";} 
                        else {echo "red";}?>">
                        <?= $fetch_order['trangthai']; ?>
                    </div>
                    <div class="details">
                        <p>ID đơn hàng: <span><a href="view_order.php?get_id=<?= $fetch_order['hoadon_id']?>" class="name"><?= $fetch_order['hoadon_id'] ?></a></span></p>
                        <!-- <p>ID sản phẩm: <span><?= $fetch_order['sanpham_id']; ?></span></p> -->
                        <p>ID khách: <span><?= $fetch_order['user_id']; ?></span></p>
                        <p>Tên người nhận: <span style="text-transform: capitalize;"><?= $fetch_order['name']; ?></span></p>
                        <p>Ngày đặt: <span><?= $fetch_order['ngaydat']; ?></span></p>
                        <p>Phương thức thanh toán: <span><?= $fetch_order['phuongthucthanhtoan']; ?></span></p>
                        <p>Số điện thoại: <span>+84<?= $fetch_order['phone']; ?></span></p>
                        <p>Tổng số tiền: <span><?= $fetch_order['price']; ?> x <?=$fetch_order['qty']; ?></span></p>
                        
                        <p>Địa chỉ: <span style="text-transform: capitalize;"><?= $fetch_order['address']; ?></span></p>
                    </div> 
                    <form action="" method="post">
                        <?php if ($fetch_order['trangthai'] == 'Chờ xác nhận' || $fetch_order['trangthai'] == 'Đã xác nhận') {?>
                            <input type="hidden" name="hoadon_id" value="<?= $fetch_order['hoadon_id']; ?>">
                            <select name="update_status" class="box" style="width: 90%;">
                                <option disabled selected>
                                    <?= $fetch_order['trangthai']; ?>  
                                </option>
                                <!-- <option value="Chờ xác nhận">Chờ xác nhận</option> -->
                                <option value="Xác nhận">Xác nhận</option>
                                <option value="Giao hàng">Giao hàng</option>
                            </select>
                            <div class="flex-btn">                            
                                <input type="submit" name="update_order" value="Cập nhật đơn hàng" class="btn">
                                <input type="submit" name="delete_order" value="Xóa" class="btn">
                            </div>
                        
                        <?php } elseif($fetch_order['trangthai'] == 'Đã hủy') {?>
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