<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else{
        $user_id = '';
    }

    $category_id = $_GET['category_id'];
    include 'component/add_wishlist.php';
    include 'component/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Shop page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <!-- slider section star -->
    
    <div class="products" >
        <?php 
            $select_thuonghieu = $conn->prepare("SELECT ten_danhmuc FROM danhmuc WHERE danhmuc_id = ?");
            $select_thuonghieu->execute([$category_id]);
            $thuonghieu_name = $select_thuonghieu->fetch(PDO::FETCH_ASSOC)['ten_danhmuc'];
        ?>
        <div class="heading">
            <h1 style="text-transform: capitalize;"><?= $thuonghieu_name; ?></h1>
        </div>
        <div class="box-container" style="margin-top: -1rem;">
            <?php 
                if (isset($_GET['category_id'])) {
                    $select_category = $conn->prepare("SELECT ten_danhmuc FROM danhmuc WHERE danhmuc_id = ?");
                    $select_category->execute([$category_id]);
                    $category_name = $select_category->fetch(PDO::FETCH_ASSOC)['ten_danhmuc'];

                    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE BINARY loai_sp = ? AND trangthai=?");
                    $select_products->execute([$category_name, 'Đang hoạt động']);

                    if ($select_products->rowCount() > 0) {
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    
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
                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $fetch_products['name']?></a></div>
                        <div>
                            <button type="submit" name="add_to_cart"> <i class="fa-solid fa-cart-plus"></i></button>
                            <button type="submit" name="add_to_wishlist"><i class="fa-solid fa-heart-circle-plus"></i></button>
                            
                        </div>
                    </div>
                    <p class="price"><?= $fetch_products['price']; ?>VNĐ</p>
                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                    <div class="flex-btn">
                        <a href="checkout.php?get_id=<?= $fetch_products['sanpham_id']?>" class="btn" style="color: var(pi); padding-top:8px;">Mua ngay</a>
                        <input type="number" name="qty" require min="1" value="1" max="99" maxlength="2" class="qty box">
                    </div>
                </div>
            </form>
        <?php
                    }
                } 
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