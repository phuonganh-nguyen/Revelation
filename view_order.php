<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else{
        $user_id = '';
        header('location:user_login.php');
    }

    if (isset($_GET['get_id'])) {
        $get_id = $_GET['get_id'];
    } else{
        $get_id = '';
        header('location:order.php');
    }

    if (isset($_POST['cancel'])){
        // Lấy thông tin về sản phẩm và số lượng trong đơn hàng
    $select_order = $conn->prepare("SELECT * FROM `hoadon` WHERE hoadon_id=?");
    $select_order->execute([$get_id]);
    $fetch_order = $select_order->fetch(PDO::FETCH_ASSOC);

    $product_id = $fetch_order['sanpham_id'];
    $quantity = $fetch_order['qty'];

    // Cập nhật số lượng sản phẩm trong kho
    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
    $select_products->execute([$product_id]);
    $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);

    $current_stock = $fetch_product['soluong'];
    $new_stock = $current_stock + $quantity;

    $update_stock = $conn->prepare("UPDATE `sanpham` SET soluong=? WHERE sanpham_id=?");
    $update_stock->execute([$new_stock, $product_id]);

    // Cập nhật trạng thái của đơn hàng
    $update_order = $conn->prepare("UPDATE `hoadon` SET trangthai=? WHERE hoadon_id=?");
    $update_order->execute(['Đã hủy', $get_id]);

    header('location:order.php');
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Order detail page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <div class="view-ord">
        <div class="heading">
            <h1>Chi tiết đơn hàng</h1>
        </div>
        <div class="row">
            <div class="summary">
                <div class="box-container">
                    <?php 
                        $grand_total = 0;
                        $select_order = $conn->prepare("SELECT * FROM `hoadon` WHERE hoadon_id=?"); // Chuẩn bị câu lệnh SELECT để lấy tất cả các sản phẩm trong giỏ hàng của người dùng hiện tại
                        $select_order->execute([$get_id]);
                        if ($select_order->rowCount() > 0) { 
                            while ($fetch_order = $select_order->fetch(PDO::FETCH_ASSOC)) {
                                $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
                                $select_products->execute([$fetch_order['sanpham_id']]);
                                if ($select_products->rowCount() > 0) { 
                                    
                                
                    ?>
                    <div class="flex">
                        <?php
                            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
                                $sub_total = ($fetch_order['price']* $fetch_order['qty']);
                                $grand_total += $sub_total;
                        ?>
                        <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">

                        <div class="">
                            <p class="price"><?= $fetch_products['price']; ?> x <?= $fetch_order['qty']; ?></p>
                            <h3 class="name"><?= $fetch_products['name']; ?></h3>
                        </div>
                        <?php }?>
                    </div>
                    <?php
                                } else {
                                    echo '
                                        <div class="empty">
                                            <p>Bạn chưa có đơn hàng nào.</p>
                                        </div>
                                    ';
                                }
                            }
                        }
                                
                    ?>
                </div>
                    
                <div class="box-container" style="margin-top: -2rem;">
                    <div class="flex">
                        <?php $select_order = $conn->prepare("SELECT * FROM `hoadon` WHERE hoadon_id=? LIMIT 1"); // Chuẩn bị câu lệnh SELECT để lấy tất cả các sản phẩm trong giỏ hàng của người dùng hiện tại
                        $select_order->execute([$get_id]);
                        $fetch_ord = $select_order->fetch(PDO::FETCH_ASSOC);?>
                        <div class="detail">
                            <p class="status" style="color:<?php 
                                if ($fetch_ord['trangthai'] == 'Đã xác nhận' || $fetch_ord['trangthai'] == 'Đang giao hàng'){echo "green";}
                                elseif ($fetch_ord['trangthai'] == 'Đã hủy'){echo "red";}
                                else {echo "orange";} ?>"><?= $fetch_ord['trangthai']; ?></p>
                        
                            <h3>Ngày đặt</h3>
                            <p><i class="fa-regular fa-calendar-days"></i> <?= $fetch_ord['ngaydat']; ?></p>
                        
                            <h3>Thông tin vận chuyển</h3>
                            <p><i class="fa-solid fa-user"></i><?= $fetch_ord['name']; ?></p>
                            <p><i class="fa-solid fa-phone"></i><?= $fetch_ord['phone']; ?></p>
                            <p><i class="fa-solid fa-location-dot"></i><?= $fetch_ord['address']; ?></p>
                        
                            <h3>Phương thức thanh toán</h3>
                            <p><i class="fa-solid fa-hand-holding-dollar"></i><?= $fetch_ord['phuongthucthanhtoan']; ?></p>
                        </div>
                        
                    </div>
                    <?php if (($fetch_ord['trangthai'] != 'Đã hủy') && ($fetch_ord['trangthai'] != 'Đang giao hàng')) {?>
                            <form action="" method="post">
                                <button type="submit" name="cancel" class="btn" onclick="return confirm ('Bạn có chắc muốn hủy đơn hàng này không?');">Hủy đơn hàng</button>
                            </form>
                    <?php } ?>             
                </div>
                <div class="grand-total">
                        <p>Thành tiền: <?= $grand_total;?>VNĐ</p>
                    </div>  
            </div>
        </div>
        
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