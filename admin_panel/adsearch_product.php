<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
    }

    //delete product
    if (isset($_POST['delete'])) {
        $p_id = $_POST['product_id'];

        $delete_product = $conn->prepare("DELETE FROM `sanpham` where sanpham_id=?");
        $delete_product->execute([$p_id]);

        $success_msg[] = 'Sản phẩm đã được xóa thành công!';
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Search page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
</head>
<body>
<div class="main-container">
    <?php include '../component/admin_header.php'; ?> 
    <section class="show-post">
        
            <form action="adsearch_product.php" method="post" class="search-form"> 
                    <input type="text" name="search_product" placeholder="Tìm kiếm sản phẩm" required maxlength="100">
                    <button type="submit" class="fas fa-search" id="search_product_btn"></button>
            </form>
            <div class="heading" style="margin-top: 2rem;">
                <h1>Kết quả tìm kiếm cho từ khoá "<?= $_POST['search_product']?>" </h1>
            </div>
            <div class="box-container">
                <?php 
                    if (isset($_POST['search_product']) or isset($_POST['search_product_btn'])) {
                        $search_products = $_POST['search_product'];
                        $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE (name LIKE '%{$search_products}%' 
                            or loai_sp LIKE '%{$search_products}%' or thuonghieu LIKE '%{$search_products}%')");
                        $select_products->execute();

                        // echo '<p> ' . $search_products . '</p>';
                        if ($select_products->rowCount() > 0) {
                            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                                $product_id = $fetch_products['sanpham_id'];
                ?>
                <form action="" method="post" class="box">
                        <input type="hidden" name="product_id" value="<?=$fetch_products['sanpham_id']; ?>">
                        <?php if($fetch_products['image'] != '') { ?>
                            <img src="../uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                            <?php } ?>
                            <div class="status" style="color: <?php if($fetch_products['trangthai'] == 'Đang hoạt động') {
                                echo "limegreen";}else{echo "coral";} ?>"><?= $fetch_products['trangthai'];?></div>
                            <div class="price"><?= $fetch_products['price']; ?>VNĐ</div>
                            <div class="content">
                                <!-- <img src="../images/logo.png" class="shap"> -->
                                <div class="title"><?= $fetch_products['name']; ?></div>
                                <div class="sl">Số lượng: <?= $fetch_products['soluong']; ?></div>
                                <div class="flex-btn">
                                    <a href="edit_product.php?id=<?=$fetch_products['sanpham_id']; ?>" class="btn">Sửa</a>
                                    <button type="submit" name="delete" class="btn" onclick="return confirm('delete this product');">Xóa</button>
                                    <a href="read_product.php?post_id=<?=$fetch_products['sanpham_id']; ?>" class="btn">Chi tiết</a>
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
        
    </section>
</div>

<script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>