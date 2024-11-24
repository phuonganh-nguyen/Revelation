<?php
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = '';
        header('location:login.php');
    }

    if (isset($_POST['place_order'])) {
        $note = $_POST['note'];
        $hoadon_id = order_id();
        $bill_id = sp_id();
        // Truy vấn để lấy thông tin vận chuyển từ bảng vanchuyen dựa trên user_id
        $select_shipping = $conn->prepare("SELECT * FROM `vanchuyen` WHERE user_id=?");
        $select_shipping->execute([$user_id]);
    
        // Kiểm tra xem có thông tin vận chuyển nào tồn tại cho người dùng này hay không
        if ($select_shipping->rowCount() > 0) {
            // Lấy dữ liệu từ kết quả truy vấn
            $shipping_info = $select_shipping->fetch(PDO::FETCH_ASSOC);
            
            // Gán thông tin vận chuyển vào các biến tương ứng
            $name = $shipping_info['name'];
            $phone = $shipping_info['phone'];
            $address = $shipping_info['dc_cap1'] . ', ' . $shipping_info['phuong'] . ', ' . $shipping_info['quan'] . ', ' . $shipping_info['tinh'];
            $address_type = $shipping_info['loai_dc'];
        }
        
        $ht_vanchuyen = $_POST['shipping_type'];
        $phivanchuyen = 0;
    
        if ($ht_vanchuyen == 'Giao hàng tiết kiệm') {
            $phivanchuyen = 28700;
        } elseif ($ht_vanchuyen == 'Giao hàng nhanh') {
            $phivanchuyen = 35200;
        } elseif ($ht_vanchuyen == 'Giao hàng hỏa tốc') {
            $phivanchuyen = 50000;
        }
    
        $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
        $verify_cart->execute([$user_id]);
        $phuongthuc = 'Thanh toán khi nhận hàng';
    
        if ($verify_cart->rowCount() > 0) {
            $tongtien = 0; // Khởi tạo biến tổng tiền
    
            while ($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)) {
                $s_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
                $s_products->execute([$f_cart['sanpham_id']]);
                $f_product = $s_products->fetch(PDO::FETCH_ASSOC);
    
                $admin_id = $f_product['admin_id'];
                $insert_order = $conn->prepare("INSERT INTO `hoadon`(hoadon_id, sanpham_id, price, soluong, size) VALUES(?,?,?,?,?)");
                $insert_order->execute([$hoadon_id, $f_cart['sanpham_id'], $f_cart['price'], $f_cart['qty'], $f_cart['size']]);
                
                $qty = $f_cart['qty'];
                $size = $f_cart['size'];
                $tongtien += $f_cart['price'] * $qty; 

                $update_qty_query = $conn->prepare("UPDATE `sanpham` SET $size = $size - ? WHERE sanpham_id=?");
                $update_qty_query->execute([$qty, $f_cart['sanpham_id']]);
    
                // $insert_sale = $conn->prepare("INSERT INTO `doanhthu` (hoadon_id, user_id, price) VALUES(?,?,?)");
                // $insert_sale->execute([$hoadon_id, $user_id, $f_cart['price'] * $f_cart['qty']]);      
            }
            $tongthanhtoan = $tongtien + $phivanchuyen;
            // Thêm vào bảng bill sau khi đã thêm vào hoadon
            $insert_bill = $conn->prepare("INSERT INTO `bill`(bill_id, hoadon_id, sl_tong, tongtien, admin_id, user_id, name, phone, address, address_type, tinhtrangthanhtoan, phuongthucthanhtoan, ht_vanchuyen, phivanchuyen, note, tongthanhtoan) 
                                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_bill->execute([$bill_id, $hoadon_id, $verify_cart->rowCount(), $tongtien, $admin_id, $user_id, $name, $phone, $address, $address_type, 'Chưa thanh toán', $phuongthuc, $ht_vanchuyen, $phivanchuyen, $note, $tongthanhtoan]);
    
            if ($insert_order) { // Nếu đặt hàng thành công, xóa toàn bộ giỏ hàng của người dùng.
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id=?");
                $delete_cart->execute([$user_id]);
                header('location:pending_order.php');
            }
        } else {
            $warning_msg[] = 'Đã xảy ra sự cố';
        }
    }
    

    if (isset($_POST['momo'])) {
        $note = $_POST['note'];
        $hoadon_id = sp_id();
        // Truy vấn để lấy thông tin vận chuyển từ bảng vanchuyen dựa trên user_id
        $select_shipping = $conn->prepare("SELECT * FROM `vanchuyen` WHERE user_id=?");
        $select_shipping->execute([$user_id]);

        // Kiểm tra xem có thông tin vận chuyển nào tồn tại cho người dùng này hay không
        if ($select_shipping->rowCount() > 0) {
            // Lấy dữ liệu từ kết quả truy vấn
            $shipping_info = $select_shipping->fetch(PDO::FETCH_ASSOC);
            
            // Gán thông tin vận chuyển vào các biến tương ứng
            $name = $shipping_info['name'];
            $phone = $shipping_info['phone'];
            $address = $shipping_info['dc_cap1'] . ', ' . $shipping_info['phuong'] . ', ' . $shipping_info['quan'] . ', ' . $shipping_info['tinh'];
            $address_type = $shipping_info['loai_dc'];
        }
        $ht_vanchuyen = $_POST['shipping_type'];
        $phivanchuyen = 0;
        if ($ht_vanchuyen == 'Giao hàng tiết kiệm') {
            $phivanchuyen = 28700;
        } elseif ($ht_vanchuyen == 'Giao hàng nhanh') {
            // Gán phí vận chuyển cho trường hợp 'Giao hàng nhanh'
            $phivanchuyen = 35200;
        } elseif ($ht_vanchuyen == 'Giao hàng hỏa tốc') {
            // Gán phí vận chuyển cho trường hợp 'Giao hàng hỏa tốc'
            $phivanchuyen = 50000;
        }
        $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
        $verify_cart->execute([$user_id]);
        $phuongthuc = 'Thanh toán bằng MOMO';
        $tinhtrang = 'Đã thanh toán bằng MOMO';
        if (isset($_GET['get_id'])) {
            $get_product = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
            $get_product->execute([$_GET['get_id']]);
            if ($get_product->rowCount() > 0) {
                while($fetch_p = $get_product->fetch(PDO::FETCH_ASSOC)){
                    $admin_id = $fetch_p['admin_id'];
                    $insert_order = $conn->prepare("INSERT INTO `hoadon`(hoadon_id, admin_id, user_id, name, phone, email, address, address_type, sanpham_id, price, qty, phuongthucthanhtoan, tinhtrangthanhtoan, ht_vanchuyen, phivanchuyen, note)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?)");
                    $insert_order->execute([$hoadon_id, $admin_id, $user_id,  $name, $phone, $address, $address_type, $fetch_p['sanpham_id'], $fetch_p['price'], 1, $phuongthuc, $tinhtrang, $ht_vanchuyen, $phivanchuyen, $note]);
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
                $insert_order = $conn->prepare("INSERT INTO `hoadon`(hoadon_id, admin_id, user_id, name, phone, address, address_type, sanpham_id, price, qty, phuongthucthanhtoan, tinhtrangthanhtoan, ht_vanchuyen, phivanchuyen, size, note)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $insert_order->execute([$hoadon_id, $admin_id, $user_id, $name, $phone, $address, $address_type, $f_cart['sanpham_id'], $f_cart['price'], $f_cart['qty'], $phuongthuc, $tinhtrang, $ht_vanchuyen, $phivanchuyen, $f_cart['size'], $note]);
                $qty = $f_cart['qty'];
                $size = $f_cart['size'];

                // Cập nhật số lượng trong bảng sanpham dựa trên sanpham_id và size
                $update_qty_query = $conn->prepare("UPDATE `sanpham` SET $size = $size - ? WHERE sanpham_id=?");
                $update_qty_query->execute([$qty, $f_cart['sanpham_id']]);

                $insert_sale = $conn->prepare("INSERT INTO `doanhthu` (hoadon_id, user_id, price) VALUES(?,?,?)");
                $insert_sale->execute([$hoadon_id, $user_id, $f_cart['price']*$f_cart['qty']]);
             }
             
            if ($insert_order) { //Nếu đặt hàng thành công, xóa toàn bộ giỏ hàng của người dùng.
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id=?");
                $delete_cart->execute([$user_id]);
                header('location:../web/thanhtoan/xulythanhtoanmomo_atm.php');
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
    <script src="https://esgoo.net/scripts/jquery.js"></script>
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
                <!-- <div class="step done"> <span> <a href="checkout.php" >Thanh toán</a></span> </div> -->
                <div class="step done"> <span><a href="vanchuyen.php" >Vận chuyển</a></span> </div>
                <div class="step current"> <span><a href="thongtinthanhtoan.php" >Thông tin thanh toán</a><span> </div>
            </div>
        </div>
        
        <div class="row">   
            <form action="" method="POST" class="register" style="margin-bottom: -1rem;">
                <input type="hidden" name="p_id" value="<?= $get_id; ?>">
                <!-- <h3>Thông tin thanh toán</h3> -->
                <div>
                        <?php 
                            $select_shipping = $conn->prepare("SELECT * FROM `vanchuyen` WHERE user_id=?");
                            $select_shipping->execute([$user_id]);
                            while($fetch_shipping = $select_shipping->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <p><?= $fetch_shipping['name']; ?> | <?= $fetch_shipping['phone']; ?></p>
                        <p><?= $fetch_shipping['dc_cap1']; ?>, <?= $fetch_shipping['phuong']; ?>, <?= $fetch_shipping['quan']; ?>, <?= $fetch_shipping['tinh']; ?></p>
                        <?php
                            }
                        ?>
                    </div>
                <div class="flex">
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

                                        $name = $fetch_products['name']; // Lấy tên sản phẩm từ dữ liệu
                                        // Kiểm tra độ dài của tên sản phẩm
                                        if (mb_strlen($name) > 10) {
                                            // Nếu tên sản phẩm dài hơn 20 ký tự, hiển thị chỉ 20 ký tự và thêm ba dấu chấm ở cuối
                                            $name = mb_substr($name, 0, 10) . '...';
                                        }
                        ?>
                        <div class="flex">
                            <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                            <div>
                                <h3 class="name"><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $name?> (<?= $fetch_cart['size']; ?>)</a></h3>
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
                <!-- <div class="grand-total"><span>Tổng thanh toán: </span> 
                    <p><?= number_format($grand_total, 0, ',', '.');?>VNĐ</p>
                </div>  -->
                </div>
                </div>
                <div class="input-field">
                    <p>Hình thức vận chuyển <span>*</span></p>
                    <select name="shipping_type" id="shipping_type" class="input" required>
                        <option value="" selected disabled hidden>Chọn hình thức vận chuyển</option>
                        <option value="Giao hàng tiết kiệm">Tiết kiệm (từ 4-5 ngày): 28.700 VNĐ</option>
                        <option value="Giao hàng nhanh">Nhanh (từ 1-2 ngày): 35.200 VNĐ</option>
                        <option value="Giao hàng hỏa tốc">Hỏa tốc (trong 1 ngày): 50.000 VNĐ</option>
                    </select>
                </div>
                <input type="text" name="note" maxlength="500" placeholder="Nhập ghi chú" class="input">
                <div>
                    <p>Tổng tiền hàng: <?= number_format($grand_total, 0, ',', '.');?>VNĐ</p>
                    <p id="shipping_fee">Phí vận chuyển: </p>
                    <p>Giảm giá: </p>
                    <p id="total_payment">Tổng thanh toán: </p>
                </div>
                <?php
                    if (isset($_GET['partnerCode'])){
                ?>   
                <p>Đã thanh toán</p>
                <?php
                    }
                ?>    
                <button type="submit" name="place_order" class="btn"  style="margin-top: 1rem;">Thanh toán bằng tiền mặt</button>
                <button type="submit" name="momo" class="btn" style="margin-top: 1rem;">Thanh toán MOMO</button>
                <!-- <button type="submit"  name="momo" formaction="../web/thanhtoan/xulythanhtoanmomo_atm.php" class="btn" style="margin-top: 1rem;">Thanh toán MoMo ATM</button> -->
                <!-- <input type="submit" formaction="../web/thanhtoan/xulythanhtoanmomo_atm.php" name="momo" value="Thanh toán MoMo ATM" class="btn btn-danger" style="margin-top: 1rem;"> -->
            </form>
            <!-- <form class="" method="POST" target="_blank" enctype="application/x-www-form-urlencoded"
                          action="../web/thanhtoan/xulythanhtoanmomo_atm.php">
                <input type="submit" name="momo" value="Thanh toán MOMO" class="btn btn-danger">
            </form> -->
        </div>
    </div>
    <script>
    // Lắng nghe sự kiện khi người dùng thay đổi lựa chọn
        document.getElementById("shipping_type").addEventListener("change", function() {
            var shippingType = this.value;
            var shippingFee = 0;

            // Tính phí vận chuyển tương ứng với loại hình vận chuyển được chọn
            switch (shippingType) {
                case "Giao hàng tiết kiệm":
                    shippingFee = 28700;
                    break;
                case "Giao hàng nhanh":
                    shippingFee = 35200;
                    break;
                case "Giao hàng hỏa tốc":
                    shippingFee = 50000;
                    break;
                default:
                    shippingFee = 0;
                    break;
            }

            // Hiển thị phí vận chuyển trong thẻ <p> có id="shipping_fee"
            document.getElementById("shipping_fee").textContent = "Tổng tiền phí vận chuyển: " + shippingFee.toLocaleString('vi-VN') + " VNĐ";
        });
    </script>
    <script>
        // Lắng nghe sự kiện khi người dùng thay đổi lựa chọn của hình thức vận chuyển
        document.getElementById("shipping_type").addEventListener("change", function() {
            var shippingType = this.value;
            var shippingFee = 0;
            var grandTotal = <?= $grand_total ?>; // Lấy tổng tiền hàng từ PHP

            // Tính phí vận chuyển tương ứng với loại hình vận chuyển được chọn
            switch (shippingType) {
                case "Giao hàng tiết kiệm":
                    shippingFee = 28700;
                    break;
                case "Giao hàng nhanh":
                    shippingFee = 35200;
                    break;
                case "Giao hàng hỏa tốc":
                    shippingFee = 50000;
                    break;
                default:
                    shippingFee = 0;
                    break;
            }

            // Tính tổng thanh toán bằng cách cộng tổng tiền hàng và phí vận chuyển
            var totalPayment = grandTotal + shippingFee;

            // Hiển thị tổng thanh toán trong thẻ <p> có id="total_payment"
            document.getElementById("total_payment").textContent = "Tổng thanh toán: " + totalPayment.toLocaleString('vi-VN') + " VNĐ";
        });
    </script>
    <script>
                // Lắng nghe sự kiện khi người dùng nhấn nút MoMo ATM
        document.querySelector('input[name="momo"]').addEventListener('click', function(event) {
            // Kiểm tra xem người dùng đã chọn hình thức thanh toán hay chưa
            var shippingType = document.getElementById("shipping_type").value;
            if (!shippingType) {
                // Nếu chưa chọn, hiển thị thông báo và ngăn người dùng tiếp tục
                alert("Vui lòng chọn hình thức vận chuyển trước khi thanh toán bằng MoMo ATM.");
                event.preventDefault(); // Ngăn mặc định hành động của nút
            }
        });

    </script>
    <!-- <script>
        function redirectToMomo() {
            var grandTotal = <?= $grand_total ?>; // Lấy tổng tiền hàng từ PHP
            var shippingType = document.getElementById("shipping_type").value;
            var shippingFee = 0;

            // Tính phí vận chuyển tương ứng với loại hình vận chuyển được chọn
            switch (shippingType) {
                case "Giao hàng tiết kiệm":
                    shippingFee = 28700;
                    break;
                case "Giao hàng nhanh":
                    shippingFee = 35200;
                    break;
                case "Giao hàng hỏa tốc":
                    shippingFee = 50000;
                    break;
                default:
                    shippingFee = 0;
                    break;
            }

            // Tính tổng thanh toán bằng cách cộng tổng tiền hàng và phí vận chuyển
            var totalPayment = grandTotal + shippingFee;

            // Chuyển hướng đến trang xử lý thanh toán MoMo và truyền tổng thanh toán qua query parameter
            var redirectUrl = "../web/thanhtoan/xulythanhtoanmomo_atm.php?total_payment=" + totalPayment;
            window.location.href = redirectUrl;
        }
    </script> -->

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>