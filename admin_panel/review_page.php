<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
include '../component/connect.php';
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:../login.php');
}

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
} else{
    $post_id = '';
    header('location:view_product.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_response'])) {
    $review_id = $_POST['review_id'];
    $response_content = trim($_POST['response_content']);
    
    if (!empty($response_content)) {
        // Cập nhật cột phanhoi trong bảng danhgia
        $update_response = $conn->prepare("UPDATE danhgia SET phanhoi = ? WHERE danhgia_id = ?");
        $update_response->execute([$response_content, $review_id]);
        $success_msg[] = 'Đã gửi phản hồi!';
    } else {
        $warning_msg[] = 'Vui lòng nhập phản hồi trước khi gửi!';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Review page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page">
<div class="main-container">
        <?php include '../component/admin_header.php'; ?> 
        <section class="user-container">
            <div class="back">
                <a href="view_product.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
            <div class="heading">
                <?php
                // Lấy tên sản phẩm từ bảng `sanpham` dựa vào `post_id`
                $select_product = $conn->prepare("SELECT name FROM `sanpham` WHERE sanpham_id = ?");
                $select_product->execute([$post_id]);
                $product = $select_product->fetch(PDO::FETCH_ASSOC);
                ?>
                <h1><?= htmlspecialchars($product['name']); ?></h1> <!-- Hiển thị tên sản phẩm -->
            </div>
            <div class="box-container">
                <div class="box">
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                        <thead>
                            <tr>
                                <th>Ngày gửi</th>
                                <th>ID KH</th>
                                <th>Số sao</th>
                                <th>Nội dung</th>
                                <th>Phản hồi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Lấy đánh giá theo sản phẩm
                            $select_users = $conn->prepare("SELECT * FROM `danhgia` WHERE sanpham_id = ? ORDER BY ngaygui DESC");
                            $select_users->execute([$post_id]);

                            if ($select_users->rowCount() > 0) {
                                while ($fetch_user = $select_users->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($fetch_user['ngaygui'])); ?></td>
                                        <td><?= htmlspecialchars($fetch_user['user_id']); ?></td>
                                        <td><?= htmlspecialchars($fetch_user['sao']); ?></td>
                                        <td><?= htmlspecialchars($fetch_user['noidung']); ?></td>
                                        <td>
                                            <?php if (!empty($fetch_user['phanhoi'])) { ?>
                                                <p><?= htmlspecialchars($fetch_user['phanhoi']); ?></p>
                                            <?php } else { ?>
                                                <form action="" method="post" class="review-page">
                                                    <input type="hidden" name="review_id" value="<?= $fetch_user['danhgia_id']; ?>">
                                                    <input type="text" name="response_content" placeholder="Nhập phản hồi của bạn hoặc chọn từ danh sách" required>
                                                    <button type="submit" name="submit_response" class="review-link">Gửi</button>
                                                    <select name="quick_response" onchange="updateQuickResponse(this)">
                                                        <option value="">Chọn phản hồi nhanh</option>
                                                        <option value="Cảm ơn bạn đã đánh giá!">Cảm ơn bạn đã đánh giá!</option>
                                                        <option value="Cảm ơn bạn đã chia sẻ ý kiến!">Cảm ơn bạn đã chia sẻ ý kiến!</option>
                                                        <option value="Chúng tôi rất biết ơn sự đóng góp của bạn!">Chúng tôi rất biết ơn sự đóng góp của bạn!</option>
                                                        <option value="Cảm ơn bạn đã giúp chúng tôi cải thiện dịch vụ!">Cảm ơn bạn đã giúp chúng tôi cải thiện dịch vụ!</option>
                                                        <option value="Cảm ơn bạn đã tin tưởng và ủng hộ chúng tôi!">Cảm ơn bạn đã tin tưởng và ủng hộ chúng tôi!</option>
                                                    </select>
                                                </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="4" style="text-align: center;">Không có đánh giá!</td>
                                    </tr>
                                ';
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>
    <script>
        function updateQuickResponse(selectElement) {
            // Lấy phần tử input trong cùng form của select hiện tại
            const inputElement = selectElement.closest('form').querySelector('input[name="response_content"]');
            inputElement.value = selectElement.value;
            selectElement.value = ""; // Reset dropdown sau khi chọn
        }
    </script>
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>