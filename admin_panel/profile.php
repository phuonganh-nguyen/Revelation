<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
    }

    $admin_products = $conn->prepare("SELECT * FROM `sanpham` WHERE admin_id=?");
    $admin_products->execute([$admin_id]);
    $total_products = $admin_products->rowCount();

    $select_orders = $conn->prepare("SELECT * FROM `hoadon` WHERE admin_id=?");
    $select_orders->execute([$admin_id]);
    $total_orders = $select_orders->rowCount();
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Beauty - Admin profile page</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <!--liên kết đến tệp CSS all.min.css của Font Awesome, được sử dụng để thêm các biểu tượng vào trang web.-->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="admin-profile">
            <div class="heading">
                <h1>Hồ sơ của tôi</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="details">
                <div class="admin">
                    <h3 class="name"><?= $fetch_profile['name'];?></h3>
                    <span>Admin</span>
                    <a href="update.php" class="btn">Sửa hồ sơ</a>
                </div>
                <div class="flex">
                    <div class="box">
                        <span><?= $total_products; ?></span>
                        <p>Sản phẩm đã thêm</p>
                        <a href="view_product.php" class="btn">Xem</a>
                    </div>
                    <div class="box">
                        <span><?= $total_orders; ?></span>
                        <p>Tổng số đơn đặt hàng</p>
                        <a href="admin_order.php" class="btn">Xem</a>
                    </div>
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