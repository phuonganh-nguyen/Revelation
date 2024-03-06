<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
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


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Beauty - Show products page</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
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
                    $select_product->execute([$get_id, $admin_id]);

                    if($select_product->rowCount()>0){
                        while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <form action="" method="post" class="box">
                    <input type="hidden" name="product_id" value="<?= $fetch_product['sanpham_id'];?>">
                    <div class="status" style="color: <?php if($fetch_product['trangthai'] == 'active'){
                        echo "limegreen";} else {echo "coral";} ?>"><?= $fetch_product['trangthai'];?>
                    </div>
                    <?php if($fetch_product['image'] != '') {?>
                        <img src="../uploaded_files/<?= $fetch_product['image'];?>" class="image">
                    <?php } ?>
                    
                    <div class="price"><?= $fetch_product['price']; ?>VNĐ</div>
                    <div class="title"><?= $fetch_product['name']; ?></div>
                    <div class="content">Số lượng: <?= $fetch_product['soluong']; ?> sản phẩm</div>
                    <div class="content">Loại sản phẩm: <?= $fetch_product['loai_sp']; ?></div>
                    <div class="content">Thương hiệu: <?= $fetch_product['thuonghieu']; ?></div>
                    <div class="content">Xuất xứ: <?= $fetch_product['xuatxu']; ?></div>
                    <div class="content"><?= $fetch_product['chitietsp']; ?></div>
                    <div class="flex-btn">
                        <a href="edit_product.php?id=<?= $fetch_product['sanpham_id']; ?>" class="btn">Sửa</a>
                        <button type="submit" name="delete" class="btn" onclick="return confirm('delete this product');">Xóa</button>
                        <a href="view_product.php?post_id=<?= $fetch_product['sanpham_id']; ?>" class="btn">Trở về</a>
                    </div>
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