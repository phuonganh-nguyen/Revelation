<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else{
        $user_id = '';
    }

    $pid = $_GET['pid'];
    include 'component/add_wishlist.php';
    include 'component/add_cart.php';

    // Lấy tên danh mục của sản phẩm đang xem
    $select_category = $conn->prepare("SELECT loai_sp FROM `sanpham` WHERE sanpham_id=?");
    $select_category->execute([$pid]);
    $category = $select_category->fetchColumn();

    // Lấy sản phẩm đang xem
    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
    $select_products->execute([$pid]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Product detail page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <!-- slider section star --> 
    
    <section class="view_page">
        <div class="heading">
            <h1>Chi tiết sản phẩm</h1>
        </div>
        <?php 
            if (isset($_GET['pid'])) {
                $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                $select_products->execute([$pid]);

                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    
            ?>            
            <form action="" method="post" class="box">
                <div class="img-box">
                    <img src="uploaded_files/<?= $fetch_products['image']; ?>">
                </div>
                <div class="detail">
                    <?php if($fetch_products['soluong'] > 9) {?>
                        <span class="soluong" style="color: green;">Có sẵn</span>
                    <?php } elseif($fetch_products['soluong'] == 0) {?>
                        <span class="soluong" style="color: red;">Hết hàng</span>
                    <?php } else {?>
                        <span class="soluong" style="color: red;">Chỉ còn <?= $fetch_products['soluong']; ?> sản phẩm</span>
                    <?php }?>
                    <p class="price"><?= $fetch_products['price']; ?>VNĐ</p>
                    <div class="name"><?= $fetch_products['name']; ?></div>
                    <p class="product-detail">Thương hiệu: <?= $fetch_products['thuonghieu']; ?></p>
                    <p class="product-detail">Xuất xứ: <?= $fetch_products['xuatxu']; ?></p>
                    <p class="product-detail"><?= $fetch_products['chitietsp']; ?></p>
                    <input type="hidden" name="product_id" value="<? $fetch_products['sanpham_id']; ?>">
                    <!-- <div>
                        <button type="submit" name="add_to_wishlist"><i class="fa-solid fa-heart-circle-plus"></i></button>
                        <input type="hidden" name="qty" value="1" min="0" class="quantity">
                        <button type="submit" name="add_to_cart"> <i class="fa-solid fa-cart-plus"></i></button>       
                    </div> -->
            </form>
        <?php
                    }
                }
            }
        ?>
        </div>
    </section>
    <div class="products" style="margin-top: -1rem;">
        <div class="heading">
            <h1>Có thể bạn cũng thích</h1>
        </div>
        <?php
        // if (isset($_GET['pid'])) {
        //     $select_view = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
        //     $select_view->execute([$pid]);
        //     $name_view = $select_view->fetch(PDO::FETCH_ASSOC);;
        // }
        ?>
        <?php include 'component/shop.php'; ?>
    </div>
    

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>