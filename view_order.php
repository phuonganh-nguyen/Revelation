<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh'); // Thiết lập múi giờ Việt Nam
    include 'component/connect.php';
    require 'vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }

    if (isset($_GET['get_id'])) {
        $get_id = $_GET['get_id'];
    } else{
        $get_id = '';
        header('location:order.php');
    }

    if (isset($_POST['cancel'])) {
        // Lấy thông tin từ bảng `bill` dựa vào `bill_id`
    $select_orders = $conn->prepare("SELECT * FROM `bill` WHERE bill_id = ?");
    $select_orders->execute([$get_id]);

    $processed_hoadon_ids = []; // Mảng lưu các `hoadon_id` đã xử lý

    while ($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
        $hoadon_id = $fetch_order['hoadon_id']; // Lấy `hoadon_id` từ bảng `bill`

        // Tiếp tục lấy thông tin từ bảng `hoadon` dựa vào `hoadon_id`
        $select_hoadon = $conn->prepare("SELECT * FROM `hoadon` WHERE hoadon_id = ?");
        $select_hoadon->execute([$hoadon_id]);

        // Chỉ xử lý các sản phẩm từ `hoadon_id` đầu tiên
        if (!in_array($hoadon_id, $processed_hoadon_ids)) {
            $processed_hoadon_ids[] = $hoadon_id; // Đánh dấu `hoadon_id` đã xử lý

            while ($fetch_hoadon = $select_hoadon->fetch(PDO::FETCH_ASSOC)) {
                $product_id = $fetch_hoadon['sanpham_id']; // Lấy mã sản phẩm
                $qty = $fetch_hoadon['soluong']; // Lấy số lượng đã mua
                $size = $fetch_hoadon['size']; // Lấy size sản phẩm

                // Cập nhật lại số lượng trong bảng `sanpham` theo `sanpham_id` và `size`
                $update_qty_query = $conn->prepare("UPDATE `sanpham` SET `$size` = `$size` + ? WHERE sanpham_id = ?");
                $update_qty_query->execute([$qty, $product_id]);
            }
        }
    }
        if ($tiendagiam > 0){
            $sudung = 70;
            $update_user = $conn->prepare("UPDATE `user` SET diem = diem + ? WHERE user_id=?");
            $update_user->execute([$sudung, $user_id]);
        }
        // Cập nhật trạng thái của đơn hàng trong bảng `bill`
        $update_order = $conn->prepare("UPDATE `bill` SET trangthai = ?, `ngayhuy` = NOW() WHERE bill_id = ?");
        $update_order->execute(['Đã hủy', $get_id]);
        // Gửi email thông báo đơn hàng đã hủy
        $mail = new PHPMailer(true);
        
        // Lấy thông tin người dùng
        $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
        $select_user->execute([$user_id]);
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
        
        // Kiểm tra xem có tìm thấy người dùng không
        if (!$fetch_user) {
            header('location:order.php');
            exit();
        }

        $email = $fetch_user['email'];
        $name = $fetch_user['name'];
        if ($thongbao == 1){
            if ($tinhtrangthanhtoan == 'Đã thanh toán'){
                try {
                    // Cấu hình email
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'loverevelationshop@gmail.com';
                    $mail->Password = 'g u w j x u t f u p j m c z r p'; // Lưu ý về bảo mật
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->setFrom('loverevelationshop@gmail.com', 'RÉVÉLATION');
                    $mail->addAddress($email, htmlspecialchars($name));
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
            
                    // Nội dung email thông báo hủy đơn hàng
                    $mail->Subject = "Đơn hàng #$get_id đã được hủy";
                    $mail->Body = "<p>Xin chào $name,</p>";
                    $mail->Body .= "<p>Đơn hàng <strong>#" . htmlspecialchars($get_id) . "</strong> của bạn đã được hủy vào ngày " . date('d/m/Y') . ".</p>";
                    $mail->Body .= "<p>Số tiền đã thanh toán sẽ được hoàn lại vào ví Momo của bạn trong vòng 1 ngày</p>";
                    $mail->Body .= "<p>Bạn có thể xem thông tin chi tiết đơn hàng <a href='http://localhost/web/view_order.php?get_id=$get_id'>tại đây</a>.</p>";
                    $mail->Body .= "<p>Nếu có bất kỳ thắc mắc nào, xin vui lòng liên hệ chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>";
                    $mail->Body .= "<p>Trân trọng,</p>";
                    $mail->Body .= "<p><a href='http://localhost/web/home.php'><strong>RÉVÉLATION</strong></a></p>";
                    $mail->Body .= "<footer><p>Số điện thoại: 1800 3015 | Email: loverevelationshop@gmail.com</p></footer>";
            
                    $mail->send();
                } catch (Exception $e) {
                    // Xử lý lỗi gửi email nếu cần thiết
                    $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
                }
                header('location:order_cancelled.php');
                exit();
            } else{
                try {
                    // Cấu hình email
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'loverevelationshop@gmail.com';
                    $mail->Password = 'g u w j x u t f u p j m c z r p'; // Lưu ý về bảo mật
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->setFrom('loverevelationshop@gmail.com', 'RÉVÉLATION');
                    $mail->addAddress($email, htmlspecialchars($name));
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
            
                    // Nội dung email thông báo hủy đơn hàng
                    $mail->Subject = "Đơn hàng #$get_id đã được hủy";
                    $mail->Body = "<p>Xin chào $name,</p>";
                    $mail->Body .= "<p>Đơn hàng <strong>#" . htmlspecialchars($get_id) . "</strong> của bạn đã được hủy vào ngày " . date('d/m/Y') . ".</p>";
                    $mail->Body .= "<p>Bạn có thể xem thông tin chi tiết đơn hàng <a href='http://localhost/web/view_order.php?get_id=$get_id'>tại đây</a>.</p>";
                    $mail->Body .= "<p>Nếu có bất kỳ thắc mắc nào, xin vui lòng liên hệ chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>";
                    $mail->Body .= "<p>Trân trọng,</p>";
                    $mail->Body .= "<p><a href='http://localhost/web/home.php'><strong>RÉVÉLATION</strong></a></p>";
                    $mail->Body .= "<footer><p>Số điện thoại: 1800 3015 | Email: loverevelationshop@gmail.com</p></footer>";
            
                    $mail->send();
                } catch (Exception $e) {
                    // Xử lý lỗi gửi email nếu cần thiết
                    $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
                }
                header('location:order_cancelled.php');
                exit();
            }
        }
        header('location:order_cancelled.php');
        
    }
    
    if (isset($_POST['received'])) {
        // Cập nhật trạng thái của đơn hàng
        $update_order = $conn->prepare("UPDATE `bill` SET trangthai=?, ngaynhan=NOW(), tinhtrangthanhtoan=? WHERE bill_id=? AND phuongthucthanhtoan='Thanh toán khi nhận hàng'");
        $update_order->execute(['Đã nhận hàng', 'Đã thanh toán', $get_id]);
        $update_order_momo = $conn->prepare("UPDATE `bill` SET trangthai=?, ngaynhan=NOW() WHERE bill_id=? AND phuongthucthanhtoan='Thanh toán bằng Momo'");
        $update_order_momo->execute(['Đã nhận hàng', $get_id]);
        
        // Lấy thông tin về tổng tiền của đơn hàng
        $select_bill = $conn->prepare("SELECT * FROM `bill` WHERE bill_id = ?");
        $select_bill->execute([$get_id]);
        $bill_info = $select_bill->fetch(PDO::FETCH_ASSOC);
        $thongbao = $bill_info['thongbao'];
        
        if ($bill_info) {
            $tongthanhtoan = $bill_info['tongthanhtoan'];

            // Tính điểm thưởng dựa trên tổng thanh toán
            $bonus_points = 0;
            if ($tongthanhtoan < 1000000) {
                $bonus_points = 10;
            } elseif ($tongthanhtoan < 2000000) {
                $bonus_points = 20;
            } elseif ($tongthanhtoan < 3000000) {
                $bonus_points = 30;
            } elseif ($tongthanhtoan < 4000000) {
                $bonus_points = 40;
            } else {
                $bonus_points = 70;
            }
            // Cập nhật điểm và tổng số tiền đã mua vào bảng `user`
            $update_user = $conn->prepare("UPDATE `user` SET diem = diem + ?, tiendamua = tiendamua + ? WHERE user_id = ?");
            $update_user->execute([$bonus_points, $tongthanhtoan, $user_id]);
            
            $doanhthusauchietkhau = $bill_info['tongtien'] - $bill_info['tiendagiam'];
            $loinhuan = $doanhthusauchietkhau - $bill_info['tongtienvon'];
            $insert_doanhthu = $conn->prepare("INSERT INTO `doanhthu` (bill_id, doanhthubandau, giamgia, doanhthusauchietkhau, loinhuan, ngayban) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_doanhthu->execute([$bill_info['bill_id'], $bill_info['tongtien'], $bill_info['tiendagiam'], $doanhthusauchietkhau, $loinhuan, $bill_info['ngaydat']]);
        }

        if ($thongbao == 1) {
            // Lấy thông tin người dùng
            $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
            $select_user->execute([$user_id]);
            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
            
            // Kiểm tra xem có tìm thấy người dùng không
            if (!$fetch_user) {
                header('location:order.php');
                exit();
            }

            $email = $fetch_user['email'];
            $name = $fetch_user['name'];
            $mail = new PHPMailer(true);


            try {
                // Cấu hình email
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'loverevelationshop@gmail.com';
                $mail->Password = 'g u w j x u t f u p j m c z r p'; // Lưu ý về bảo mật
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('loverevelationshop@gmail.com', 'RÉVÉLATION');
                $mail->addAddress($email, htmlspecialchars($name));
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
        
                // Nội dung email thông báo hủy đơn hàng
                $mail->Subject = "Đơn hàng #$get_id đã giao thành công";
                $mail->Body = "<p>Xin chào $name,</p>";
                $mail->Body .= "<p>Đơn hàng <strong>#" . htmlspecialchars($get_id) . "</strong> của bạn đã được được giao thành công ngày " . date('d/m/Y') . ".</p>";
                $mail->Body .= "<p>Cảm ơn bạn đã đặt hàng tại RÉVÉLATION.</p>";
                $mail->Body .= "<p><strong>TÓM TẮT ĐƠN HÀNG:</strong></p>";
                $mail->Body .= "<ul>";
                $mail->Body .= "<li><strong>Tổng tiền:</strong> " . number_format($bill_info['tongtien']) . " VNĐ</li>";
                $mail->Body .= "<li><strong>Phí vận chuyển:</strong> " . number_format($bill_info['phivanchuyen']) . " VNĐ</li>";
                $mail->Body .= "<li><strong>Giảm giá:</strong> " . number_format($bill_info['tiendagiam']) . " VNĐ</li>";
                $mail->Body .= "<li><strong>Tổng thanh toán:</strong> " . number_format($bill_info['tongthanhtoan']) . " VNĐ</li>";
                $mail->Body .= '<li><strong>Địa chỉ giao hàng:</strong> ' . htmlspecialchars($bill_info['address']) . '</li>';
                $mail->Body .= "<li><strong>Số điện thoại liên hệ:</strong> " . $bill_info['phone'] . "</li>";
                $mail->Body .= "</ul>";
                $mail->Body .= "<p>Bạn có thể xem thông tin chi tiết đơn hàng <a href='http://localhost/web/view_order.php?get_id=$get_id' style='color: #851639;'>tại đây</a>.</p>";
                $mail->Body .= "<p>Nếu có bất kỳ thắc mắc nào, xin vui lòng liên hệ chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>";
                $mail->Body .= "<p>Trân trọng,</p>";
                $mail->Body .= "<p><a href='http://localhost/web/home.php' style='color: #851639;'><strong>RÉVÉLATION</strong></a></p>";
                $mail->Body .= "<footer><p>Số điện thoại: 1800 3015 | Email: loverevelationshop@gmail.com</p></footer>";
        
                $mail->send();
            } catch (Exception $e) {
                // Xử lý lỗi gửi email nếu cần thiết
                $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
            }
        }
        
        
        header('location:order_successful.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Order detail page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body id="storekeeper-page">
    <?php include 'component/user_header.php' ?>
    <div class="orders">   
        <div class="heading">
            <h1>Chi tiết đơn hàng</h1>
        </div>
        <div class="box-container">
            <?php 
                // Truy vấn lấy thông tin đơn hàng dựa theo get_id
                $select_bill = $conn->prepare("SELECT * FROM `bill` WHERE bill_id = ? LIMIT 1");
                $select_bill->execute([$get_id]);

                if ($select_bill->rowCount() > 0) { 
                    while ($fetch_bill = $select_bill->fetch(PDO::FETCH_ASSOC)) {
                        // Hiển thị thông tin đơn hàng cho mỗi bill
                        $bill_id = $fetch_bill['bill_id'];
                        $hoadon_id = $fetch_bill['hoadon_id'];
                        $tongthanhtoan = $fetch_bill['tongthanhtoan'];
                        $ngaydat = $fetch_bill['ngaydat'];
                        $tinhtrangthanhtoan = $fetch_bill['tinhtrangthanhtoan'];
                        $trangthai = $fetch_bill['trangthai'];
                        $dateTime = new DateTime($ngaydat);
                        $formattedDate = $dateTime->format('H:i d/m/Y');
                        // Calculate points based on total amount
                        $points = match (true) {
                            $tongthanhtoan < 1000000 => 10,
                            $tongthanhtoan < 2000000 => 20,
                            $tongthanhtoan < 3000000 => 30,
                            $tongthanhtoan < 4000000 => 40,
                            default => 70,
                        };
                        ?>
                        <h2>Ngày đặt: <?= $formattedDate; ?></h2>
                        <div class="box" onclick="window.location.href='view_order.php?get_id=<?= $fetch_bill['bill_id']?>'">
                            <h3>Mã đơn hàng #<?= $bill_id; ?></h3>
                            <p class="order-status">
                                <?= htmlspecialchars($trangthai); ?>
                            </p>
                            <div style="margin-top:1rem;">
                                <div class="detail-top" style="display: flex; justify-content: space-between; padding-bottom: 20px; border-bottom: 1px solid #ccc;">
                                    <div class="shipping-info" style="flex: 1;">
                                        <p><strong><?= htmlspecialchars($fetch_bill['name']); ?></strong></p>
                                        <p><?= htmlspecialchars($fetch_bill['address']); ?></p>
                                        <p>Điện thoại: <?= htmlspecialchars($fetch_bill['phone']); ?></p>
                                        <p>Điểm: +<?= $points; ?></p>
                                    </div>
                                    <div class="status-info" style="flex: 1; text-align: right;">
                                        <?php
                                        function formatDateTime($datetime) {
                                            return date('H:i d/m/Y', strtotime($datetime)); // Giờ:phút ngày/tháng/năm
                                        }
                                        // Hiển thị trạng thái và thời gian tương ứng theo yêu cầu
                                        switch($trangthai) {
                                            case 'Chờ xác nhận':
                                                echo "<p><strong>Đặt hàng thành công</strong> " . formatDateTime($fetch_bill['ngaydat']) . "</p>";
                                                break;
                                
                                            case 'Đóng hàng':
                                                echo "<p><strong>Đã xác nhận, đang đóng hàng</strong> " . formatDateTime($fetch_bill['ngaydonghang']) . "</p>";
                                                echo "<p><strong>Đặt hàng thành công</strong> " . formatDateTime($fetch_bill['ngaydat']) . "</p>";
                                                break;
                                
                                            case 'Đang giao hàng':
                                                echo "<p><strong>Đang giao hàng</strong> " . formatDateTime($fetch_bill['ngaygiaohang']) . "</p>";
                                                echo "<p><strong>Đã xác nhận, đang đóng hàng</strong> " . formatDateTime($fetch_bill['ngaydonghang']) . "</p>";
                                                echo "<p><strong>Đặt hàng thành công</strong> " . formatDateTime($fetch_bill['ngaydat']) . "</p>";
                                                break;
                                
                                            case 'Đã nhận hàng':
                                                echo "<p><strong>Đã nhận hàng</strong> " . formatDateTime($fetch_bill['ngaynhan']) . "</p>";
                                                echo "<p><strong>Đang giao hàng</strong> " . formatDateTime($fetch_bill['ngaygiaohang']) . "</p>";
                                                echo "<p><strong>Đã xác nhận, đang đóng hàng</strong> " . formatDateTime($fetch_bill['ngaydonghang']) . "</p>";
                                                echo "<p><strong>Đặt hàng thành công</strong> " . formatDateTime($fetch_bill['ngaydat']) . "</p>";
                                                break;
                                
                                            case 'Đã hủy':
                                                echo "<p><strong>Đã hủy</strong> " . formatDateTime($fetch_bill['ngayhuy']) . "</p>";
                                                echo "<p><strong>Đặt hàng thành công</strong> " . formatDateTime($fetch_bill['ngaydat']) . "</p>";
                                                break;
                                        }
                                
                                        ?>
                                    </div>
                                </div>

                            </div>
                            <div style="margin-top:1rem;">
                                <?php
                                // Truy vấn lấy thông tin sản phẩm từ bảng hoadon dựa trên hoadon_id
                                $select_order_items = $conn->prepare("SELECT * FROM `hoadon` WHERE hoadon_id=?");
                                $select_order_items->execute([$hoadon_id]);

                                if ($select_order_items->rowCount() > 0) {
                                    while ($fetch_order_item = $select_order_items->fetch(PDO::FETCH_ASSOC)) {
                                        // Truy vấn lấy chi tiết sản phẩm từ bảng sanpham dựa trên sanpham_id
                                        $select_product = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                                        $select_product->execute([$fetch_order_item['sanpham_id']]);
                                        $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                            <div class="product-image">
                                                <img src="uploaded_files/<?= $fetch_product['image'];?>" alt="<?= htmlspecialchars($fetch_product['name']); ?>">
                                            </div>
                                            <div class="product-info">
                                                <div class="product-details" style="flex: 2;">
                                                    <input type="hidden" name="product_id" value="<?= $fetch_product['sanpham_id']?>">
                                                    <a href="view_page.php?pid=<?= $fetch_product['sanpham_id']?>" class="name"><?= htmlspecialchars($fetch_product['name']); ?></a>
                                                    <p class="" style="color: #525252;">Kích thước: <?= $fetch_order_item['size']; ?></p>
                                                    <p>x <?= $fetch_order_item['soluong']; ?></p>
                                                </div>
                                                <div class="product-price" style="flex: 1; text-align: right;">
                                                    <p><?= number_format($fetch_order_item['price']* $fetch_order_item['soluong'], 0, ',', '.'); ?> VNĐ</p>
                                                </div>

                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo '<p>Không có sản phẩm nào trong đơn hàng này.</p>';
                                }
                                ?>
                            </div>
                            <hr style="border: .5px solid #ccc;"> <!-- Đường gạch ngang -->
        
                            <!-- Hiển thị tổng tiền cuối box -->
                            <div class="order-total" style="text-align: right; margin-top: 20px;">
                                <p>Tổng tiền hàng: <?= number_format($fetch_bill['tongtien'], 0, ',', '.'); ?> VNĐ</p>
                                <p>Phí vận chuyển: <?= number_format($fetch_bill['phivanchuyen'], 0, ',', '.'); ?> VNĐ</p>
                                <p>Giảm giá: - <?= number_format($fetch_bill['tiendagiam'], 0, ',', '.'); ?> VNĐ</p>
                                <p><strong>Thành tiền: <?= number_format($tongthanhtoan, 0, ',', '.'); ?> VNĐ</strong></p>
                                <?php
                                if ($fetch_bill['phuongthucthanhtoan'] == 'Thanh toán bằng Momo'){
                                ?>
                                    <p><strong>Đã thanh toán bằng Momo: <?= number_format($tongthanhtoan, 0, ',', '.'); ?> VNĐ</strong></p>                              
                                <?php } ?>
                            </div>
                            <?php 
                            if ($fetch_bill['trangthai'] == 'Chờ xác nhận') {
                            ?>
                            <div class="cancel-button-container">
                                <form action="" method="post">
                                    <button type="submit" name="cancel" class="btn" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">Hủy đơn hàng</button>
                                </form>
                            </div>
                            <?php } elseif ($fetch_bill['trangthai'] == 'Đang giao hàng') {?>
                            <div class="cancel-button-container">
                                <form action="" method="post">
                                    <button type="submit" name="received" class="btn" onclick="return confirm('Bạn vui lòng chỉ nhấn `OK` khi đã nhận được sản phẩm và sản phẩm không có vấn đề nào.');">Đã nhận hàng</button>
                                </form>
                            </div>
                            <?php } ?>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty"><p>Bạn chưa có đơn hàng nào.</p></div>';
                }
            ?>
            <div class="back">
                <a href="order.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
        </div>
    </div>

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>