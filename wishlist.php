<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }
    include 'component/add_cart.php';

    // // delete
    if(isset($_POST['delete_item'])) {
        $love_id = $_POST['love_id'];
        // $product_id = $_POST['product_id'];

        $verify_delete_item = $conn->prepare("SELECT * FROM `love` WHERE id=?");
        $verify_delete_item->execute([$love_id]);
        
        if($verify_delete_item->rowCount()>0){
            $delete_wish_id = $conn->prepare("DELETE FROM `love` WHERE id=?");
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
    <title>révélation - Wishlist page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php'; ?>
    
    <div class="products-new" style="margin-top: -1.5rem;">
        <div class="heading">
            <h1>Mục yêu thích</h1>
        </div>
        <div class="box-container search">
            <?php 
                $grand_total = 0; // Khởi tạo biến grand_total để lưu trữ tổng giá trị giỏ hàng
                $select_wish = $conn->prepare("SELECT * FROM `love` WHERE user_id=?"); // Chuẩn bị câu lệnh SELECT để lấy tất cả các sản phẩm trong giỏ hàng của người dùng hiện tại
                $select_wish->execute([$user_id]);

                if ($select_wish->rowCount() > 0) { // Kiểm tra xem có sản phẩm nào trong giỏ hàng của người dùng hiện tại hay không
                    while ($fetch_wish = $select_wish->fetch(PDO::FETCH_ASSOC)) {// Duyệt qua từng sản phẩm trong giỏ hàng
                        $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");// Chuẩn bị câu lệnh SELECT để lấy thông tin chi tiết của từng sản phẩm trong giỏ hàng
                        $select_products->execute([$fetch_wish['sanpham_id']]);
                        
                        if ($select_products->rowCount() > 0) { // Kiểm tra xem có sản phẩm nào trong danh sách sản phẩm hay không
                            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);// Lấy thông tin chi tiết của từng sản phẩm
                        
            ?>
            <form action="" method="post" class="box" onclick="window.location.href='view_page.php?pid=<?= $fetch_products['sanpham_id']?>'" <?php if($fetch_products['soluong'] == 0) {echo 'disabled';}; ?>>
                <input type="hidden" name="love_id" value="<?= $fetch_wish['id']; ?>">
                <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                <?php $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];

                    // Kiểm tra trạng thái dựa trên tổng số lượng
                    if ($total_quantity > 0 && $total_quantity <= 5) {
                        echo '<span class="soluong" style="color: red;">Chỉ còn ' . $total_quantity . ' sản phẩm</span>';
                    }?> 
                <div class="content">
                    <!-- <img src="" alt=""> -->
                    <div class="button">
                    <?php
                        $product_name = $fetch_products['name']; // Lấy tên sản phẩm từ dữ liệu

                        // Kiểm tra độ dài của tên sản phẩm
                        if (mb_strlen($product_name) > 29) {
                            // Nếu tên sản phẩm dài hơn 20 ký tự, hiển thị chỉ 20 ký tự và thêm ba dấu chấm ở cuối
                            $product_name = mb_substr($product_name, 0, 26) . '...';
                        }
                        ?>

                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?=$product_name?></a><button type="submit" name="delete_item" onclick="return confirm ('Xóa khỏi mục yêu thích?');"><i class="bi bi-trash-fill"></i></button></div>
                    </div>
                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                    <div class="flex">
                        <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.'); ?> VNĐ</p>
                    </div>
                    
                </div>       
            </form>
            
            <?php
                        $grand_total += $fetch_wish['price'];
                
                        }
                    }
                } else {
                    echo '
                        <div class="empty" style = "margin-left: 20rem; margin-bottom: 8rem; margin-top: 3rem;">
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
    <?php include 'component/footer.php';?>
</body>
</html>