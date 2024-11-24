<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }

    // Xử lý khi nhấn nút "+" hoặc "-"
if (isset($_POST['qty_update'])) {
    $cart_id = $_POST['cart_id'];
    $product_id = $_POST['product_id'];
    $size = $_POST['size'];

    // Lấy số lượng hiện tại trong giỏ hàng
    $check_cart = $conn->prepare("SELECT qty FROM `cart` WHERE cart_id=?");
    $check_cart->execute([$cart_id]);
    $current_qty = $check_cart->fetch(PDO::FETCH_ASSOC)['qty'];

    // Lấy số lượng hiện có trong kho theo size
    $check_sp = $conn->prepare("SELECT $size FROM `sanpham` WHERE sanpham_id=?");
    $check_sp->execute([$product_id]);
    $available_qty = $check_sp->fetch(PDO::FETCH_ASSOC)[$size]; // Số lượng sản phẩm hiện có

    // Kiểm tra điều kiện tăng số lượng
    if ($_POST['qty_update'] === 'increase') {
        if ($current_qty < $available_qty) {
            $new_qty = $current_qty + 1;
            $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE cart_id=?");
            $update_qty->execute([$new_qty, $cart_id]);
            $success_msg[] = 'Số lượng đã được cập nhật thành công';
        } else {
            $warning_msg[] = 'Vượt quá số lượng sản phẩm có sẵn';
        }
    } elseif ($_POST['qty_update'] === 'decrease') {
        if ($current_qty > 1) { // Đảm bảo số lượng không giảm xuống dưới 1
            $new_qty = $current_qty - 1;
            $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE cart_id=?");
            $update_qty->execute([$new_qty, $cart_id]);
            $success_msg[] = 'Số lượng đã được cập nhật thành công';
        }
    }
}


    // delete
    if(isset($_POST['delete_item'])) {
        $cart_id = $_POST['cart_id'];
        $product_id = $_POST['product_id'];

        $verify_delete_item = $conn->prepare("SELECT * FROM `cart` WHERE cart_id=?");
        $verify_delete_item->execute([$cart_id]);
        if($verify_delete_item->rowCount()>0){
            // $check_cart = $conn->prepare("SELECT qty FROM `cart` WHERE sanpham_id=?");
            // $check_cart->execute([$product_id]);
            // $qty_oldcart = $check_cart->fetch(PDO::FETCH_ASSOC)['qty']; //sl cũ trong giỏ hàng

            // $update_product_quantity = $conn->prepare("UPDATE `sanpham` SET soluong = soluong + ? WHERE sanpham_id = ?");
            // $update_product_quantity->execute([$qty_oldcart, $product_id]);            
            $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE cart_id=?");
            $delete_cart_id->execute([$cart_id]);

            $success_msg[] = 'Đã xóa thành công';
        } else{
            $warning_msg[] = 'Sản phẩm đã bị xóa khỏi giỏ hàng';
        }
    }

    // Xóa toàn bộ giỏ hàng
    if (isset($_POST['empty_cart'])) {
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart->execute([$user_id]);

        if ($delete_cart->rowCount() > 0) {
            $success_msg[] = 'Giỏ hàng đã được xóa thành công';
        } else {
            $warning_msg[] = 'Bạn chưa thêm sản phẩm vào giỏ hàng';
        }
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Cart page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>

    <div class="cart-page">
        
        <?php
            $grand_total = 0;
            $index = 1; // Đếm số thứ tự
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=? ORDER BY date DESC");
            $select_cart->execute([$user_id]);

            if ($select_cart->rowCount() > 0) {
        ?>
        <div class="heading">
            <h1>Giỏ hàng</h1>
        </div>
        <div class="box-container">
            <div class="box">
                    <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Số tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                                    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                                    $select_products->execute([$fetch_cart['sanpham_id']]);

                                    if ($select_products->rowCount() > 0) {
                                        $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                                        
                                        // Kiểm tra số lượng size hiện có
                                        $size = $fetch_cart['size'];
                                        $available_size_qty = $fetch_products[$size];

                                        // Nếu số lượng size > 0 thì mới hiển thị
                                        if ($available_size_qty > 0) {
                                            $sub_total = $fetch_cart['qty'] * $fetch_products['price'];
                                            $grand_total += $sub_total;
                        ?>
                        <tr>
                            <td><?= $index++; ?></td>
                            <td>
                                <div class="item-details">
                                    <img src="uploaded_files/<?= $fetch_products['image']; ?>" alt="<?= $fetch_products['name']; ?>" class="small-img" style="width: 50px; height: 50px; margin-right: 10px;">
                                    <div>
                                        <p><?= $fetch_products['name']; ?> (<?= $fetch_cart['size']; ?>)</p>
                                    </div>
                                </div>
                            </td>
                            <td><?= number_format($fetch_products['price'], 0, ',', '.') ?> VNĐ</td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="cart_id" value="<?= $fetch_cart['cart_id']; ?>">
                                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']; ?>">
                                    <input type="hidden" name="size" value="<?= $fetch_cart['size']; ?>">
                                    <div class="quantity-controls">
                                        <button type="submit" name="qty_update" value="decrease"><strong>-</strong></button>
                                        <input type="number" name="qty" value="<?= $fetch_cart['qty']; ?>" min="1" max="99" readonly>
                                        <button type="submit" name="qty_update" value="increase"><strong>+</strong></button>
                                    </div>
                                </form>
                            </td>
                            <td><?= number_format($sub_total, 0, ',', '.') ?> VNĐ</td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="cart_id" value="<?= $fetch_cart['cart_id']; ?>">
                                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']; ?>">
                                    <button type="submit" name="delete_item" onclick="return confirm('Xóa sản phẩm này?');">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                                        } 
                                    } 
                                }
                            
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        } else { ?>
            <div class="heading" style="text-align: center;">
                <h1>Giỏ hàng trống</h1>
                <div class="empty-cart-message">
                    <p><i class="bi bi-emoji-laughing-fill"></i> Bạn chưa thêm sản phẩm vào giỏ hàng</p>
                    <a href="menu.php" class="btn">Tiếp tục mua sắm</a>
                </div>
                
            </div>
            
        <?php }
        ?>

        <!-- Hiển thị tổng tiền và nút thanh toán -->
        <?php if($grand_total != 0) { ?>
            <div class="cart-total">
                <p>Tạm tính: <span><?= number_format($grand_total, 0, ',', '.'); ?> VNĐ</span></p>
                <div class="button">
                    <form action="" method="post">
                        <button type="submit" name="empty_cart" class="btn" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ sản phẩm?');">Xóa tất cả</button>
                    </form>
                    <?php
                        $check_shipping = $conn->prepare("SELECT * FROM `vanchuyen` WHERE user_id=?");
                        $check_shipping->execute([$user_id]);

                        if ($check_shipping->rowCount() > 0) { 
                    ?>
                        <a href="thongtinthanhtoan.php" class="btn" style="font-size: 1.3rem;">Tiến hành thanh toán</a>
                    <?php } else { ?>
                        <a href="vanchuyen.php" class="btn" style="font-size: 1.3rem;">Tiến hành thanh toán</a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

    </div>

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php' ?>
</body>
</html>
