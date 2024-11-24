<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = '';
        header('location:login.php');
    }

    if (isset($_POST['place_order'])) {
        $bill_id = sp_id();
        $hoadon_id = sp_id();
        $name = $_POST['name'];
        $number = $_POST['number'];
        $address = $_POST['flat']. ', ' .$_POST['phuong_xa']. ', ' .$_POST['quan_huyen']. ', ' .$_POST['tinh_tp'];
        $address_type = $_POST['address_type'];
        $method = $_POST['method'];

        $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
        $verify_cart->execute([$user_id]);

        if (isset($_GET['get_id'])) {
            $get_product = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
            $get_product->execute([$_GET['get_id']]);
            if ($get_product->rowCount() > 0) {
                while($fetch_p = $get_product->fetch(PDO::FETCH_ASSOC)){
                    $admin_id = $fetch_p['admin_id'];
                    $insert_order = $conn->prepare("INSERT INTO `hoadon`(hoadon_id, admin_id, user_id, name, phone, address, address_type, phuongthucthanhtoan, sanpham_id, price, qty)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?)");
                    $insert_order->execute([$hoadon_id, $admin_id, $user_id, $name, $number, $address, $address_type, $method, $fetch_p['sanpham_id'], $fetch_p['price'], 1]);
                    header('location:order.php');
                }
            }else{
                $warning_msg[] = 'Đã xảy ra sự cố';
            }
        } elseif($verify_cart->rowCount()>0){
             while($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)){
                $s_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
                $s_products->execute([$f_cart['sanpham_id']]);
                $f_product = $s_products->fetch(PDO::FETCH_ASSOC);

                $admin_id = $f_product['admin_id'];
                $insert_order = $conn->prepare("INSERT INTO `hoadon`(hoadon_id, admin_id, user_id, name, phone, address, address_type, phuongthucthanhtoan, sanpham_id, price, qty)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?)");
                $insert_order->execute([$hoadon_id, $admin_id, $user_id, $name, $number, $address, $address_type, $method, $f_cart['sanpham_id'], $f_cart['price'], $f_cart['qty']]);
             }
            if ($insert_order) { //Nếu đặt hàng thành công, xóa toàn bộ giỏ hàng của người dùng.
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id=?");
                $delete_cart->execute([$user_id]);
                header('location:order.php');
            }
        } else{
            $warning_msg[] = 'Đã xảy ra sự cố';
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Checkout page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    
    <div class="checkout">
        <div class="heading">
            <h1>Thanh toán</h1>
        </div>
        <div class="container">
            <!-- Responsive Arrow Progress Bar -->
            <div class="arrow-steps clearfix">
                <div class="step current"> <span> <a href="checkout.php" >Thanh toán</a></span> </div>
                <div class="step"> <span><a href="vanchuyen.php" >Vận chuyển</a></span> </div>
                <div class="step"> <span><a href="thongtinthanhtoan.php" >Thông tin thanh toán</a><span> </div>
                <div class="step"> <span><a href="chitietdonhang.php" >Chi tiết đơn hàng</a><span> </div>
            </div>
        </div>
        <div class="row">
            <div class="summary">
                <h3>Giỏ Hàng</h3>
                <div class="box-container">
                    <?php
                        $grand_total = 0;
                        if (isset($_GET['get_id'])) {
                            $select_get = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                            $select_get->execute([$_GET['get_id']]);

                            while($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)){
                                $sub_total = $fetch_get['price'];
                                $grand_total += $sub_total;
                        
                    ?>
                    <div class="flex">
                        <img src="uploaded_files/<?= $fetch_get['image']; ?>" class="image">
                        <div>
                            <h3 class="name"><?= $fetch_get['name']; ?></h3>
                            <p class="price"><?= $fetch_get['price']; ?>VNĐ</p>
                        </div>
                    </div>
                    <?php 
                            }
                        }else {
                            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
                            $select_cart->execute([$user_id]);

                            if ($select_cart->rowCount() > 0){
                                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                                    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                                    $select_products->execute([$fetch_cart['sanpham_id']]);
                                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                                    $sub_total = ($fetch_cart['qty'] * $fetch_products['price']);
                                    $grand_total += $sub_total;
                                
                    ?>
                    <div class="flex">
                        <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                        <div>
                            <h3 class="name"><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $fetch_products['name']?> (<?= $fetch_cart['size']; ?>)</a></h3>
                            <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.'); ?> x <?= $fetch_cart['qty']; ?></p>
                        </div>
                    </div>
                    <?php
                                }
                            }else{
                                echo '<p class="empty">Không có sản phẩm trong giỏ hàng</p>';
                            }
                        }
                    ?>
                </div>
                <div class="grand-total"><span>Tổng thanh toán: </span> 
                    <p><?= number_format($grand_total, 0, ',', '.');?>VNĐ</p>
                </div> 
                
            </div>
            <form action="" method="post" class="register" style="margin-bottom: -1rem;">
                <input type="hidden" name="p_id" value="<?= $get_id; ?>">
                <h3>Chi tiết thanh toán</h3>
                <div class="flex">
                    <div class="box">
                        <div class="input-field">
                            <p>Họ và tên <span>*</span></p>
                            <input type="text" name="name" required maxlength="50" placeholder="Nhập họ và tên" class="input">
                        </div>
                        <div class="input-field">
                            <p>Số điện thoại <span>*</span></p>
                            <input type="number" name="number" required maxlength="10" placeholder="Nhập số điện thoại" class="input">
                        </div>
                        <!-- <div class="input-field">
                            <p>Email <span>*</span></p>
                            <input type="type" name="email" required maxlength="50" placeholder="Nhập email" class="input">
                        </div> -->
                        <!-- <div class="input-field">
                            <p>Phương thức thanh toán <span>*</span></p>
                            <select name="method" class="input">
                                <option value="Thanh toán khi nhận hàng">Thanh toán khi nhận hàng</option>
                                <option value="Ví điện tử">Ví điện tử</option>
                                <option value="ATM card (thẻ nội địa)">ATM card (thẻ nội địa)</option>
                            </select>
                        </div> -->
                        <div class="input-field">
                            <p>Loại địa chỉ <span>*</span></p>
                            <select name="address_type" class="input">
                                <option value="home">Nhà riêng</option>
                                <option value="office">Văn phòng</option>
                            </select>
                        </div>
                    </div>
                    <div class="box">
                        <div class="input-field">
                            <p>Tên đường, tòa nhà, số nhà <span>*</span></p>
                            <input type="text" name="flat" required maxlength="50" placeholder="Nhập tên đường, tòa nhà, số nhà" class="input">
                        </div>
                        <div class="input-field">
                            <p>Phường/Xã <span>*</span></p>
                            <input type="text" name="phuong_xa" required maxlength="50" placeholder="Nhập Phường/Xã" class="input">
                        </div>
                        <div class="input-field">
                            <p>Quận/Huyện <span>*</span></p>
                            <input type="text" name="quan_huyen" required maxlength="50" placeholder="Nhập Quận/Huyện" class="input">
                        </div>
                        <div class="input-field">
                            <p>Tỉnh/Thành phố <span>*</span></p>
                            <input type="text" name="tinh_tp" required maxlength="50" placeholder="Nhập Tỉnh/Thành phố" class="input">
                        </div>
                    </div>
                </div>
                <input type="text" name="note" required maxlength="500" placeholder="Nhập ghi chú" class="input">
                <!-- <div>
                    <p>Phương thức thanh toán</p>
                    <button>Thanh toán khi nhận hàng</button>
                    <button>Thanh toán qua MOMO</button>
                    <button>Thanh toán bằng ATM card (thẻ nội địa)</button>
                </div> -->
                <button type="submit" name="place_order" class="btn">Đặt hàng</button>
            </form>
            <form class="" method="POST" target="_blank" enctype="application/x-www-form-urlencoded"
                          action="../web/thanhtoan/xulythanhtoanmomo.php">
                <input type="submit" name="momo" value="Thanh toán MOMO QRcode" class="btn btn-danger">
            </form>

            <form class="" method="POST" target="_blank" enctype="application/x-www-form-urlencoded"
                          action="../web/thanhtoan/xulythanhtoanmomo_atm.php">
                <input type="submit" name="momo" value="Thanh toán MOMO" class="btn btn-danger">
            </form>
            
        </div>
    </div>
    

    <!-- <script>
        $(document).ready(function() {

            var back = $(".prev");
            var next = $(".next");
            var steps = $(".step");

            next.bind("click", function() {
            $.each(steps, function(i) {
                if (!$(steps[i]).hasClass('current') && !$(steps[i]).hasClass('done')) {
                $(steps[i]).addClass('current');
                $(steps[i - 1]).removeClass('current').addClass('done');
                return false;
                }
            })
            });
            back.bind("click", function() {
            $.each(steps, function(i) {
                if ($(steps[i]).hasClass('done') && $(steps[i + 1]).hasClass('current')) {
                $(steps[i + 1]).removeClass('current');
                $(steps[i]).removeClass('done').addClass('current');
                return false;
                }
            })
            });

            })
    </script> -->
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