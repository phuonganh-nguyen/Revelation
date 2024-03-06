<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
    }

    if(isset($_POST['update'])) {
        $product_id = $_POST['product_id'];
        $soluong = $_POST['soluong'];
        $name = $_POST['name'];
        
        $type = $_POST['type'];
        $brand = $_POST['brand'];
        $origin = $_POST['origin'];
        $price = $_POST['price'];  
        $description = $_POST['description'];
        $status = $_POST['status'];

        $update_product = $conn->prepare("UPDATE `sanpham` SET soluong=?, name=?, loai_sp=?, thuonghieu=?, xuatxu=?, price=?,
            chitietsp=?, trangthai=? WHERE sanpham_id=?");
        $update_product->execute([$soluong, $name, $type, $brand, $origin, $price, $description, $status, $product_id]);
        
        $success_msg[] = 'Sản phẩm đã được cập nhật';

        $old_image = $_POST['old_image'];
        $image = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/'.$image;

        $select_image = $conn->prepare("SELECT * FROM `sanpham` WHERE image=? AND admin_id=?");
        $select_image->execute([$image, $admin_id]);

        if (!empty($image)){
            if ($image_size > 2000000) {
                $warning_msg[] = 'Kích thước hình ảnh quá lớn';
            } elseif ($select_image->rowCount() > 0) {
                $warning_msg[] = 'Vui lòng đặt lại tên hình ảnh';
            } else {
                $update_image = $conn->prepare("UPDATE `sanpham` SET image=? AND sanpham_id=?");
                $update_image->execute([$image, $product_id]);
                move_uploaded_file($image_tmp_name, $image_folder);
                if ($old_image != $image AND $old_image != '') {
                    unlink('../uploaded_files/'.$old_image);
                }
                $success_msg[] = 'Hình ảnh đã được cập nhật!';
            }
        }
    }

    //delete image
    if (isset($_POST['delete_image'])) {
        $empty_image = '';
        $product_id = $_POST['product_id'];
        $delete_image = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
        $delete_image->execute([$product_id]);

        $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);

        if ($fetch_delete_image['image'] != ''){
            unlink('../uploaded_files/'.$fetch_delete_image['image']);
        }

        $unset_image = $conn->prepare("UPDATE `sanpham` SET image=? WHERE sanpham_id=?");
        $unset_image->execute([$empty_image, $product_id]);
        $success_msg[] = 'Ảnh đã được xóa thành công!';
    }

    //delete product
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $delete_image = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
        $delete_image->execute([$product_id]);
        $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);

        if ($fetch_delete_image['image'] != '') {
            unlink('../uploaded_files/'.$fetch_delete_image['image']);
        }
        $delete_product = $conn->prepare("DELETE FROM `sanpham` WHERE sanpham_id=?");
        $delete_product->execute([$product_id]);
        $success_msg[] = 'Sản phẩm đã được xóa thành công!';
        header('location:view_product.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Beauty - Product editing page</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <!--liên kết đến tệp CSS all.min.css của Font Awesome, được sử dụng để thêm các biểu tượng vào trang web.-->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="post-editor">
            <div class="heading">
                <h1>Chỉnh sửa sản phẩm</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="box-container">
                <?php
                    $product_id = $_GET['id'];
                    $select_product = $conn->prepare("SELECT *FROM `sanpham` WHERE sanpham_id=? AND admin_id=?");
                    $select_product->execute([$product_id, $admin_id]);
                    if($select_product->rowCount()>0) {
                        while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)) {

                ?>
                <div class="form-container">
                    <form action="" method="post" enctype="multipart/form-data">
                        <!-- trường hidden để lưu trữ dữ liệu của sản phẩm đang được chỉnh sửa -->
                        <input type="hidden" name="old_image" value="<?= $fetch_product['image']; ?>">
                        <input type="hidden" name="product_id" value="<?= $fetch_product['sanpham_id']; ?>">
                        <div class="input-field">
                            <p>Trạng thái sản phẩm <span>*</span></p>
                            <select name="status" class="box">
                                <option value="<?= $fetch_product['trangthai']; ?>" selected>
                                    <?= $fetch_product['trangthai']; ?>
                                </option>
                                <option value="Đang hoạt động">Đang hoạt động</option>
                                <option value="Ngừng hoạt động">Ngừng hoạt động</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Số lượng <span>*</span></p>
                            <input type="number" name="soluong" value="<?= $fetch_product['soluong'];?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Tên sản phẩm <span>*</span></p>
                            <input type="text" name="name" value="<?= $fetch_product['name'];?>" class="box">
                        </div>
                        
                        <?php
                            $select_categories = $conn->query("SELECT * FROM danhmuc");
                            $categories = $select_categories->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="input-field">
                            <p>Loại sản phẩm <span>*</span></p>
                            <select name="type" class="box">
                                <option value="<?= $fetch_product['loai_sp']; ?>" selected>
                                    <?= $fetch_product['loai_sp']; ?>
                                </option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['ten_danhmuc']; ?>" style="text-transform: capitalize;"><?= $category['ten_danhmuc']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php
                            $select_brands = $conn->query("SELECT * FROM thuonghieu");
                            $brands = $select_brands->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="input-field">
                            <p>Thương hiệu <span>*</span></p>
                            <select name="brand" class="box">
                                <option value="<?= $fetch_product['thuonghieu']; ?>" selected>
                                    <?= $fetch_product['thuonghieu']; ?>
                                </option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['ten_thuonghieu']; ?>" style="text-transform: capitalize;"><?= $brand['ten_thuonghieu']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Xuất xứ <span>*</span></p>
                            <input type="text" name="origin" value="<?= $fetch_product['xuatxu'];?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Giá tiền <span>*</span></p>
                            <input type="number" name="price" value="<?= $fetch_product['price'];?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Chi tiết sản phẩm <span>*</span></p>
                            <textarea name="description" class="box">
                                <?= $fetch_product['chitietsp']; ?>
                            </textarea>
                        </div>
                        <div class="input-field">
                            <p>Ảnh sản phẩm <span>*</span></p>
                            <input type="file" name="image" accept="image/*" class="box">
                            <?php if($fetch_product['image'] != ''){?>
                                <img src="../uploaded_files/<?= $fetch_product['image']; ?>" class="image">
                                <div class="flex-btn">
                                    <input type="submit" name="update" value="Cập nhật" class="btn">
                                    <input type="submit" name="delete_image" class="btn" value="Xóa ảnh">
                                    
                                </div>
                            <?php } ?>
                        </div>
                        <div class="flex-btn">
                            
                            <input type="submit" name="delete_product" value="Xóa sản phẩm" class="btn">
                            <a href="view_product.php" class="btn" style="width:49%; text-align:center; 
                                        height:3rem; margin-top:.7rem; font-size:1.2rem">Trở về</a>
                        </div>
                    </form>
                </div>
                <?php
                        }
                    }else{
                        echo '
                            <div class="empty">
                                <p>Chưa có sản phẩm nào được thêm vào!</p>
                            </div>
                        ';
                    
                ?>
                <br><br>
                <div class="flex-btn">
                    <a href="view_product.php" class="btn">Xem</a>
                    <a href="add_product.php" class="btn">Thêm sản phẩm</a>
                </div>
                <?php } ?>
            </div>

        </section>
    </div>

    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>