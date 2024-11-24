<?php 
    include 'component/connect.php';
    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
    }

    include 'component/add_wishlist.php';
    include 'component/add_cart.php';

    if (isset($_GET['id'])) {
        $collection_id = $_GET['id'];
    
        // Lấy thông tin bộ sưu tập từ bảng anhbst dựa trên ID
        $select_bst = $conn->prepare("SELECT * FROM `anhbst` WHERE id = ?");
        $select_bst->execute([$collection_id]);
        $bst = $select_bst->fetch(PDO::FETCH_ASSOC);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Collection page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
    <?php include 'component/user_header.php'; ?>
    <div class="hero" style="margin-top: -5rem;">
        <img src="uploaded_files/<?= ($bst['image']); ?>" class="back-video" alt="">
    </div> 

    <div class="products-new" >
        <div class="heading" style="margin-top: -2.5rem; margin-bottom: 1rem;">
            <h1><?= ($bst['name']); ?></h1>
            <p><?= ($bst['mota']); ?></p>
        </div>
        
        <div class="box-container" style="margin-top: -3rem; margin-bottom: 2rem;">
            <?php 
                // Lấy sanpham_id từ bảng collection dựa trên name của bộ sưu tập
                $select_collection = $conn->prepare("SELECT sanpham_id FROM `collection` WHERE name = ?");
                $select_collection->execute([$bst['name']]); // Truy vấn theo tên bộ sưu tập
                while ($fetch_collection = $select_collection->fetch(PDO::FETCH_ASSOC)){
                
                $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id = ? AND trangthai=?");
                $select_products->execute([$fetch_collection['sanpham_id'], 'Đang hoạt động']);

                    if ($select_products->rowCount() > 0) {
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    
            ?>
            <form action="" method="post" class="box"  onclick="window.location.href='view_page.php?pid=<?= $fetch_products['sanpham_id']?>'">
                <img src="uploaded_files/<?= $fetch_products['image'];?>" class="image">
                <?php
                        // Lấy số lượng từ các cỡ sản phẩm và cộng lại với nhau
                        $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];

                        // Kiểm tra trạng thái dựa trên tổng số lượng
                        if ($total_quantity > 0 && $total_quantity <= 5){
                            echo '<span class="soluong" style="color: red;">Chỉ còn ' . $total_quantity . ' sản phẩm</span>';
                        }
                        ?>
                <div class="content">
                    <!-- <img src="" alt=""> -->
                    <div class="button">
                    <?php
                        $product_name = $fetch_products['name']; // Lấy tên sản phẩm từ dữ liệu

                        // Kiểm tra độ dài của tên sản phẩm
                        if (mb_strlen($product_name) > 30) {
                            // Nếu tên sản phẩm dài hơn 20 ký tự, hiển thị chỉ 20 ký tự và thêm ba dấu chấm ở cuối
                            $product_name = mb_substr($product_name, 0, 28) . '...';
                        }
                        ?>

                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $product_name ?></a></div>
                        
                    </div>
                    <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.') ?> VNĐ</p>
                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
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
    <script src="js/user_script.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    
    <?php include 'component/alert.php'; ?>
    <?php include './component/footer.php'?>
</body>
</html>