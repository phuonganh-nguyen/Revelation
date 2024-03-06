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
    <title>Secret Beauty - Dashboard</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <!--liên kết đến tệp CSS all.min.css của Font Awesome, được sử dụng để thêm các biểu tượng vào trang web.-->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="dashboard">
            <div class="heading">
                <h1>Dashboard</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">
                <!-- <div class="box">
                    <?php 
                        $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE admin_id=?");
                        $select_products->execute([$admin_id]);  
                        $number_of_products = $select_products->rowCount();
                    ?>
                    <p>Quản lý danh mục</p>
                    <a href="type.php" class="btn">Xem</a>
                </div> -->
                <div class="box">
                    <?php 
                        $select_message = $conn->prepare("SELECT * FROM `message`");
                        $select_message->execute();
                        $number_of_msg = $select_message->rowCount();
                    ?>
                    <h3><?= $number_of_msg; ?></h3>
                    <p>Tin nhắn</p>
                    <a href="admin_message.php" class="btn">Xem</a>
                </div>
                
                <div class="box">
                    <?php 
                        $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE admin_id=?");
                        $select_products->execute([$admin_id]);
                        $number_of_products = $select_products->rowCount();
                    ?>
                    <h3><?= $number_of_products; ?></h3>
                    <p>Sản phẩm đã thêm</p>
                    <a href="view_product.php" class="btn">Xem</a>
                </div>

                <div class="box">
                    <?php 
                        $select_active_products = $conn->prepare("SELECT * FROM `sanpham` WHERE admin_id=? AND trangthai=?");
                        $select_active_products->execute([$admin_id, 'Đang hoạt động']);
                        $number_of_active_products = $select_active_products->rowCount();
                    ?>
                    <h3><?= $number_of_active_products; ?></h3>
                    <p>Sản phẩm đang hoạt động</p>
                    <a href="active_product.php" class="btn">Xem</a>
                </div>

                <div class="box">
                    <?php 
                        $select_deactive_products = $conn->prepare("SELECT * FROM `sanpham` WHERE admin_id=? AND trangthai=?");
                        $select_deactive_products->execute([$admin_id, 'Ngừng hoạt động']);
                        $number_of_deactive_products = $select_deactive_products->rowCount();
                    ?>
                    <h3><?= $number_of_deactive_products; ?></h3>
                    <p>Sản phẩm ngừng hoạt động</p>
                    <a href="deactive_product.php" class="btn">Xem</a>
                </div>

                <div class="box">
                    <?php 
                        $select_users = $conn->prepare("SELECT * FROM `user`");
                        $select_users->execute();
                        $number_of_users = $select_users->rowCount();
                    ?>
                    <h3><?= $number_of_users; ?></h3>
                    <p>Tài khoản khách hàng</p>
                    <a href="user_accounts.php" class="btn">Xem</a>
                </div>

                <div class="box">
                    <?php 
                        $select_admins = $conn->prepare("SELECT * FROM `admin`");
                        $select_admins->execute();
                        $number_of_admins = $select_admins->rowCount();
                    ?>
                    <h3><?= $number_of_admins; ?></h3>
                    <p>Tài khoản admin</p>
                    <a href="admin_accounts.php" class="btn">Xem</a>
                </div>

                <div class="box">
                    <?php 
                        $select_orders = $conn->prepare("SELECT * FROM `hoadon` WHERE admin_id=?");
                        $select_orders->execute([$admin_id]);
                        $number_of_orders= $select_orders->rowCount();
                    ?>
                    <h3><?= $number_of_orders; ?></h3>
                    <p>Tổng số đơn hàng</p>
                    <a href="admin_order.php" class="btn">Xem</a>
                </div>

                <div class="box">
                    <?php 
                        $select_confirm_orders = $conn->prepare("SELECT * FROM `hoadon` WHERE admin_id=? AND  trangthai IN ('Đã xác nhận', 'Đang giao hàng')");
                        $select_confirm_orders->execute([$admin_id]);
                        $number_of_confirm_orders= $select_confirm_orders->rowCount();
                    ?>
                    <h3><?= $number_of_confirm_orders; ?></h3>
                    <p>Số đơn hàng đã xác nhận</p>
                    <a href="admin_order_checked.php" class="btn">Xem</a>
                </div>

                <div class="box">
                    <?php 
                        $select_canceled_orders = $conn->prepare("SELECT * FROM `hoadon` WHERE admin_id=? AND  trangthai=?");
                        $select_canceled_orders->execute([$admin_id, 'Đã hủy']);
                        $number_of_canceled_orders= $select_canceled_orders->rowCount();
                    ?>
                    <h3><?= $number_of_canceled_orders; ?></h3>
                    <p>Số đơn hàng đã bị hủy</p>
                    <a href="admin_order_cancel.php" class="btn">Xem</a>
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