<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh'); // Thiết lập múi giờ Việt Nam
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }
    if (isset($_GET['bill_id'])) {
        $get_id = $_GET['bill_id'];
    } else{
        $get_id = '';
        header('location:order.php');
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_rating'])) {
        $sanpham_id = $_POST['sanpham_id'];
        $hoadon_id = $_POST['hoadon_id'];
        $user_id = $_POST['user_id'];
        $rate = $_POST['rate'];
        $noidung = $_POST['noidung'];
    
        // Kiểm tra các biến không rỗng trước khi thêm vào bảng đánh giá
        if (!empty($sanpham_id) && !empty($hoadon_id) && !empty($user_id) && !empty($rate) && !empty($noidung)) {
            $insert_review = $conn->prepare("INSERT INTO `danhgia` (sanpham_id, hoadon_id, user_id, sao, noidung, ngaygui) VALUES (?, ?, ?, ?, ?, NOW())");
            $insert_review->execute([$sanpham_id, $hoadon_id, $user_id, $rate, $noidung]);
            // Cập nhật trạng thái đánh giá thành "xong" trong tất cả các hàng phù hợp trong bảng hoadon
            $update_hoadon = $conn->prepare("UPDATE `hoadon` SET danhgia = 'xong' WHERE hoadon_id = ? AND sanpham_id = ?");
            $update_hoadon->execute([$hoadon_id, $sanpham_id]);

            // Kiểm tra xem tất cả các hoadon_id đã được đánh giá "xong" hay chưa
            $check_completed = $conn->prepare("SELECT COUNT(*) FROM `hoadon` WHERE hoadon_id = ? AND danhgia = 'xong'");
            $check_completed->execute([$hoadon_id]);
            $completed_count = $check_completed->fetchColumn();

            // Đếm tổng số hoadon liên quan đến hoadon_id
            $total_count = $conn->prepare("SELECT COUNT(*) FROM `hoadon` WHERE hoadon_id = ?");
            $total_count->execute([$hoadon_id]);
            $total_reviews = $total_count->fetchColumn();

            // Nếu tất cả đánh giá đã hoàn thành, cập nhật bảng bill
            if ($total_reviews > 0 && $total_reviews == $completed_count) {
                // Cập nhật danhgia = 'xong' cho bill_id từ biến get_id
                $update_bill = $conn->prepare("UPDATE `bill` SET danhgia = 'xong' WHERE bill_id = ?");
                $update_bill->execute([$get_id]);
            }

            $success_msg[] = 'Đã gửi đánh giá!';
            // header('location:order_detail.php?get_id=' . $get_id);
        } 
    }

    // Truy vấn để lấy tất cả sản phẩm trong hóa đơn và đánh giá
    $select_order_items = $conn->prepare("SELECT h.*, s.*, d.noidung AS review_content, d.sao AS review_rating 
    FROM `hoadon` h
    LEFT JOIN `sanpham` s ON h.sanpham_id = s.sanpham_id
    LEFT JOIN `danhgia` d ON h.hoadon_id = d.hoadon_id AND h.sanpham_id = d.sanpham_id
    WHERE h.hoadon_id = (SELECT hoadon_id FROM `bill` WHERE bill_id = ?)");
    $select_order_items->execute([$get_id]);

    $products = [];
    while ($fetch_order_item = $select_order_items->fetch(PDO::FETCH_ASSOC)) {
        $sanpham_id = $fetch_order_item['sanpham_id'];

        // Tạo mảng sản phẩm
        $products[$sanpham_id] = [
            'fetch_product' => $fetch_order_item,
            'review_content' => $fetch_order_item['review_content'] ?? '',
            'review_rating' => $fetch_order_item['review_rating'] ?? '',
            'size' => $fetch_order_item['size']
        ];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Evaluete page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?= time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body id="storekeeper-page">
    <?php include 'component/user_header.php'; ?>
    <div class="orders">   
        <div class="heading">
            <h1>Đánh giá</h1>
        </div>
        
        <div class="box-container">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $sanpham_id => $product_data): ?>
                    <form action="" method="post" onsubmit="return validateForm(this)">
                        <input type="hidden" name="sanpham_id" value="<?= $sanpham_id; ?>">
                        <input type="hidden" name="hoadon_id" value="<?= $product_data['fetch_product']['hoadon_id']; ?>">
                        <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                        <div class="box">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div class="product-image">
                                    <img src="uploaded_files/<?= htmlspecialchars($product_data['fetch_product']['image']); ?>" alt="<?= htmlspecialchars($product_data['fetch_product']['name']); ?>">
                                </div>
                                <div class="product-info">
                                    <div class="product-details" style="flex: 2;">
                                        <p class="name"><?= htmlspecialchars($product_data['fetch_product']['name']); ?></p>
                                        <p style="color: #525252;">Kích thước: <?= htmlspecialchars($product_data['size']); ?></p>
                                    </div>
                                    <div class="product-price" style="flex: 1; text-align: right;">
                                        <div class="star-widget">
                                            <?php if ($product_data['review_content']): ?>
                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                    <input type="radio" name="rate" id="rate-<?= $i; ?>-<?= $sanpham_id; ?>" value="<?= $i; ?>" <?= ($product_data['review_rating'] == $i) ? 'checked' : ''; ?> disabled>
                                                    <label for="rate-<?= $i; ?>-<?= $sanpham_id; ?>" class="bi bi-star-fill"></label>
                                                <?php endfor; ?>
                                            <?php else: ?>
                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                    <input type="radio" name="rate" id="rate-<?= $i; ?>-<?= $sanpham_id; ?>" value="<?= $i; ?>" onclick="setDefaultReviewText(this, <?= $sanpham_id; ?>)">
                                                    <label for="rate-<?= $i; ?>-<?= $sanpham_id; ?>" class="bi bi-star-fill"></label>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <?php if (!$product_data['review_content']): ?>
                                    <input type="text" name="noidung" id="noidung-<?= $sanpham_id; ?>" placeholder="Viết đánh giá (dưới 100 ký tự)" class="review-input" maxlength="100">
                                <?php else: ?>
                                    <input type="text" value="<?= htmlspecialchars($product_data['review_content']); ?>" class="review-input" disabled>
                                <?php endif; ?>
                            </div>
                            <div class="cancel-button-container">
                                <?php if (!$product_data['review_content']): ?>
                                    <button type="submit" name="submit_rating" class="btn" style="width: 10rem;">Gửi đánh giá</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty"><p>Bạn chưa có đơn hàng nào.</p></div>
            <?php endif; ?>
            <div class="back">
                <a href="order_successful.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
        </div>
        
    </div>              

<script>
    function setDefaultReviewText(radio, sanpham_id) {
        const rating = radio.value;
        const reviewInput = document.getElementById('noidung-' + sanpham_id);
        
        let defaultText = '';
        switch (rating) {
            case '1':
                defaultText = 'Tệ';
                break;
            case '2':
                defaultText = 'Không hài lòng';
                break;
            case '3':
                defaultText = 'Bình thường';
                break;
            case '4':
                defaultText = 'Hài lòng';
                break;
            case '5':
                defaultText = 'Tuyệt vời';
                break;
        }
        
        reviewInput.value = defaultText;
    }

    function validateForm(form) {
        const rate = form.querySelector('input[name="rate"]:checked');
        const reviewInput = form.querySelector('input[name="noidung"]');
        
        if (!rate) {
            alert('Bạn cần chọn số sao trước khi gửi đánh giá!'); // Thông báo nhắc nhở
            return false; // Ngăn gửi biểu mẫu
        }
        
        // Nếu không có đánh giá nhập, có thể thêm tự động text đánh giá dựa vào số sao đã chọn
        if (!reviewInput.value) {
            const rating = rate.value;
            setDefaultReviewText({value: rating}, form.querySelector('input[name="sanpham_id"]').value);
        }
        
        return true; // Cho phép gửi biểu mẫu
    }
</script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'; ?>
</body>
</html>