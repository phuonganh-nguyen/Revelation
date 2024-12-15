<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    $khach_id = $_GET['user_id'];
    $messages = $conn->prepare("SELECT * FROM send_message WHERE user_id = ? ORDER BY ngaygui ASC");
    $messages->execute([$khach_id]);

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    if (isset($_POST['send_message'])) {
        $admin_message = trim($_POST['message']);
        $current_time = date('Y-m-d H:i:s');
        if (!empty($admin_message)) {            
            $stmt = $conn->prepare("INSERT INTO reply_message (admin_id, user_id, phanhoi, ngaygui) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $khach_id, $admin_message, $current_time]);
        }else {
            $warning_msg[] = 'Vui lòng nhập nội dung tin nhắn!';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Admin message</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
</head>
<body id="acc-page"> 
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?> 
        <section class="user-container">
            <div class="back">
                <a href="admin_message.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
            <div class="box-container mess-box" style="margin-top: -2.5rem;">
                <div class="box">
                    <?php 
                        $select_user = $conn->prepare("SELECT name FROM user WHERE user_id = ?");
                        $select_user->execute([$khach_id]);
                        $user_info = $select_user->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <div class="head">
                        <h2><?= htmlspecialchars($user_info['name']); ?></h2> <!-- Hiển thị tên người dùng -->
                    </div>
                    <ul class="chatbox">
                        <?php 
                            // Kết hợp tin nhắn từ send_message và reply_message, sắp xếp theo ngày gửi
                            $messages_query = "
                                (SELECT 'user' AS sender, noidung AS message, sanpham_id, ngaygui, NULL AS admin_id FROM send_message WHERE user_id = ?)
                                UNION
                                (SELECT 'admin' AS sender, phanhoi AS message, NULL AS sanpham_id, ngaygui, admin_id FROM reply_message WHERE user_id = ?)
                                ORDER BY ngaygui ASC
                            ";
                            $messages = $conn->prepare($messages_query);
                            $messages->execute([$khach_id, $khach_id]);

                            foreach ($messages as $message): 
                                if ($message['sender'] == 'user'): // Tin nhắn từ người dùng
                                    if (!empty($message['sanpham_id'])) {
                                        // Nếu có sanpham_id, lấy thông tin từ bảng sanpham
                                        $product_query = $conn->prepare("SELECT name, image FROM sanpham WHERE sanpham_id = ?");
                                        $product_query->execute([$message['sanpham_id']]);
                                        $product = $product_query->fetch(PDO::FETCH_ASSOC);
                                    }
                                    ?>
                                    <li class="chat incoming">
                                        <?php if (!empty($message['sanpham_id']) && !empty($product)): ?>
                                            <p class="link-page"><?php echo htmlspecialchars($product['name']); ?></p>
                                            <img src="../uploaded_files/<?php echo htmlspecialchars($product['image']); ?>" alt="Product" class="product-image">
                                        <?php else: ?>
                                            <p><?php echo htmlspecialchars($message['message']); ?><br>
                                                <span class="time-send"><?php echo date('H:i d/m/Y', strtotime($message['ngaygui'])); ?></span>
                                            </p>
                                        <?php endif; ?>
                                    </li>
                                <?php else: // Tin nhắn từ admin ?>
                                    <li class="chat outgoing">
                                        <?php 
                                            $select_admin = $conn->prepare("SELECT name FROM user WHERE user_id = ?");
                                            $select_admin->execute([$message['admin_id']]);
                                            $admin_info = $select_admin->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <p>
                                            <span class="time-send"><?=  $admin_info['name']?></span>
                                            <?php echo htmlspecialchars($message['message']); ?><br>
                                            <span class="time-send"><?php echo date('H:i d/m/Y', strtotime($message['ngaygui'])); ?></span>
                                        </p>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                    </ul>
                    <div class="chat-input">
                        <form id="chat-form" method="POST">
                            <input type="hidden" name="receiver_id" id="receiver-id" value="<?= $user_id; ?>">
                            <textarea name="message" placeholder="Nhập tin nhắn..." required></textarea>
                            <span id="send-btn" class=""><button name="send_message">Gửi</button></span>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatbox = document.querySelector(".chatbox");

            // Cuộn đến tin nhắn mới nhất khi trang tải
            chatbox.scrollTop = chatbox.scrollHeight;
        });

        const handleChat = () => {
            userMessage = chatInput.value.trim();
            if (!userMessage) return;

            // Thêm tin nhắn ra màn hình
            chatbox.appendChild(createChatLi(userMessage, "outgoing"));

            // Cuộn đến tin nhắn mới nhất
            chatbox.scrollTop = chatbox.scrollHeight;

            // Gửi tin nhắn tới máy chủ để lưu vào cơ sở dữ liệu
            document.getElementById("chat-form").submit();
        };

    </script>
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>