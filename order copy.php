<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Order page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <div class="orders">
        
        <div class="heading" >
            <h1>Đơn hàng của tôi</h1>
        </div>
        <div class="container">
            <div class="arrow-steps order clearfix">
                <div class="step current"> <span><a href="order.php" >Tất cả đơn hàng</a></span> </div>
                <div class="step done"> <span><a href="" >Chờ xác nhận</a><span> </div>
                <div class="step done"> <span><a href="" >Đang đóng hàng</a></span> </div>
                <div class="step done"> <span><a href="" >Chờ giao hàng</a><span> </div>
                <div class="step done"> <span><a href="" >Hoàn thành</a></span> </div>
                <div class="step done"> <span><a href="" >Đã hủy</a></span> </div>
            </div>
        </div>
        
        <div class="box-container">
        <?php 
            $select_bill = $conn->prepare("SELECT * FROM `bill` WHERE user_id=? ORDER BY ngaydat DESC");
            $select_bill->execute([$user_id]);
            $billsById = array();

            if ($select_bill->rowCount() > 0) { 
                while ($fetch_bill = $select_bill->fetch(PDO::FETCH_ASSOC)) {
                    $billId = $fetch_bill['bill_id'];

                    if (!isset($ordersById[$orderId])) {
                        $ordersById[$orderId] = array();
                    }

                    $ordersById[$orderId][] = $fetch_order;
                }

                foreach ($ordersById as $orderId => $orders) {
                    echo '<div class="order-box">';
                    echo "<h3>Mã đơn hàng #$orderId</h3>";
                    $totalPrice = 0;
                    $quantity = 0;

                    foreach ($orders as $fetch_order) {
                        $product_id = $fetch_order['sanpham_id'];
                        
                        $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                        $select_products->execute([$product_id]);

                        if ($select_products->rowCount() > 0) {
                            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                            
                        }
                        $totalPrice += ($fetch_order['price'] * $fetch_order['qty']);
                        // Tăng số lượng sản phẩm
                    $quantity += $fetch_order['qty'];
                    }
                    
                    // Hiển thị thông tin đơn hàng
                    echo '<div class="box" ' . ($fetch_order['trangthai'] == 'Đã hủy' ? 'style="border: 2px solid red"' : '') . '>';
                    echo '<a href="view_order.php?get_id=' . $orderId . '">';
                    // Sử dụng hình ảnh của sản phẩm đầu tiên trong danh sách
                    echo '<img src="uploaded_files/' . $fetch_products['image'] . '" class="image">';
                    echo '<p class="date"><i class="fa-regular fa-calendar-days"> </i> ' . $fetch_order['ngaydat'] . '</p>';
                    echo '<div class="content">';
                    echo '<div class="row">';
                    echo '</div>';
                    echo '<div class="row" style="padding-top: 1rem;">';
                    $total = $totalPrice + $fetch_order['phivanchuyen'];
                    ?>
                    <p class="total-price" style="color: black;">Thành tiền: <?= number_format($total, 0, ',', '.') ?> VNĐ |</p>
                    <?php
                    // echo '<p class="total-price" style="color: black;">Thành tiền: ' . $totalPrice + $fetch_order['phivanchuyen'] . 'VNĐ</p>';
                    echo '<p class="quantity" style="color: black;">' . $quantity . ' sản phẩm</p>';
                    echo '<p class="status" style="color:' . 
                        (($fetch_order['trangthai'] == 'Đã xác nhận' or $fetch_order['trangthai'] == 'Đang giao hàng' or $fetch_order['trangthai'] == 'Đã nhận hàng') ? 'green' : 
                        ($fetch_order['trangthai'] == 'Đã hủy' ? 'red' : 'orange')) . '">'
                        . $fetch_order['trangthai'] . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '
                    <div class="empty">
                        <p>Bạn chưa có đơn hàng nào.</p>
                    </div>
                ';
            }
        ?>
    </div>

    </div>

    <!--liên kết đến tệp JavaScript sweetalert.min.js, được sử dụng để hiển thị các thông báo cảnh báo.-->
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <!--liên kết đến tệp JavaScript script.js, được sử dụng để chứa mã JavaScript tùy chỉnh của trang web.-->

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>