<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    $get_id = $_GET['post_id'];

    //delete product
    if (isset($_POST['delete'])) {
        $p_id = $_POST['product_id'];

        $delete_image = $conn->prepare("DELETE FROM `sanpham` where sanpham_id=? AND admin_id=?");
        $delete_image->execute([$p_id, $admin_id]);

        $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
        if ($fetch_delete_image[''] != ''){
            unlink('../uploaded_files/'.$fetch_delete_image['image']);
        }
        $delete_product = $conn->prepare("DELETE FROM `sanpham` WHERE sanpham_id=? AND admin_id=?");
        $delete_product->execute([$p_id, $admin_id]);
        header("location:view_product.php");
    }

    if (isset($_POST['reply'])) {
        $rep = $_POST['rep'];
        $cmt_id = $_POST['cmt_id'];
        $update_cmt = $conn->prepare("UPDATE `binhluan` SET phanhoi=? WHERE sanpham_id=? AND cmt_id=?");
        $update_cmt->execute([$rep, $get_id, $cmt_id]);
        $success_msg[] = 'Đã gửi phản hồi';
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Show products page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="read-post">
            <div class="heading">
                <h1>Chi tiết sản phẩm</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">
                <?php 
                    $select_product = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? AND admin_id=?");
                    $select_product->execute([$get_id, $user_id]);

                    if($select_product->rowCount()>0){
                        while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <form action="" method="post" class="box">
                    <input type="hidden" name="product_id" value="<?= $fetch_product['sanpham_id'];?>">
                    <div class="status" style="color: <?php if($fetch_product['trangthai'] == 'active'){
                        echo "limegreen";} else {echo "coral";} ?>"><?= $fetch_product['trangthai'];?>
                    </div>
                    <div class="slider-container">
                            <div class="slider" >
                                <div class="slideBox active">
                                    <div class="imgBox">
                                        <img src="../uploaded_files/<?= $fetch_product['image']; ?>" class="image" style="width: 18%; padding-right: 1rem;">
                                   
                                    <?php
                                        // Lấy tất cả các hình ảnh có khóa ngoại là sanpham_id của sản phẩm hiện tại
                                        $select_images = $conn->prepare("SELECT * FROM `anhsp` WHERE sanpham_id=?");
                                        $select_images->execute([$fetch_product['sanpham_id']]);
                                        while ($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            echo '<img src="../uploaded_files/' . $fetch_images['img_path'] . '" style="width: 18%; padding-right: 1rem;">';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    
                    <!-- <div class="price"><?= number_format($fetch_product['price'], 0, ',', '.') ?>VNĐ</div> -->

                    <div class="title" style="margin-top: -50%;"><?= $fetch_product['name']; ?></div>
                    <div class="content">Giá nhập vào: <?= number_format($fetch_product['old_price'], 0, ',', '.') ?>VNĐ</div>
                    <div class="content">Giá nhập vào: <?= number_format($fetch_product['price'], 0, ',', '.') ?>VNĐ</div>
                    <div class="content">Chất liệu: <?= $fetch_product['chatlieu']; ?></div>
                    <div class="content">Màu sắc: <?= $fetch_product['color']; ?></div>
                    <div class="content">Số lượng: 
                        <p>Size S: <?= $fetch_product['sizeS']; ?> sản phẩm</p>
                        <p>Size M: <?= $fetch_product['sizeM']; ?> sản phẩm</p>
                        <p>Size L: <?= $fetch_product['sizeL']; ?> sản phẩm</p>
                        <p>Size XL: <?= $fetch_product['sizeXL']; ?> sản phẩm</p>
                        <p>Free size: <?= $fetch_product['freesize']; ?> sản phẩm</p>
                    </div>
                    <div class="content">Loại sản phẩm: <?= $fetch_product['loaisp']; ?></div>
                    <div class="content">Thương hiệu: <?= $fetch_product['thuonghieu']; ?></div>
                    <div class="content">Xuất xứ: <?= $fetch_product['xuatxu']; ?></div>
                    <div class="content"><?= $fetch_product['chitiet']; ?></div>
                    <div class="flex-btn">
                        <a href="edit_product.php?id=<?= $fetch_product['sanpham_id']; ?>" class="btn">Sửa</a>
                        <!-- <button type="submit" name="delete" class="btn" onclick="return confirm('delete this product');">Xóa</button> -->
                        <a href="view_product.php?post_id=<?= $fetch_product['sanpham_id']; ?>" class="btn">Trở về</a>
                    </div>
                    <div class="content">Đánh giá về sản phẩm:</div>
                    <?php 
                        $select_cmt = $conn->prepare("SELECT * FROM `binhluan` WHERE sanpham_id = ?");
                        $select_cmt->execute([$get_id]);
                        while ($fetch_ord = $select_cmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <div class="input-field" style="">
                            <p style="margin-bottom: -1rem;"><?= $fetch_ord['name']?>: <?= $fetch_ord['noidung']?></p>
                    <?php if (empty($fetch_ord['phanhoi'])) {
                    ?>
                            <input type="hidden" name="cmt_id" value="<?= $fetch_ord['cmt_id']; ?>">
                            <textarea name="rep" id="" maxlength="200" placeholder="Thêm phản hồi" class="box" style="padding: 1rem 10rem;"></textarea>
                        </div>
                        
                        <div style="margin-left: rem;">
                            <input type="submit" name="reply" value="Gửi phản hồi" class="btn" style="padding: .5rem .5rem; font-size: 1rem; margin-top: -1rem; margin-bottom: 1rem;">
                        </div>
                    <?php } else{ ?>
                        <div style="margin-left: 2rem;">
                            <p style="font-size: 1rem; margin-top: 1rem; margin-bottom: 1rem; margin-left: -2rem;">Phản hồi: <?= $fetch_ord['phanhoi']?></p>
                        </div>
                    <?php }}?>
                </form>
                <?php
                        }
                    } else {
                        echo '
                            <div class="empty">
                                <p>Chưa có sản phẩm nào được thêm vào! <br> <a href="add_product.php"
                                    class="btn" style="margin-top: 1.5rem;line-hight:2;">add products </a> </p>
                            </div>
                        ';
                    }
                ?>
            </div>
        </section>
    
    </div>

    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>