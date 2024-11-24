<?php
    include 'component/connect.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';

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
        $discount = isset($_POST['discount']) ? $_POST['discount'] : 0;
        $chietkhau = 0; // Giá trị mặc định nếu không có giảm giá
        if ($discount > 0) {
            $chietkhau = 10; // Chiết khấu 20%
        }
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
        $estimated_delivery = null;

        if ($ht_vanchuyen == 'Giao hàng tiết kiệm') {
            $phivanchuyen = 28700;
            $estimated_delivery_start = date('d/m/Y', strtotime('+5 days')); // Ngày bắt đầu dự kiến là ngày hiện tại +5
            $estimated_delivery_end = date('d/m/Y', strtotime('+7 days'));   // Ngày kết thúc dự kiến là ngày hiện tại +7
        } elseif ($ht_vanchuyen == 'Giao hàng nhanh') {
            $phivanchuyen = 35200;
            $estimated_delivery_start = date('d/m/Y', strtotime('+2 days')); // Ngày bắt đầu dự kiến là ngày hiện tại +2
            $estimated_delivery_end = date('d/m/Y', strtotime('+3 days'));   // Ngày kết thúc dự kiến là ngày hiện tại +3
        }
        
        $thongbao = $_POST['thongbao'];
        $thongbao_value = ($thongbao === "Nhận thông báo") ? 1 : 0;
    
        if ($ht_vanchuyen == 'Giao hàng tiết kiệm') {
            $phivanchuyen = 25000;
        } elseif ($ht_vanchuyen == 'Giao hàng nhanh') {
            $phivanchuyen = 30000;
        } elseif ($ht_vanchuyen == 'Giao hàng hỏa tốc') {
            $phivanchuyen = 50000;
        }
    
        $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
        $verify_cart->execute([$user_id]);
        $phuongthuc = 'Thanh toán khi nhận hàng';
    
        if ($verify_cart->rowCount() > 0) {
            $tongtien = 0; // Khởi tạo biến tổng tiền
            $$tongtienvon = 0;
            while ($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)) {
                $s_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
                $s_products->execute([$f_cart['sanpham_id']]);
                $f_product = $s_products->fetch(PDO::FETCH_ASSOC);
    
                $admin_id = $f_product['admin_id'];
                $insert_order = $conn->prepare("INSERT INTO `hoadon`(hoadon_id, sanpham_id, price, old_price, soluong, size) VALUES(?,?,?,?,?,?)");
                $insert_order->execute([$hoadon_id, $f_cart['sanpham_id'], $f_cart['price'], $f_product['old_price'], $f_cart['qty'], $f_cart['size']]);
                
                $qty = $f_cart['qty'];
                $size = $f_cart['size'];
                $tongtien += $f_cart['price'] * $qty; 
                $tongtienvon += $f_product['old_price'] * $qty; 
                $update_qty_query = $conn->prepare("UPDATE `sanpham` SET $size = $size - ?, luotmua = luotmua + ? WHERE sanpham_id=?");
                $update_qty_query->execute([$qty, $qty, $f_cart['sanpham_id']]);
    
                // $insert_sale = $conn->prepare("INSERT INTO `doanhthu` (hoadon_id, user_id, price) VALUES(?,?,?)");
                // $insert_sale->execute([$hoadon_id, $user_id, $f_cart['price'] * $f_cart['qty']]);      
            }
            $tongthanhtoan = ($tongtien - $discount) + $phivanchuyen;
            // Thêm vào bảng bill sau khi đã thêm vào hoadon
            $insert_bill = $conn->prepare("INSERT INTO `bill`(bill_id, hoadon_id, sl_tong, tongtien, tongtienvon, user_id, name, phone, address, address_type, tinhtrangthanhtoan, phuongthucthanhtoan, ht_vanchuyen, phivanchuyen, note, tongthanhtoan, chietkhau, tiendagiam, thongbao) 
                                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_bill->execute([$bill_id, $hoadon_id, $verify_cart->rowCount(), $tongtien, $tongtienvon, $user_id, $name, $phone, $address, $address_type, 'Chưa thanh toán', $phuongthuc, $ht_vanchuyen, $phivanchuyen, $note, $tongthanhtoan, $chietkhau, $discount, $thongbao_value]);
            // Nếu có giảm giá, thêm vào bảng diemsudung
            if ($discount > 0) {
                $sudung = 70;
                $dungvaongay = date('Y-m-d'); // Ngày sử dụng điểm
                $stmt_points = $conn->prepare("INSERT INTO diemsudung (user_id, sudung, dungvaongay) VALUES (?, ?, ?)");
                $stmt_points->execute([$user_id, $sudung, $dungvaongay]);

                $update_user = $conn->prepare("UPDATE `user` SET diem = diem - ? WHERE user_id=?");
                $update_user->execute([$sudung, $user_id]);
            }
            if ($insert_order) { // Nếu đặt hàng thành công, xóa toàn bộ giỏ hàng của người dùng.
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id=?");
                $delete_cart->execute([$user_id]);
                header('location:pending_order.php');
            }
        } else {
            $warning_msg[] = 'Đã xảy ra sự cố';
        }
        if ($thongbao == 'Nhận thông báo') {
            $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id=?");
            $select_user->execute([$user_id]);
            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
            $email = $fetch_user['email'];
            $name = $fetch_user['name'];
            $mail = new PHPMailer(true);
        
            try {
                // Cấu hình máy chủ
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'loverevelationshop@gmail.com';
                $mail->Password   = 'g u w j x u t f u p j m c z r p';  // Thay bằng mật khẩu email của bạn
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
        
                // Nội dung email
                $mail->setFrom('loverevelationshop@gmail.com', 'RÉVÉLATION');
                $mail->addAddress($email, htmlspecialchars($name));
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = "Xác nhận đơn hàng #$bill_id từ RÉVÉLATION";
                $mail->Body  = '<p>Xin chào ' . htmlspecialchars($name) . ',</p>';
                $mail->Body .= '<p>Đơn hàng <strong>#' . htmlspecialchars($bill_id) . ' </strong> của bạn đã được đặt thành công vào ngày ' . date('d/m/Y') . '.</p>';
                $mail->Body .= "<p>Cảm ơn bạn đã đặt hàng tại RÉVÉLATION.</p>";
                $mail->Body .= "<p><strong>TÓM TẮT ĐƠN HÀNG:</strong></p>";
                $mail->Body .= "<ul>";
                $mail->Body .= "<li><strong>Tổng tiền:</strong> " . number_format($tongtien) . "VNĐ</li>";
                $mail->Body .= "<li><strong>Phí vận chuyển:</strong> " . number_format($phivanchuyen) . "VNĐ</li>";
                $mail->Body .= "<li><strong>Giảm giá:</strong> " . number_format($discount) . "VNĐ</li>";
                $mail->Body .= "<li><strong>Tổng thanh toán:</strong> " . number_format($tongthanhtoan) . "VNĐ</li>";
                $mail->Body .= '<li><strong>Địa chỉ giao hàng:</strong> ' . htmlspecialchars($address) . '</li>';
                $mail->Body .= "<li><strong>Số điện thoại liên hệ:</strong> $phone</li>";
                $mail->Body .= "<li><strong>Thời gian giao hàng dự kiến:</strong> $estimated_delivery_start - $estimated_delivery_end</li>";
                $mail->Body .= "</ul>";
                $mail->Body .= "<p>Vui lòng thanh toán " . number_format($tongthanhtoan) . "VNĐ khi nhận hàng</p>";
                $mail->Body .= "<p>Bạn có thể xem thông tin chi tiết đơn hàng <a href='http://localhost/web/view_order.php?get_id=$bill_id'>tại đây</a>.</p>";
                $mail->Body .= "<p>Nếu có bất kỳ thắc mắc nào, xin vui lòng liên hệ chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>";
                $mail->Body .= "<p>Trân trọng,</p>";
                $mail->Body .= "<p><strong>RÉVÉLATION</strong></p>";
                $mail->Body .= "<footer><p>Số điện thoại: 1800 3015 | Email: loverevelationshop@gmail.com</p></footer>";
        
                $mail->send();
                header("Location: pending_order.php");
                exit;
            } catch (Exception $e) {
                $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
            }
        }
        
    }
    if (isset($_POST['momo'])) {
        $note = $_POST['note'];
        $hoadon_id = order_id();
        $bill_id = sp_id();
        $hienthi = 0;
        $discount = isset($_POST['discount']) ? $_POST['discount'] : 0;
        $chietkhau = 0; // Giá trị mặc định nếu không có giảm giá
        if ($discount > 0) {
            $chietkhau = 10; // Chiết khấu 10%
        }
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
        $estimated_delivery = null;

        $thongbao = $_POST['thongbao'];
        $thongbao_value = ($thongbao === "Nhận thông báo") ? 1 : 0;
    
        if ($ht_vanchuyen == 'Giao hàng tiết kiệm') {
            $phivanchuyen = 25000;
        } elseif ($ht_vanchuyen == 'Giao hàng nhanh') {
            $phivanchuyen = 30000;
        } elseif ($ht_vanchuyen == 'Giao hàng hỏa tốc') {
            $phivanchuyen = 50000;
        }
    
        $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
        $verify_cart->execute([$user_id]);
        $phuongthuc = 'Thanh toán bằng Momo';
    
        if ($verify_cart->rowCount() > 0) {
            $tongtien = 0; // Khởi tạo biến tổng tiền
            $$tongtienvon = 0;
            while ($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)) {
                $s_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
                $s_products->execute([$f_cart['sanpham_id']]);
                $f_product = $s_products->fetch(PDO::FETCH_ASSOC);
    
                $admin_id = $f_product['admin_id'];
                $insert_order = $conn->prepare("INSERT INTO `hoadon`(hoadon_id, sanpham_id, price, old_price, soluong, size) VALUES(?,?,?,?,?,?)");
                $insert_order->execute([$hoadon_id, $f_cart['sanpham_id'], $f_cart['price'], $f_product['old_price'], $f_cart['qty'], $f_cart['size']]);
                
                $qty = $f_cart['qty'];
                $size = $f_cart['size'];
                $tongtien += $f_cart['price'] * $qty; 
                $tongtienvon += $f_product['old_price'] * $qty; 
                $update_qty_query = $conn->prepare("UPDATE `sanpham` SET $size = $size - ?, luotmua = luotmua + ? WHERE sanpham_id=?");
                $update_qty_query->execute([$qty, $qty, $f_cart['sanpham_id']]);
    
                // $insert_sale = $conn->prepare("INSERT INTO `doanhthu` (hoadon_id, user_id, price) VALUES(?,?,?)");
                // $insert_sale->execute([$hoadon_id, $user_id, $f_cart['price'] * $f_cart['qty']]);      
            }
            $tongthanhtoan = ($tongtien - $discount) + $phivanchuyen;
            // Thêm vào bảng bill sau khi đã thêm vào hoadon
            $insert_bill = $conn->prepare("INSERT INTO `bill`(bill_id, hoadon_id, sl_tong, tongtien, tongtienvon, user_id, name, phone, address, address_type, tinhtrangthanhtoan, phuongthucthanhtoan, hienthi, ht_vanchuyen, phivanchuyen, note, tongthanhtoan, chietkhau, tiendagiam, trangthai, thongbao) 
                                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_bill->execute([$bill_id, $hoadon_id, $verify_cart->rowCount(), $tongtien, $tongtienvon, $user_id, $name, $phone, $address, $address_type, 'Chờ xử lý', $phuongthuc, $hienthi, $ht_vanchuyen, $phivanchuyen, $note, $tongthanhtoan, $chietkhau, $discount, 'Đang xử lý', $thongbao_value]);
            
            // Chuyển hướng đến trang xử lý thanh toán MoMo
            header('location:thanhtoan/xulythanhtoanmomo_atm.php?bill_id=' . $bill_id);
            exit;
        } else {
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
            <h1>Thông tin thanh toán</h1>
        </div>
        <!-- <div class="container">
            <div class="arrow-steps clearfix">
                <div class="step done"> <span><a href="vanchuyen.php" >Thông tin giao hàng</a></span> </div>
                <div class="step current"> <span><a href="thongtinthanhtoan.php" >Thông tin thanh toán</a><span> </div>
            </div>
        </div> -->
        
        <div class="row">   
            <form action="" method="POST" class="register" style="margin-bottom: -1rem;">
                <input type="hidden" name="p_id" value="<?= $get_id; ?>">
                <!-- <h3>Thông tin thanh toán</h3> -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
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
                    <div>
                        <a href="vanchuyen.php" style="color: #851639;">Thêm mới</a>
                    </div>
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
                    </div>
                </div>
                <div class="input-field">
                    <p>Đơn vị vận chuyển <span>*</span></p>
                    <select name="shipping_type" id="shipping_type" class="input" required>
                        <option value="" selected disabled hidden>Chọn đơn vị vận chuyển</option>
                        <option value="Giao hàng tiết kiệm">Tiết kiệm (từ 4-5 ngày): 25.000 VNĐ</option>
                        <option value="Giao hàng nhanh">Nhanh (từ 2-3 ngày): 30.000 VNĐ</option>
                        <!-- <option value="Giao hàng hỏa tốc">Hỏa tốc (trong 1 ngày): 50.000 VNĐ</option> -->
                    </select>
                </div>
                <div class="input-field">
                    <p>Nhận thông báo qua email</p>
                    <select name="thongbao" id="thongbao" class="input" required>
                        <option value="Không">Không</option>
                        <option value="Nhận thông báo">Nhận thông báo</option>
                    </select>
                </div>
                <input type="text" name="note" maxlength="500" placeholder="Nhập ghi chú" class="input">
                

                <div>
                    <p>Tổng tiền hàng: <?= number_format($grand_total, 0, ',', '.');?> VNĐ</p>
                    <p id="shipping_fee">Phí vận chuyển: </p>
                    <?php
                    // Kiểm tra điểm tích lũy
                    $select_points = $conn->prepare("SELECT diem FROM user WHERE user_id = ?");
                    $select_points->execute([$user_id]);
                    $points = $select_points->fetch(PDO::FETCH_ASSOC);

                    if ($points && $points['diem'] >= 70) {
                        $discount = $grand_total * 0.1;
                    ?>
                    <p>Giảm giá: - <?= number_format($discount, 0, ',', '.');?> VNĐ (10%)</p>
                    <?php
                    } else {
                        $discount = 0;
                    ?>
                    <p>Giảm giá: - 0 VNĐ</p>
                    <?php } ?>
                    <input type="hidden" id="discount" name="discount" value="<?= $discount; ?>">
                    <?php
                    // Tính phí vận chuyển dựa trên lựa chọn
                    $shipping_fee = 0;
                    if (isset($_SESSION['shipping_type'])) {
                        if ($_SESSION['shipping_type'] == "Giao hàng tiết kiệm") {
                            $shipping_fee = 28700;
                        } elseif ($_SESSION['shipping_type'] == "Giao hàng nhanh") {
                            $shipping_fee = 35200;
                        }
                    }
                    $tongtien = 0;
                    // Tính tổng thanh toán
                    $tongtien = $grand_total + $shipping_fee - $discount;
                    ?>
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
            <!-- <form method="POST" target="_blank" enctype="application/x-www-form-urlencoded" action="thanhtoan/xulythanhtoanmomo_atm.php">
                <input type="hidden" value="<?php echo $tongtien; ?>" name="tongtien">
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
                    shippingFee = 25000;
                    break;
                case "Giao hàng nhanh":
                    shippingFee = 30000;
                    break;
                case "Giao hàng hỏa tốc":
                    shippingFee = 50000;
                    break;
                default:
                    shippingFee = 0;
                    break;
            }

            // Hiển thị phí vận chuyển trong thẻ <p> có id="shipping_fee"
            document.getElementById("shipping_fee").textContent = "Phí vận chuyển: " + shippingFee.toLocaleString('vi-VN') + " VNĐ";
        });
    </script>
    <script>
        // Lắng nghe sự kiện khi người dùng thay đổi lựa chọn của hình thức vận chuyển
        document.getElementById("shipping_type").addEventListener("change", function() {
            var shippingType = this.value;
            var shippingFee = 0;
            var grandTotal = <?= $grand_total ?>; // Lấy tổng tiền hàng từ PHP
            var discount = <?= $discount; ?>
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
            var totalPayment = (grandTotal - discount) + shippingFee;

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
    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>