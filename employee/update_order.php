<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh'); // Thiết lập múi giờ Việt Nam
    include '../component/connect.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../vendor/autoload.php';

    if (isset($_COOKIE['nv_id'])) {
        $user_id = $_COOKIE['nv_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    if (isset($_GET['get_id'])) {
        $get_id = $_GET['get_id'];

        // Truy vấn thông tin đơn hàng từ bảng hoadon
        $select_bill = $conn->prepare("SELECT * FROM `bill` WHERE bill_id = ?");
        $select_bill->execute([$get_id]);

        if ($select_bill->rowCount() > 0) {
            $fetch_bill = $select_bill->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "<script>alert('Không tìm thấy đơn hàng này!');</script>";
            header('location:order.php');
        }
    } else {
        $get_id = '';
        header('location:order.php');
    }
    if (isset($_POST['order_status'])) {
        $new_status = $_POST['order_status'];   
        // Cập nhật trạng thái và ngày tương ứng
        if ($new_status === 'confirmed') {
            $update_bill = $conn->prepare("UPDATE `bill` SET `trangthai` = 'Đóng hàng', `ngaydonghang` = NOW(), admin_id=? WHERE `bill_id` = ?");
            $update_bill->execute([$user_id, $get_id]);
        } elseif ($new_status === 'shipped') {
            $update_bill = $conn->prepare("UPDATE `bill` SET `trangthai` = 'Đang giao hàng', `ngaygiaohang` = NOW(), admin_id=? WHERE `bill_id` = ?");
            $update_bill->execute([$user_id, $get_id]);
        } elseif ($new_status === 'cancelled') {
            $update_bill = $conn->prepare("UPDATE `bill` SET `trangthai` = 'Đã hủy', `ngayhuy` = NOW(), admin_id=? WHERE `bill_id` = ?");
            $update_bill->execute([$user_id, $get_id]);
            if ($fetch_bill['tiendagiam'] > 0){
                $sudung = 70;
                $update_user = $conn->prepare("UPDATE `user` SET diem = diem + ? WHERE user_id=?");
                $update_user->execute([$sudung, $fetch_bill['user_id']]);
            }
        }       
        $success_msg[] = 'Trạng thái đơn hàng đã được cập nhật';
        $select_bill = $conn->prepare("SELECT * FROM `bill` WHERE bill_id = ?");
        $select_bill->execute([$get_id]);
        $fetch_bill = $select_bill->fetch(PDO::FETCH_ASSOC); // Cập nhật biến fetch_bill với thông tin mới
        // Truy vấn lại thông tin đơn hàng để kiểm tra biến thongbao và cập nhật trạng thái mới nhất
        if ($fetch_bill['thongbao'] == 1) {
            $mail = new PHPMailer(true);
            $user_id = $fetch_bill['user_id'];
        
            // Lấy `name` và `email` từ bảng `user`
            $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
            $select_user->execute([$user_id]);
            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
            // Kiểm tra xem có kết quả từ bảng `user` không
            if (!$fetch_user) {
                $warning_msg[] = 'Không tìm thấy thông tin người dùng.';
                return;
            }
            $email = $fetch_user['email'];
            $name = $fetch_user['name'];
            
            try {
                // Cấu hình email
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'loverevelationshop@gmail.com';
                $mail->Password = 'g u w j x u t f u p j m c z r p';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('loverevelationshop@gmail.com', 'RÉVÉLATION');
                $mail->addAddress($email, htmlspecialchars($name));
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';

                // Nội dung email theo trạng thái
                if ($new_status === 'confirmed') {
                    $mail->Subject = "Đơn hàng #$get_id đã được xác nhận";
                    $mail->Body = "<p>Xin chào $name,</p>";
                    $mail->Body .= "<p>Đơn hàng <strong>#" . htmlspecialchars($get_id) . "</strong> của bạn đã được xác nhận và đang trong quá trình đóng gói vào ngày " . date('d/m/Y') . ".</p>";
                    $mail->Body .= "<p>Bạn có thể xem thông tin chi tiết đơn hàng <a href='http://localhost/web/view_order.php?get_id=$get_id'>tại đây</a>.</p>";
                    $mail->Body .= "<p>Nếu có bất kỳ thắc mắc nào, xin vui lòng liên hệ chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>";
                    $mail->Body .= "<p>Trân trọng,</p>";
                    $mail->Body .= "<p><a href='http://localhost/web/home.php'><strong>RÉVÉLATION</strong></a></p>";
                    $mail->Body .= "<footer><p>Số điện thoại: 1800 3015 | Email: loverevelationshop@gmail.com</p></footer>";
                } elseif ($new_status === 'shipped') {
                    $mail->Subject = "Đơn hàng #$get_id đang được vận chuyển";
                    $mail->Body = "<p>Xin chào $name,</p>";
                    $mail->Body .= "<p>Đơn hàng <strong>#" . htmlspecialchars($get_id) . "</strong> của bạn đã được bàn giao cho đơn vị vận chuyển vào ngày " . date('d/m/Y') . ".</p>";
                    $mail->Body .= "<p>Bạn có thể xem thông tin chi tiết đơn hàng <a href='http://localhost/web/view_order.php?get_id=$get_id'>tại đây</a>.</p>";
                    $mail->Body .= "<p>Nếu có bất kỳ thắc mắc nào, xin vui lòng liên hệ chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>";
                    $mail->Body .= "<p>Trân trọng,</p>";
                    $mail->Body .= "<p><a href='http://localhost/web/home.php'><strong>RÉVÉLATION</strong></a></p>";
                    $mail->Body .= "<footer><p>Số điện thoại: 1800 3015 | Email: loverevelationshop@gmail.com</p></footer>";
                } elseif ($new_status === 'cancelled') {
                    $mail->Subject = "Đơn hàng #$get_id đã được hủy";
                    $mail->Body = "<p>Xin chào $name,</p>";
                    $mail->Body .= "<p>Đơn hàng <strong>#" . htmlspecialchars($get_id) . "</strong> của bạn đã được hủy vào ngày " . date('d/m/Y') . ".</p>";
                    $mail->Body .= "<p>Bạn có thể xem thông tin chi tiết đơn hàng <a href='http://localhost/web/view_order.php?get_id=$get_id'>tại đây</a>.</p>";
                    $mail->Body .= "<p>Nếu có bất kỳ thắc mắc nào, xin vui lòng liên hệ chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>";
                    $mail->Body .= "<p>Trân trọng,</p>";
                    $mail->Body .= "<p><a href='http://localhost/web/home.php'><strong>RÉVÉLATION</strong></a></p>";
                    $mail->Body .= "<footer><p>Số điện thoại: 1800 3015 | Email: loverevelationshop@gmail.com</p></footer>";
                }
                
                $mail->send();
            } catch (Exception $e) {
                $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
            }
        }
    }   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - User order detail</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page"> 
    <div class="main-container">
        <?php include '../component/employee_header.php'; ?> 
        <section class="user-container">
            <div class="back">
                <a href="employee_order.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
            <div class="heading">
                <h1>Chi tiết đơn hàng</h1>
            </div>
            <?php 
                function formatDateTime($datetime) {
                    return date('H:i d/m/Y', strtotime($datetime)); // Giờ:phút ngày/tháng/năm
                }
            ?>
            <div class="order-status">
                <div class="order-id">
                    <h3>Đơn hàng #<?= $fetch_bill['bill_id']; ?> (<?= formatDateTime($fetch_bill['ngaydat'])?>)</h3>
                </div>
                <div class="order-pending">
                    <?php if ($fetch_bill['trangthai'] == 'Chờ xác nhận'): ?>
                        <!-- Nếu trạng thái là 'pending', cho phép xác nhận hoặc hủy -->
                        <form action="" method="POST">
                            <select name="order_status" id="order_status" onchange="this.form.submit()">
                                <option value="pending" <?= $fetch_bill['trangthai'] == 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                <option value="confirmed">Xác nhận và đóng hàng</option>
                                <option value="cancelled">Hủy</option>
                            </select>
                            <input type="hidden" name="bill_id" value="<?= $fetch_bill['bill_id']; ?>">
                        </form>
                    <?php elseif ($fetch_bill['trangthai'] == 'Đóng hàng'): ?>
                        <!-- Nếu trạng thái là 'confirmed', cho phép giao hàng hoặc hủy -->
                        <form action="" method="POST">
                            <select name="order_status" id="order_status" onchange="this.form.submit()">
                                <option value="confirmed" <?= $fetch_bill['trangthai'] == 'confirmed' ? 'selected' : ''; ?>>Xác nhận và đóng hàng</option>
                                <option value="shipped">Giao hàng</option>
                                <option value="cancelled">Hủy</option>
                            </select>
                            <input type="hidden" name="bill_id" value="<?= $fetch_bill['bill_id']; ?>">
                        </form>
                    <?php elseif ($fetch_bill['trangthai'] == 'Đang giao hàng'): ?>
                        <form action="" method="POST">
                            <select name="order_status" id="order_status" onchange="this.form.submit()">
                                <option value="shipped">Giao hàng</option>
                                <option value="cancelled">Hủy</option>
                            </select>
                            <input type="hidden" name="bill_id" value="<?= $fetch_bill['bill_id']; ?>">
                        </form>
                    <?php else: ?>
                        <p style="font-size: 1.1rem;">Trạng thái: <strong><?= ucfirst($fetch_bill['trangthai']); ?></strong></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="box-container">
                <div class="order-flex-container">
                    <!-- Sản phẩm -->
                    <div class="order-left">
                        <div class="order-box">
                            <?php
                            $select_products = $conn->prepare("SELECT * FROM `hoadon` WHERE hoadon_id=?");
                            $select_products->execute([$fetch_bill['hoadon_id']]);
                            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                                $select_sanpham = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id = ?");
                                $select_sanpham->execute([$fetch_product['sanpham_id']]);
                                $fetch_sanpham = $select_sanpham->fetch(PDO::FETCH_ASSOC);

                                $sub_total = $fetch_product['price'] * $fetch_product['soluong'];
                            ?>
                                <div class="product-item">
                                    <img src="../uploaded_files/<?= $fetch_sanpham['image']; ?>" alt="<?= $fetch_sanpham['name']; ?>" class="small-img">
                                    <div class="product-details">
                                        <p class="product-name"><?= $fetch_sanpham['name']; ?></p>
                                        <p class="product-size">(<?= $fetch_product['size']; ?>)</p>
                                        <p class="product-size">Số lượng: <?= $fetch_product['soluong']; ?></p>
                                    </div>
                                    <div class="product-price">
                                        <p><?= number_format($fetch_product['price'], 0, ',', '.'); ?></p>
                                    </div>
                                    <div class="product-total">
                                        <p><?= number_format($sub_total, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Tổng kết thanh toán -->
                        <div class="order-box bottom">
                            <div class="summary-item">
                                <p>Tổng tiền hàng:</p>
                                <p><?= number_format($fetch_bill['tongtien'], 0, ',', '.'); ?> VNĐ</p>
                            </div>
                            <div class="summary-item">
                                <p>Phí vận chuyển:</p>
                                <p><?= number_format($fetch_bill['phivanchuyen'], 0, ',', '.'); ?> VNĐ</p>
                            </div>
                            <div class="summary-item">
                                <p>Giảm giá:</p>
                                <p>- <?= number_format($fetch_bill['tiendagiam'], 0, ',', '.'); ?> VNĐ</p>
                            </div>
                            <div class="summary-item">
                                <strong><p>Thành tiền:</p></strong>
                                <strong><p><?= number_format($fetch_bill['tongthanhtoan'], 0, ',', '.'); ?> VNĐ</p></strong>
                            </div>
                            <?php
                                if ($fetch_bill['phuongthucthanhtoan'] == 'Thanh toán bằng Momo'){
                                ?>
                                <div class="summary-item">
                                    <strong><p>Đã thanh toán:</p></strong>
                                    <strong><p><?= number_format($fetch_bill['tongthanhtoan'], 0, ',', '.'); ?> VNĐ</p></strong>
                                </div>
                                <?php } ?>
                        </div>

                    </div>                    
                    <div class="order-right">
                        <div class="order-box">
                            <p><strong>Người nhận: <?= $fetch_bill['name']; ?></strong></p>
                            <p>ID: #<?= $fetch_bill['user_id']; ?></p>
                            <p>Địa chỉ: <?= $fetch_bill['address']; ?></p>
                            <p>Điện thoại: <?= $fetch_bill['phone']; ?></p>
                            <hr>
                            <?php
                            
                            switch($fetch_bill['trangthai']) {
                                case 'Chờ xác nhận':
                                    echo "<div class='summary-row'><p><strong>Đặt hàng thành công</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaydat']) . "</p></div>";
                                    break;
                    
                                case 'Đóng hàng':
                                    echo "<div class='summary-row'><p><strong>Đã xác nhận, đang đóng hàng</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaydonghang']) . "</p></div>";
                                    echo "<div class='summary-row'><p><strong>Đặt hàng thành công</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaydat']) . "</p></div>";
                                    break;
                    
                                case 'Đang giao hàng':
                                    echo "<div class='summary-row'><p><strong>Đang giao hàng</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaygiaohang']) . "</p></div>";
                                    echo "<div class='summary-row'><p><strong>Đã xác nhận, đang đóng hàng</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaydonghang']) . "</p></div>";
                                    echo "<div class='summary-row'><p><strong>Đặt hàng thành công</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaydat']) . "</p></div>";
                                    break;
                    
                                case 'Đã nhận hàng':
                                    echo "<div class='summary-row'><p><strong style='color: green;'>Đã nhận hàng</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaynhan']) . "</p></div>";
                                    echo "<div class='summary-row'><p><strong>Đang giao hàng</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaygiaohang']) . "</p></div>";
                                    echo "<div class='summary-row'><p><strong>Đã xác nhận, đang đóng hàng</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaydonghang']) . "</p></div>";
                                    echo "<div class='summary-row'><p><strong>Đặt hàng thành công</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaydat']) . "</p></div>";
                                    break;
                    
                                case 'Đã hủy':
                                    echo "<div class='summary-row'><p><strong style='color: red;'>Đã hủy</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngayhuy']) . "</p></div>";
                                    echo "<div class='summary-row'><p><strong>Đặt hàng thành công</strong></p><p class='time'>" . formatDateTime($fetch_bill['ngaydat']) . "</p></div>";
                                    break;;
                            }
                            ?>

                        </div>
                    </div>                  
                </div>
            </div>
        </section>
    </div>
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
