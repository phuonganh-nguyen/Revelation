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
    <title>Secrect Beauty - Home page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <!-- slider section star -->
    <div class="slider-container">
        <div class="slider">
            <div class="slideBox active">
                <!-- <div class="textBox">
                    <h1>We... <br> Exceptional</h1>
                    <a href="menu.php" class="btn">Shop now</a>
                </div> -->
                <div class="imgBox">
                    <img src="images/banner1.png" alt="">
                </div>
            </div>
            <div class="slideBox">
                <!-- <div class="textBox">
                    <h1>We... <br> Exceptional</h1>
                    <a href="menu.php" class="btn">Shop now</a>
                </div> -->
                <div class="imgBox">
                    <img src="images/banner2.png" alt="">
                </div>
            </div>
            <div class="slideBox">
                <!-- <div class="textBox">
                    <h1>We... <br> Exceptional</h1>
                    <a href="menu.php" class="btn">Shop now</a>
                </div> -->
                <div class="imgBox">
                    <img src="images/banner3.png" alt="">
                </div>
            </div>
            <div class="slideBox">
                <!-- <div class="textBox">
                    <h1>We... <br> Exceptional</h1>
                    <a href="menu.php" class="btn">Shop now</a>
                </div> -->
                <div class="imgBox">
                    <img src="images/banner4.png" alt="">
                </div>
            </div>
            <div class="slideBox">
                <!-- <div class="textBox">
                    <h1>We... <br> Exceptional</h1>
                    <a href="menu.php" class="btn">Shop now</a>
                </div> -->
                <div class="imgBox">
                    <img src="images/banner5.png" alt="">
                </div>
            </div>
        </div>
        <ul class="controls">
            <li onclick="nextSlide();" class="next"> 
                <i class="fa-solid fa-chevron-right"></i>
            </li>
            <li onclick="prevSlide();" class="prev"> 
                <i class="fa-solid fa-chevron-left"></i>
            </li>
        </ul>
    </div> 
    <div class="categories">
        <div class="heading">
            <h1>Danh mục</h1>
            <img src="" alt="">
        </div>
        <div class="box-container">
            <!-- <div class="box">
                <img src="images/trangdiemmat.png">
                <a href="menu.php" class="btn">Trang điểm mặt</a>
            </div> -->
            
            <?php
            $select_categories = $conn->prepare("SELECT * FROM `danhmuc`");
            $select_categories->execute();

            // Lấy kết quả và hiển thị
            while ($category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <form action="" method="post">
                <div class="box">
                    <img src="uploaded_files/<?= $category['image']; ?>">
                    <a href="cate_product.php?category_id=<?= $category['danhmuc_id']; ?>" class="btn"><?= $category['ten_danhmuc']; ?></a>
                </div>
                </form>
            <?php
            }?>
            
        </div>
    </div>

    <div class="categories" style="margin-top: -3rem;">
        <div class="heading">
            <h1>Thương hiệu</h1>
        </div>
        <div class="box-container">
        <?php
            $select_brand = $conn->prepare("SELECT * FROM `thuonghieu`");
            $select_brand->execute();

            // Lấy kết quả và hiển thị
            while ($brand = $select_brand->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <div class="box">
                    <img src="uploaded_files/<?= $brand['image']; ?>">
                    <a href="brand_product.php?brand_id=<?= $brand['thuonghieu_id']; ?>" class="btn" style="text-transform: uppercase;"><?= $brand['ten_thuonghieu']; ?></a>
                </div>
            <?php
            }?>
        </div>
    </div>
    <div class="products" style="margin-top: -1rem;">
        <div class="heading">
            <h1>Sản phẩm mới về</h1>
        </div>
        <?php include 'new_product.php'; ?>
    </div>

    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>