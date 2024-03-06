<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:user_login.php');
    }
 
    include 'component/add_cart.php';

    // // delete
    if(isset($_POST['delete_item'])) {
        $love_id = $_POST['love_id'];
        // $product_id = $_POST['product_id'];

        $verify_delete_item = $conn->prepare("SELECT * FROM `yeuthich` WHERE love_id=?");
        $verify_delete_item->execute([$love_id]);
        
        if($verify_delete_item->rowCount()>0){
            $delete_wish_id = $conn->prepare("DELETE FROM `yeuthich` WHERE love_id=?");
            $delete_wish_id->execute([$love_id]);

            $success_msg[] = 'Đã xóa thành công';
        } else{
            $warning_msg[] = 'Sản phẩm đã bị xóa khỏi mục yêu thích';
        }
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Wishlist page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    
    <div class="products">
        <div class="heading">
            <h1>Mục yêu thích</h1>
        </div>
        <div class="box-container">
            <?php 
                $grand_total = 0; // Khởi tạo biến grand_total để lưu trữ tổng giá trị giỏ hàng
                $select_wish = $conn->prepare("SELECT * FROM `yeuthich` WHERE user_id=?"); // Chuẩn bị câu lệnh SELECT để lấy tất cả các sản phẩm trong giỏ hàng của người dùng hiện tại
                $select_wish->execute([$user_id]);

                if ($select_wish->rowCount() > 0) { // Kiểm tra xem có sản phẩm nào trong giỏ hàng của người dùng hiện tại hay không
                    while ($fetch_wish = $select_wish->fetch(PDO::FETCH_ASSOC)) {// Duyệt qua từng sản phẩm trong giỏ hàng
                        $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");// Chuẩn bị câu lệnh SELECT để lấy thông tin chi tiết của từng sản phẩm trong giỏ hàng
                        $select_products->execute([$fetch_wish['sanpham_id']]);
                        
                        if ($select_products->rowCount() > 0) { // Kiểm tra xem có sản phẩm nào trong danh sách sản phẩm hay không
                            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);// Lấy thông tin chi tiết của từng sản phẩm
                        
            ?>
            <form action="" method="post" class="box" <?php if($fetch_products['soluong'] == 0) {echo 'disabled';}; ?>>
                <input type="hidden" name="love_id" value="<?= $fetch_wish['love_id']; ?>">
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
                    <div class="button">
                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $fetch_products['name']?></a></div>
                        <div>
                            <button type="submit" name="add_to_cart"><i class="fa-solid fa-cart-shopping"></i></button>
                            <button type="submit" name="delete_item" onclick="return confirm ('Xóa khỏi mục yêu thích?');"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                    <div class="flex">
                        <p class="price"><?= $fetch_products['price']; ?>VNĐ</p>
                    </div>
                    <div class="flex" style="margin-top: 1.8rem;">
                        <input type="hidden" name="qty" require min="1" value="1" max="99" maxlength="2" class="qty">
                        <a href="checkout.php?get_id=<?= $fetch_products['sanpham_id']; ?>" class="btn" style="padding: .5rem 1.8rem;">Mua ngay</a>
                    </div>
                </div>       
            </form>
            
            <?php
                        $grand_total += $fetch_wish['price'];
                
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
        <!-- <?php if($grand_total != 0) {?>
            <div class="cart-total">
                <p>Tổng thanh toán: <span><?= $grand_total; ?></span>VNĐ</p>
                <div class="button">
                    <form action="" method="post">
                        <button type="submit" name="empty_cart" class="btn" onclick="return confirm ('Bạn có chắc muốn xóa toàn bộ sản phẩm?');">Xóa tất cả</button>
                    </form>
                    <a href="checkout.php" class="btn">Tiến hành thanh toán</a>
                </div>
            </div>
        <?php } ?> -->
        
    </div>
    
    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>