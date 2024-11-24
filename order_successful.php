<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }

    // Đếm tổng số đơn hàng của người dùng
    $count_orders = $conn->prepare("SELECT COUNT(*) FROM `bill` WHERE user_id = ? AND hienthi=1");
    $count_orders->execute([$user_id]);
    $total_orders = $count_orders->fetchColumn();

    // Đếm số đơn hàng trạng thái "Chờ xác nhận"
    $count_pending = $conn->prepare("SELECT COUNT(*) FROM `bill` WHERE user_id = ? AND trangthai = 'Chờ xác nhận'");
    $count_pending->execute([$user_id]);
    $pending_orders = $count_pending->fetchColumn();

    // Đếm số đơn hàng trạng thái "Đang đóng hàng"
    $count_packing = $conn->prepare("SELECT COUNT(*) FROM `bill` WHERE user_id = ? AND trangthai = 'Đóng hàng'");
    $count_packing->execute([$user_id]);
    $packing_orders = $count_packing->fetchColumn();

    // Đếm số đơn hàng trạng thái "Chờ giao hàng"
    $count_delivery = $conn->prepare("SELECT COUNT(*) FROM `bill` WHERE user_id = ? AND trangthai = 'Đang giao hàng'");
    $count_delivery->execute([$user_id]);
    $delivery_orders = $count_delivery->fetchColumn();

    // Đếm số đơn hàng trạng thái "Hoàn thành"
    $count_completed = $conn->prepare("SELECT COUNT(*) FROM `bill` WHERE user_id = ? AND trangthai = 'Đã nhận hàng'");
    $count_completed->execute([$user_id]);
    $completed_orders = $count_completed->fetchColumn();

    // Đếm số đơn hàng trạng thái "Đã hủy"
    $count_cancelled = $conn->prepare("SELECT COUNT(*) FROM `bill` WHERE user_id = ? AND trangthai = 'Đã hủy'");
    $count_cancelled->execute([$user_id]);
    $cancelled_orders = $count_cancelled->fetchColumn();
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Order page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?= time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="storekeeper-page">
    <?php include 'component/user_header.php'; ?>
    <div class="orders">   
        <div class="heading">
            <h1>Đơn hàng của tôi</h1>
        </div>
        <div class="container">
            <div class="arrow-steps order clearfix">
                <div class="step done" id="order-link">
                    <span><a href="order.php">Tất cả đơn hàng (<?= $total_orders; ?>)</a></span>
                </div>
                <div class="step done" id="pending-link">
                    <span><a href="pending_order.php">Chờ xác nhận (<?= $pending_orders; ?>)</a></span>
                </div>
                <div class="step done" id="packing-link">
                    <span><a href="packing.php">Đang đóng hàng (<?= $packing_orders; ?>)</a></span>
                </div>
                <div class="step done" id="on-delivery-link">
                    <span><a href="on_delivery.php">Chờ giao hàng (<?= $delivery_orders; ?>)</a></span>
                </div>
                <div class="step current" id="completed-link">
                    <span><a href="order_successful.php">Hoàn thành (<?= $completed_orders; ?>)</a></span>
                </div>
                <div class="step done" id="cancelled-link">
                    <span><a href="order_cancelled.php">Đã hủy (<?= $cancelled_orders; ?>)</a></span>
                </div>
            </div>
        </div>      

        <div class="box-container">
            <?php 
            // Truy vấn lấy thông tin các đơn hàng theo user_id
            $select_bill = $conn->prepare("SELECT * FROM `bill` WHERE user_id=? AND hienthi=1 ORDER BY ngaydat DESC");
            $select_bill->execute([$user_id]);

            if ($select_bill->rowCount() > 0) { 
                $hasPendingOrders = false; // Biến kiểm tra có đơn hàng đang giao hay không
                
                while ($fetch_bill = $select_bill->fetch(PDO::FETCH_ASSOC)) {
                    $trangthai = $fetch_bill['trangthai'];
                    // Kiểm tra trạng thái "Đang giao hàng"
                    if ($trangthai == "Đã nhận hàng") {
                        $hasPendingOrders = true; // Đánh dấu là có đơn hàng đang giao
                        $bill_id = $fetch_bill['bill_id'];
                        $hoadon_id = $fetch_bill['hoadon_id'];
                        $tongthanhtoan = $fetch_bill['tongthanhtoan'];
                        $ngaydat = $fetch_bill['ngaydat'];

                        $dateTime = new DateTime($ngaydat);
                        $formattedDate = $dateTime->format('H:i d/m/Y');
                        ?>
                        <h2>Ngày đặt: <?= $formattedDate; ?></h2>
                        <div class="box" onclick="window.location.href='view_order.php?get_id=<?= $fetch_bill['bill_id']?>'">
                            <h3>Đơn hàng #<?= $bill_id; ?></h3>
                            <p class="order-status"><?= htmlspecialchars($trangthai); ?></p>
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
                                            <div class="product-info" style="">
                                                <div class="product-details" style="flex: 2;">
                                                    <p class="name"><?= htmlspecialchars($fetch_product['name']); ?></p>
                                                    <p class="" style="color: #525252;">Kích thước: <?= $fetch_order_item['size']; ?></p>
                                                    <p>x <?= $fetch_order_item['soluong']; ?></p>
                                                </div>
                                                <div class="product-price" style="flex: 1; text-align: right;">
                                                    <p><?= number_format($fetch_order_item['price'] * $fetch_order_item['soluong'], 0, ',', '.'); ?> VNĐ</p>
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
                            <div class="order-total" style="text-align: right; margin-top: 20px;">
                                <h4>Thành tiền: <?= number_format($tongthanhtoan, 0, ',', '.'); ?> VNĐ</h4>
                            </div>
                            <?php if ($fetch_bill['danhgia'] == "chua"){ ?>
                            <div class="cancel-button-container">
                                <a href="evaluate_page.php?bill_id=<?= $fetch_bill['bill_id']?>" class="btn" style="width: 7rem;">Đánh giá</a>
                            </div>
                            <?php } else {?>
                            <div class="cancel-button-container">
                                <a href="evaluate_page.php?bill_id=<?= $fetch_bill['bill_id']?>" class="btn" style="width: 10rem;">Xem đánh giá</a>
                            </div>
                            <?php }?>
                        </div>
                        <?php
                    }
                }

                // Nếu không có đơn hàng nào đang giao
                if (!$hasPendingOrders) {
                    echo '<div class="empty"><p>Bạn không có đơn hàng nào hoàn thành.</p></div>';
                }
            } else {
                echo '<div class="empty"><p>Bạn chưa có đơn hàng nào.</p></div>';
            }
            ?>
        </div>
    </div>
    <script>
        // JavaScript để chuyển hướng khi bấm vào các thẻ div
        document.getElementById('order-link').addEventListener('click', function() {
            window.location.href = document.querySelector('#order-link a').getAttribute('href');
        });

        document.getElementById('pending-link').addEventListener('click', function() {
            window.location.href = document.querySelector('#pending-link a').getAttribute('href');
        });

        document.getElementById('packing-link').addEventListener('click', function() {
            window.location.href = document.querySelector('#packing-link a').getAttribute('href');
        });

        document.getElementById('on-delivery-link').addEventListener('click', function() {
            window.location.href = document.querySelector('#on-delivery-link a').getAttribute('href');
        });

        document.getElementById('completed-link').addEventListener('click', function() {
            window.location.href = document.querySelector('#completed-link a').getAttribute('href');
        });

        document.getElementById('cancelled-link').addEventListener('click', function() {
            window.location.href = document.querySelector('#cancelled-link a').getAttribute('href');
        });
    </script>

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'; ?>
</body>
</html>