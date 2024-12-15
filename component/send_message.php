<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    if (isset($_POST['send_message'])) {
        if (!empty($user_id)) {
            $message = $_POST['message'];

            // Kiểm tra nội dung tin nhắn
            if (!empty($message)) {
                // Lấy thời gian hiện tại theo múi giờ Việt Nam
                $current_time = date('Y-m-d H:i:s');

                $insert_message = $conn->prepare("INSERT INTO `send_message` (user_id, noidung, ngaygui) VALUES (?, ?, ?)");
                $insert_message->execute([$user_id, $message, $current_time]);
            } else {
                $warning_msg[] = 'Vui lòng nhập nội dung tin nhắn!';
            }
        } else {
            $warning_msg[] = 'Vui lòng đăng nhập!';
        }
    }
?>