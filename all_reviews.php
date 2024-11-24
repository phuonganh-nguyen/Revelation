<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh'); // Thiết lập múi giờ Việt Nam
    include 'component/connect.php';
    
    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }

    if (isset($_GET['pid'])) {
        $get_id = $_GET['pid'];
    } else{
        $get_id = '';
        header('location:view_page.php');
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
            <h1>Tất cả đánh giá</h1>
        </div>
        <div class="box-container" style="margin-bottom: 5rem;">
            <?php
                $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                $select_products->execute([$get_id]);
                $fetch_products = $select_products->fetch(); // Lấy dữ liệu sản phẩm

                if ($fetch_products) { // Kiểm tra xem có dữ liệu không
            ?>
            <div class="box">
                <div style="margin-top:1rem;">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div class="product-image">
                            <img src="uploaded_files/<?= htmlspecialchars($fetch_products['image']); ?>" alt="">
                        </div>
                        <div class="product-info">
                            <div class="product-details" style="flex: 2;">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($fetch_products['sanpham_id']); ?>">
                                <a href="view_page.php?pid=<?= htmlspecialchars($fetch_products['sanpham_id']); ?>" class="name"><?= htmlspecialchars($fetch_products['name']); ?></a>
                            </div>
                        </div>
                    </div>
                    <hr style="border: .5px solid #ccc;"> <!-- Đường gạch ngang -->
                </div>
                <div class="box-header">
                <h4>ĐÁNH GIÁ SẢN PHẨM</h4>
                <div class="button-container">
                    <!-- <button class="filter-button" data-star="0">Tất cả</button>
                    <button class="filter-button" data-star="5">5 sao</button>
                    <button class="filter-button" data-star="4">4 sao</button>
                    <button class="filter-button" data-star="3">3 sao</button>
                    <button class="filter-button" data-star="2">2 sao</button>
                    <button class="filter-button" data-star="1">1 sao</button> -->
                </div>
            </div>
            
            <div class="reviews">
            <?php
                // Lấy đánh giá sản phẩm theo sanpham_id
                $select_reviews = $conn->prepare("SELECT * FROM danhgia WHERE sanpham_id = ? ORDER BY ngaygui  DESC");
                $select_reviews->execute([$get_id]);

                $reviews = $select_reviews->fetchAll(PDO::FETCH_ASSOC);
                if (count($reviews) > 0) {
                    foreach ($reviews as $fetch_review) {
                        $rating = htmlspecialchars($fetch_review['sao']);
                        $user_id = htmlspecialchars($fetch_review['user_id']);
                        $hiddenUserId = substr($user_id, 0, 1) . '*****' . substr($user_id, -1);

                        // Lấy tất cả kích thước cho hoadon_id và sanpham_id từ bảng hoadon
                        $select_sizes = $conn->prepare("SELECT size FROM hoadon WHERE hoadon_id = ? AND sanpham_id = ?");
                        $select_sizes->execute([$fetch_review['hoadon_id'], $fetch_review['sanpham_id']]);
                        $sizes = $select_sizes->fetchAll(PDO::FETCH_COLUMN);

                        // Hiển thị tất cả kích thước
                        $sizeDisplay = !empty($sizes) ? implode(', ', $sizes) : 'Không có kích thước';
                        ?>
                        <div class="review" data-rating="<?= $rating; ?>">
                            <div class="user-id">
                                <p class="user-icon"><i class="bi bi-person-circle"></i></p>
                                <p class="id"><?= $hiddenUserId; ?></p>
                            </div>
                            <div class="stars">
                                <p class="star-rating">
                                    <?php
                                    for ($i = 0; $i < 5; $i++) {
                                        if ($i < $rating) {
                                            echo '<i class="bi bi-star-fill" style="font-size: .9rem; color: gold;"></i>';
                                        } else {
                                            echo '<i class="bi bi-star" style="font-size: .9rem; color: gold;"></i>';
                                        }
                                    }
                                    ?>
                                </p>
                                <p class="phanloai"><?= date('d/m/Y', strtotime($fetch_review['ngaygui'])); ?> | Phân loại hàng: <?= $sizeDisplay; ?></p>
                            </div>
                            <div class="noidung">
                                <p><?= htmlspecialchars($fetch_review['noidung']); ?></p>
                                <?php 
                                    if ($fetch_review['phanhoi']){
                                ?>
                                <p class="reply"><strong>Phản hồi từ người bán:</strong> <?= htmlspecialchars($fetch_review['phanhoi']); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <hr style="border: 1px solid #ccc;">
                        <?php
                    }
                } else {
                    echo '<p>Chưa có đánh giá nào cho sản phẩm này.</p>';
                }
            ?>  
            </div>    
            </div>
            <?php
                } else {
                    echo "<p>Không tìm thấy sản phẩm.</p>"; // Thông báo nếu không có sản phẩm
                }
            ?>

        </div>
    </div>
    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>