<?php 
include '../component/connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

if (isset($_COOKIE['khach_id'])) {
    $user_id = $_COOKIE['khach_id'];
} else{
    $user_id = '';
    header('location:login.php');
}

// Kiểm tra xem có bill_id trong URL không
if (isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];

    // Truy vấn thông tin hóa đơn
    $select_bill = $conn->prepare("SELECT * FROM `bill` WHERE bill_id = ?");
    $select_bill->execute([$bill_id]);

    if ($select_bill->rowCount() > 0) {
        $bill_info = $select_bill->fetch(PDO::FETCH_ASSOC);
        $tien_dagiam = $bill_info['tiendagiam']; // Giả sử có trường tien_dagiam trong bảng bill
        $ngaydat = strtotime($bill_info['ngaydat']); // Thời gian đặt hóa đơn
        $current_time = time(); // Thời gian hiện tại

        // Kiểm tra thời gian đã trôi qua và phương thức thanh toán
        if ($bill_info['phuongthucthanhtoan'] === 'Thanh toán bằng MoMo' && ($current_time - $ngaydat) >= 300 && $bill_info['tinhtrangthanhtoan'] !== 'Đã thanh toán') {
            // Cập nhật tình trạng đơn hàng thành "Đã hủy"
            $update_cancelled = $conn->prepare("UPDATE bill SET tinhtrangthanhtoan = 'Đã hủy' WHERE bill_id = ?");
            $update_cancelled->execute([$bill_id]);
            header('Location: ../order.php'); 
            exit();
        }

        $update_bill = $conn->prepare("UPDATE bill SET hienthi = 1, trangthai = 'Chờ xác nhận', tinhtrangthanhtoan = 'Đã thanh toán' WHERE bill_id = ?");
        $update_bill->execute([$bill_id]);


        // Kiểm tra xem tiền giảm có lớn hơn 0 không
        if ($tien_dagiam > 0) {
            $sudung = 70;
            $dungvaongay = date('Y-m-d'); // Ngày sử dụng điểm
            $stmt_points = $conn->prepare("INSERT INTO diemsudung (user_id, sudung, dungvaongay) VALUES (?, ?, ?)");
            $stmt_points->execute([$user_id, $sudung, $dungvaongay]);
            $update_user = $conn->prepare("UPDATE `user` SET diem = diem - ? WHERE user_id=?");
            $update_user->execute([$sudung, $user_id]);
        }

        // Xóa giỏ hàng của người dùng
        $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $delete_cart->execute([$user_id]);

        // Lấy thông tin phí vận chuyển và ngày giao hàng
        $ht_vanchuyen = $bill_info['ht_vanchuyen']; // Lấy thông tin phương thức vận chuyển từ hóa đơn
        if ($ht_vanchuyen == 'Giao hàng tiết kiệm') {
            // $phivanchuyen = 25000;
            $estimated_delivery_start = date('d/m/Y', strtotime('+5 days')); // Ngày bắt đầu dự kiến
            $estimated_delivery_end = date('d/m/Y', strtotime('+7 days'));   // Ngày kết thúc dự kiến
        } elseif ($ht_vanchuyen == 'Giao hàng nhanh') {
            // $phivanchuyen = 30000;
            $estimated_delivery_start = date('d/m/Y', strtotime('+2 days')); // Ngày bắt đầu dự kiến
            $estimated_delivery_end = date('d/m/Y', strtotime('+3 days'));   // Ngày kết thúc dự kiến
        }

        if ($bill_info['thongbao'] == 1) {
            $select_user = $conn->prepare("SELECT * FROM `user` WHERE user_id=?");
            $select_user->execute([$user_id]);
            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
            $email = $fetch_user['email'];
            $name = $fetch_user['name'];

            $address = $bill_info['address']; // Địa chỉ giao hàng từ bảng bill
            $phone = $bill_info['phone']; // Số điện thoại từ bảng bill
            $tongtien = $bill_info['tongtien']; // Tổng tiền từ bảng bill
            $discount = $bill_info['tiendagiam']; // Giảm giá từ bảng bill
            $tongthanhtoan = $bill_info['tongthanhtoan'];
            $phivanchuyen = $bill_info['phivanchuyen'];
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
                $mail->Body .= "<li><strong>Đã thanh toán:</strong> " . number_format($tongthanhtoan) . "VNĐ</li>";
                $mail->Body .= '<li><strong>Địa chỉ giao hàng:</strong> ' . htmlspecialchars($address) . '</li>';
                $mail->Body .= "<li><strong>Số điện thoại liên hệ:</strong> $phone</li>";
                $mail->Body .= "<li><strong>Thời gian giao hàng dự kiến:</strong> $estimated_delivery_start - $estimated_delivery_end</li>";
                $mail->Body .= "</ul>";
                $mail->Body .= "<p>Bạn có thể xem thông tin chi tiết đơn hàng <a href='http://localhost/web/view_order.php?get_id=$bill_id'>tại đây</a>.</p>";
                $mail->Body .= "<p>Nếu có bất kỳ thắc mắc nào, xin vui lòng liên hệ chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>";
                $mail->Body .= "<p>Trân trọng,</p>";
                $mail->Body .= "<p><strong>RÉVÉLATION</strong></p>";
                $mail->Body .= "<footer><p>Số điện thoại: 1800 3015 | Email: loverevelationshop@gmail.com</p></footer>";
        
                $mail->send();
                header("Location: ../order.php");
                exit;
            } catch (Exception $e) {
                $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
            }
        }
        header('Location: ../order.php'); 
        exit();
    } else {
        echo "Không tìm thấy hóa đơn.";
    }
} else {
    echo "Không có bill_id trong URL.";
}
?>