<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }
    include 'component/send_message.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Search preferences page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="login-page">
    <?php include 'component/user_header.php' ?>
    <img src="images/Banner/bg.jpg" alt="" id="bg-video">
    <div class="overlay"></div>
    <section class="form-container" style="margin-top: -5rem;">
        <div class="chatbot">
            <div class="head">
                <h2>R É V É L A T I O N</h2>
            </div>
            
            <ul class="chatbox">
                <li class="chat incoming">
                    <img src="images\logo1.png" alt="User" class="re-icon">
                    <p>Xin chào! Ré có thể giúp gì cho bạn</p>
                </li>
                <?php 
                    $reply_messages = $conn->prepare("SELECT * FROM reply_message WHERE user_id = ? ORDER BY ngaygui ASC");
                    $reply_messages->execute([$user_id]);
                ?>
                <?php 
                // Truy vấn UNION để kết hợp reply_message và send_message
                $all_messages = $conn->prepare("
                    SELECT 'incoming' AS type, phanhoi AS message, ngaygui, NULL AS sanpham_id 
                    FROM reply_message 
                    WHERE user_id = ?
                    UNION ALL
                    SELECT 'outgoing' AS type, noidung AS message, ngaygui, sanpham_id 
                    FROM send_message 
                    WHERE user_id = ?
                    ORDER BY ngaygui ASC
                ");
                $all_messages->execute([$user_id, $user_id]);

                while ($msg = $all_messages->fetch(PDO::FETCH_ASSOC)):
                    if ($msg['type'] === 'outgoing' && !empty($msg['sanpham_id'])) {
                        // Truy vấn lấy thông tin sản phẩm nếu có sanpham_id
                        $product_query = $conn->prepare("SELECT name, image FROM sanpham WHERE sanpham_id = ?");
                        $product_query->execute([$msg['sanpham_id']]);
                        $product = $product_query->fetch(PDO::FETCH_ASSOC);
                    }
                ?>
                    <?php if ($msg['type'] === 'incoming'): ?>
                        <li class="chat incoming">
                            <img src="images/logo1.png" alt="User" class="re-icon">
                            <p>
                                <?php echo htmlspecialchars($msg['message']); ?><br>
                                <span class="time-send"><?php echo date('H:i d/m/Y', strtotime($msg['ngaygui'])); ?></span>
                            </p>
                        </li>
                    <?php else: ?>
                        <li class="chat outgoing">
                            <?php if (!empty($msg['sanpham_id']) && !empty($product)): ?>
                                <!-- Hiển thị thông tin sản phẩm -->
                                <img src="uploaded_files/<?php echo htmlspecialchars($product['image']); ?>" alt="Product" class="product-image">
                                <p onclick="window.location.href='view_page.php?pid=<?= $msg['sanpham_id'] ?>'" class="link-page">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </p>
                            <?php else: ?>
                                <!-- Hiển thị nội dung tin nhắn -->
                                <p>
                                    <?php echo htmlspecialchars($msg['message']); ?><br>
                                    <span class="time-send"><?php echo date('H:i d/m/Y', strtotime($msg['ngaygui'])); ?></span>
                                </p>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endwhile; ?>
            </ul>
            <div class="chat-input">
                <form id="chat-form" method="POST">
                    <textarea name="message" placeholder="Nhập tin nhắn..." required></textarea>
                    <span id="send-btn" class=""><button name="send_message">Gửi</button></span>
                </form>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatbox = document.querySelector(".chatbox");
            chatbox.scrollTop = chatbox.scrollHeight;
        });

        const handleChat = () => {
            userMessage = chatInput.value.trim();
            if (!userMessage) return;
            chatbox.appendChild(createChatLi(userMessage, "outgoing"));
            chatbox.scrollTop = chatbox.scrollHeight;
            document.getElementById("chat-form").submit();
        };

    </script>
    <script src="js/sweetalert.js"></script>
    <script src="js/script.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>