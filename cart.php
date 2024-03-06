<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:user_login.php');
    }

    // update
    if (isset($_POST['update_cart'])) {
        $cart_id = $_POST['cart_id'];
        $qty = $_POST['qty']; //sl mới
        $product_id = $_POST['product_id'];

        $check_sp = $conn->prepare("SELECT soluong FROM `sanpham` WHERE sanpham_id=?");
        $check_sp->execute([$product_id]);
        $sl_sp = $check_sp->fetch(PDO::FETCH_ASSOC)['soluong']; //sl sp hiện tại trong kho

        

        if ($qty > $sl_sp) {
            $warning_msg[] = 'Vượt quá số lượng sản phẩm có sẵn';
        } else {
            $check_cart = $conn->prepare("SELECT qty FROM `cart` WHERE sanpham_id=?");
            $check_cart->execute([$product_id]);
            $qty_oldcart = $check_cart->fetch(PDO::FETCH_ASSOC)['qty']; //sl cũ trong giỏ hàng
            $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE cart_id=?");
            $update_qty->execute([$qty, $cart_id]);

            $update_product_quantity = $conn->prepare("UPDATE `sanpham` SET soluong = soluong - ? WHERE sanpham_id = ?");
            $update_product_quantity->execute([$qty-$qty_oldcart, $product_id]);

            $success_msg[] = 'Số lượng đã được cập nhật thành công';
        }

        
    } 

    // delete
    if(isset($_POST['delete_item'])) {
        $cart_id = $_POST['cart_id'];
        $product_id = $_POST['product_id'];

        $verify_delete_item = $conn->prepare("SELECT * FROM `cart` WHERE cart_id=?");
        $verify_delete_item->execute([$cart_id]);
        if($verify_delete_item->rowCount()>0){
            $check_cart = $conn->prepare("SELECT qty FROM `cart` WHERE sanpham_id=?");
            $check_cart->execute([$product_id]);
            $qty_oldcart = $check_cart->fetch(PDO::FETCH_ASSOC)['qty']; //sl cũ trong giỏ hàng

            $update_product_quantity = $conn->prepare("UPDATE `sanpham` SET soluong = soluong + ? WHERE sanpham_id = ?");
            $update_product_quantity->execute([$qty_oldcart, $product_id]);

            
            $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE cart_id=?");
            $delete_cart_id->execute([$cart_id]);

            $success_msg[] = 'Đã xóa thành công';
        } else{
            $warning_msg[] = 'Sản phẩm đã bị xóa khỏi giỏ hàng';
        }
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Cart page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    
    <div class="products">
        <div class="heading" style="margin-top: 2rem;">
            <h1>Giỏ hàng</h1>
        </div>
        <div class="box-container">
            <?php 
                $grand_total = 0; // Khởi tạo biến grand_total để lưu trữ tổng giá trị giỏ hàng
                $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?"); // Chuẩn bị câu lệnh SELECT để lấy tất cả các sản phẩm trong giỏ hàng của người dùng hiện tại
                $select_cart->execute([$user_id]);

                if ($select_cart->rowCount() > 0) { // Kiểm tra xem có sản phẩm nào trong giỏ hàng của người dùng hiện tại hay không
                    while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {// Duyệt qua từng sản phẩm trong giỏ hàng
                        $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");// Chuẩn bị câu lệnh SELECT để lấy thông tin chi tiết của từng sản phẩm trong giỏ hàng
                        $select_products->execute([$fetch_cart['sanpham_id']]);
                        
                        if ($select_products->rowCount() > 0) { // Kiểm tra xem có sản phẩm nào trong danh sách sản phẩm hay không
                            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);// Lấy thông tin chi tiết của từng sản phẩm
                        
            ?>
            <form action="" method="post" class="box" <?php if($fetch_products['soluong'] == 0) {echo 'disabled';}; ?>>
                <input type="hidden" name="cart_id" value="<?= $fetch_cart['cart_id']; ?>">
                <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                <?php if($fetch_products['soluong'] > 9) { ?>
                    <span class="soluong" style="color: green;">Có sẵn</span>
                <?php } elseif ($fetch_products['soluong'] == 0) {?>
                    <span class="soluong" style="color: red;">Đã hết hàng</span>
                <?php } else {?>
                    <span class="soluong" style="color: red;">Chỉ còn <?= $fetch_products['soluong']; ?> sản phẩm</span>
                <?php }?>
                <div class="content">
                    <!-- <img src="" alt=""> -->
                    <a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $fetch_products['name']?></a>
                    <div class="flex-btn">
                        <p class="price"><?= $fetch_products['price']; ?>VNĐ</p>
                        <input type="number" name="qty" require min="1" value="<?= $fetch_cart['qty']?>" max="99" maxlength="2" class="box qty">
                        <button type="submit" name="update_cart" class="fa-solid fa-pen-to-square fa-edit box"></button> 
                    </div>
                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                    <div class="flex-btn">
                        <p class="sub_total">Tổng tiền: <span><?= $sub_total = ($fetch_cart['qty']*$fetch_cart['price']); ?>VNĐ</span></p>
                        <button type="submit" name="delete_item" class="btn" onclick="return confirm ('Xóa khỏi giỏ hàng?');">Xóa</button>
                    </div>
                </div>       
            </form>
            
            <?php
                        $grand_total += $sub_total;
                        } else{
                            echo '
                                <div class="empty">
                                    <p>Không có sản phẩm nào được tìm thấy.</p>
                                </div>
                            ';
                        }
                    }
                } else {
                    echo '
                        <div class="empty">
                            <p>Chưa có sản phẩm nào được thêm vào.</p>
                        </div>
                    ';
                }
            ?>
        </div>
        <?php if($grand_total != 0) {?>
            <div class="cart-total">
                <p>Tổng thanh toán: <span><?= $grand_total; ?></span>VNĐ</p>
                <div class="button">
                    <form action="" method="post">
                        <button type="submit" name="empty_cart" class="btn" onclick="return confirm ('Bạn có chắc muốn xóa toàn bộ sản phẩm?');">Xóa tất cả</button>
                    </form>
                    <a href="checkout.php" class="btn" style="font-size: 1.3rem;">Tiến hành thanh toán</a>
                </div>
            </div>
        <?php } ?>
        
    </div>
    
    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>