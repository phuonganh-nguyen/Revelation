<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else{
        $user_id = '';
        header('location:user_login.php');
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Order page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <div class="orders">
        <div class="heading" >
            <h1>Đơn hàng của tôi</h1>
        </div>
        <div class="box-container">
        <?php 
            $select_order = $conn->prepare("SELECT * FROM `hoadon` WHERE user_id=? ORDER BY ngaydat DESC");
            $select_order->execute([$user_id]);
            $ordersById = array();

            if ($select_order->rowCount() > 0) { 
                while ($fetch_order = $select_order->fetch(PDO::FETCH_ASSOC)) {
                    $orderId = $fetch_order['hoadon_id'];

                    if (!isset($ordersById[$orderId])) {
                        $ordersById[$orderId] = array();
                    }

                    $ordersById[$orderId][] = $fetch_order;
                }

                foreach ($ordersById as $orderId => $orders) {
                    echo '<div class="order-box">';
                    echo "<h3>Mã đơn hàng #$orderId</h3>";
                    // Tổng giá tiền của các đơn hàng cùng ID
                    $totalPrice = 0;
                    // Số lượng sản phẩm trong đơn hàng
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
                    echo '<p class="total-price" style="color: black;">Thành tiền: ' . $totalPrice . 'VNĐ</p>';
                    echo '<p class="quantity" style="color: black;">' . $quantity . ' sản phẩm</p>';
                    echo '<p class="status" style="color:' . 
                        (($fetch_order['trangthai'] == 'Đã xác nhận' or $fetch_order['trangthai'] == 'Đang giao hàng') ? 'green' : 
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