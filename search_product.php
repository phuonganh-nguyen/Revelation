<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else{
        $user_id = '';
    }

    include 'component/add_wishlist.php';
    include 'component/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Search product page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?> 
    <div class="products">
        <div class="heading" style="margin-top: 2rem;">
            <h1>Kết quả tìm kiếm cho từ khoá "<?= $_POST['search_product']?>" </h1>
        </div>
        <div class="box-container">
            <?php 
                if (isset($_POST['search_product']) or isset($_POST['search_product_btn'])) {
                    $search_products = $_POST['search_product'];
                    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE (name LIKE '%{$search_products}%' 
                        or loai_sp LIKE '%{$search_products}%' or thuonghieu LIKE '%{$search_products}%') AND trangthai=?");
                    $select_products->execute(['Đang hoạt động']);

                    // echo '<p> ' . $search_products . '</p>';
                    if ($select_products->rowCount() > 0) {
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                            $product_id = $fetch_products['sanpham_id'];
            ?>
            <form action="" method="post" class="box <?php if($fetch_products['soluong'] == 0){echo "disabled";} ?>">
                <img src="uploaded_files/<?= $fetch_products['image'];?>" class="image">
                <?php if($fetch_products['soluong'] > 9) {?>
                    <span class="soluong" style="color: green;">Có sẵn</span>
                <?php } elseif($fetch_products['soluong'] == 0) {?>
                    <span class="soluong" style="color: red;">Hết hàng</span>
                <?php } else {?>
                    <span class="soluong" style="color: red;">Chỉ còn <?= $fetch_products['soluong']; ?> sản phẩm</span>
                <?php }?>
                <div class="content">
                    <!-- <img src="" alt=""> -->
                    <div class="button">
                        <div><h3 class="name"><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $fetch_products['name']?></a></h3></div>
                        <div>
                            <button type="submit" name="add_to_cart"> <i class="fa-solid fa-cart-plus"></i></button>
                            <button type="submit" name="add_to_wishlist"><i class="fa-solid fa-heart-circle-plus"></i></button>
                            <a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="fa-solid fa-eye"></a>
                        </div>
                    </div>
                    <p class="price"><?= $fetch_products['price']; ?>VNĐ</p>
                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                    <div class="flex-btn">
                        <a href="checkout.php?get_id=<?= $fetch_products['sanpham_id']?>" class="btn">Mua ngay</a>
                        <input type="number" name="qty" require min="1" value="1" max="99" maxlength="2" class="qty box">
                    </div>
                </div>
            </form>
            <?php
                        }
                    } else {
                        echo '
                            <div class="empty">
                                <p>Không tìm thấy sản phẩm</p>
                            </div>
                        ';
                    }
                } else {
                    echo '
                        <div class="empty">
                            <p>Vui lòng nhập tên sản phẩm</p>
                        </div>
                    ';
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