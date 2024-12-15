<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    if(isset($_POST['update'])) {
        $product_id = $_POST['product_id']; 
        $name = $_POST['name']; 
        $type = $_POST['type'];
        $origin = $_POST['origin'];
        $description = $_POST['description'];
        $status = $_POST['status'];
        $chatlieu_1 = isset($_POST['chatlieu1']) && $_POST['chatlieu1'] != '' ? $_POST['chatlieu1'] : 0;
        $chatlieu_2 = isset($_POST['chatlieu2']) && $_POST['chatlieu2'] != '' ? $_POST['chatlieu2'] : 0;
        $color_1 = isset($_POST['color1']) && $_POST['color1'] != '' ? $_POST['color1'] : 0;
        $color_2 = isset($_POST['color2']) && $_POST['color2'] != '' ? $_POST['color2'] : 0;
        $style_1 = isset($_POST['style1']) && $_POST['style1'] != '' ? $_POST['style1'] : 0;
        $style_2 = isset($_POST['style2']) && $_POST['style2'] != '' ? $_POST['style2'] : 0;
        $style_3 = isset($_POST['style3']) && $_POST['style3'] != '' ? $_POST['style3'] : 0;
        $dip_1 = isset($_POST['dip1']) && $_POST['dip1'] != '' ? $_POST['dip1'] : 0;
        $dip_2 = isset($_POST['dip2']) && $_POST['dip2'] != '' ? $_POST['dip2'] : 0;
        $dip_3 = isset($_POST['dip3']) && $_POST['dip3'] != '' ? $_POST['dip3'] : 0;
        $hoatiet = isset($_POST['hoatiet']) ? $_POST['hoatiet'] : 0;
        $season_1 = isset($_POST['season1']) && $_POST['season1'] != '' ? $_POST['season1'] : 0;
        $season_2 = isset($_POST['season2']) && $_POST['season2'] != '' ? $_POST['season2'] : 0;
        $season_3 = isset($_POST['season3']) && $_POST['season3'] != '' ? $_POST['season3'] : 0;
        $season_4 = isset($_POST['season4']) && $_POST['season4'] != '' ? $_POST['season4'] : 0;
        $age_from = isset($_POST['age_from']) ? $_POST['age_from'] : 0;
        $age_to = isset($_POST['age_to']) ? $_POST['age_to'] : 0;

        $update_product = $conn->prepare("UPDATE `sanpham` SET name=?, loaisp=?, xuatxu=?, trangthai=? WHERE sanpham_id=?");
        $update_product->execute([$name, $type, $origin, $status, $product_id]);

        $update_detail = $conn->prepare("UPDATE `motasanpham` SET loaisp=?, name=?, chitiet=?, chatlieu_1=?, chatlieu_2=?, color_1=?, color_2=?, 
            style_1=?, style_2=?, style_3=?, dip_1=?, dip_2=?, dip_3=?, hoatiet=?, season_1=?, season_2=?, season_3=?, season_4=?, old_from=?, 
            old_to=? WHERE sanpham_id=?");
        $update_detail->execute([$type, $name, $description, $chatlieu_1, $chatlieu_2, $color_1, $color_2, 
            $style_1, $style_2, $style_3, $dip_1, $dip_2, $dip_3, $hoatiet, $season_1, $season_2, $season_3, $season_4, $age_from, 
            $age_to, $product_id]);

        $success_msg[] = 'Sản phẩm đã được cập nhật';

        // Xử lý hình ảnh
        $old_image = $_POST['old_image'];
        $background_image = $_FILES['background']['name'];
        $background_image_size = $_FILES['background']['size'];
        $background_image_tmp_name = $_FILES['background']['tmp_name'];
        $background_image_folder = '../uploaded_files/'.$background_image;

        // Kiểm tra hình ảnh mới
        if (!empty($background_image)){
            if ($background_image_size > 2000000) {
                $warning_msg[] = 'Kích thước hình ảnh quá lớn';
            } else {
                // Di chuyển và cập nhật hình ảnh
                move_uploaded_file($background_image_tmp_name, $background_image_folder);
                $update_image = $conn->prepare("UPDATE `sanpham` SET image=? WHERE sanpham_id=?");
                $update_image->execute([$background_image, $product_id]);
                // Xóa hình ảnh cũ nếu có
                if ($old_image != $background_image AND $old_image != '') {
                    unlink('../uploaded_files/'.$old_image);
                }
                $success_msg[] = 'Ảnh tổng quan đã được cập nhật!';
            }
        }

        // Xử lý ảnh mô tả mới
        $description_images = $_FILES['images'];

        // Lặp qua từng ảnh mô tả
        foreach ($description_images['tmp_name'] as $index => $tmp_name) {
            $description_image_name = $description_images['name'][$index];
            $description_image_size = $description_images['size'][$index];
            $description_image_tmp_name = $description_images['tmp_name'][$index];
            $description_image_folder = '../uploaded_files/' . $description_image_name;

            // Kiểm tra ảnh mô tả mới
            if (!empty($description_image_name)) {
                if ($description_image_size > 2000000) {
                    $warning_msg[] = 'Kích thước hình ảnh mô tả quá lớn';
                } else {
                    // Di chuyển và lưu trữ ảnh mô tả vào thư mục
                    move_uploaded_file($description_image_tmp_name, $description_image_folder);
                    // Thêm ảnh mô tả vào bảng anhsp
                    $insert_description_image = $conn->prepare("INSERT INTO `anhsp` (sanpham_id, img_path) VALUES (?, ?)");
                    $insert_description_image->execute([$product_id, $description_image_name]);
                    $success_msg[] = 'Ảnh mô tả đã được cập nhật!';
                }
            }
        }

    }

    if (isset($_POST['delete_background'])) {
        // Xóa hình ảnh nền
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

    // Xóa ảnh
    if (isset($_POST['delete_image'])) {
        $image_id = $_POST['image_id']; // Lấy id của ảnh được chọn

        // Lấy đường dẫn ảnh từ cơ sở dữ liệu
        $stmt = $conn->prepare("SELECT img_path FROM anhsp WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Xóa tệp ảnh trong thư mục
            $file_path = "../uploaded_files/" . $image['img_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Xóa ảnh trong cơ sở dữ liệu
            $delete_stmt = $conn->prepare("DELETE FROM anhsp WHERE id = ?");
            $delete_stmt->execute([$image_id]);

            $success_msg[] = 'Ảnh đã được xóa thành công!';
        } else {
            $warning_msg[] = 'Ảnh không tồn tại!';
        }
    }

    



    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
    
        // Xóa tất cả các ảnh liên quan đến sản phẩm từ bảng `anhsp`
        $delete_description_images = $conn->prepare("DELETE FROM `anhsp` WHERE sanpham_id=?");
        $delete_description_images->execute([$product_id]);
    
        // Xóa sản phẩm từ bảng `sanpham`
        $delete_product = $conn->prepare("DELETE FROM `sanpham` WHERE sanpham_id=?");
        $delete_product->execute([$product_id]);
    
        // Xóa sản phẩm từ bảng `motasanpham`
        $delete_detail = $conn->prepare("DELETE FROM `motasanpham` WHERE sanpham_id=?");
        $delete_detail->execute([$product_id]);

        // Thêm thông báo thành công và chuyển hướng đến trang xem sản phẩm
        $success_msg[] = 'Sản phẩm đã được xóa thành công!';
        header('location:view_product.php');
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Product editing page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>   
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="post-editor">
            <div class="back">
                <a href="view_product.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
            <div class="heading">
                <h1>Chỉnh sửa sản phẩm</h1>
            </div>
            <div class="box-container">
                <?php
                    $product_id = $_GET['id'];
                    $select_product = $conn->prepare("SELECT *FROM `sanpham` WHERE sanpham_id=? AND admin_id=?");
                    $select_product->execute([$product_id, $user_id]);
                    if($select_product->rowCount()>0) {
                        while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)) {
                            $select_detail = $conn->prepare("SELECT * FROM `motasanpham` WHERE sanpham_id=?");
                            $select_detail->execute([$product_id]);
                            $fetch_detail = $select_detail->fetch(PDO::FETCH_ASSOC);

                ?>
                <div class="form-container">
                    <form action="" method="post" enctype="multipart/form-data">
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
                        
                        <?php
                            $select_categories = $conn->query("SELECT * FROM danhmuc");
                            $categories = $select_categories->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="input-field">
                            <p>Loại sản phẩm <span>*</span></p>
                            <select name="type" class="box">
                                <option value="<?= $fetch_product['loaisp']; ?>" selected>
                                    <?= $fetch_product['loaisp']; ?>
                                </option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['name']; ?>" style="text-transform: capitalize;"><?= $category['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="input-field">
                            <p>Tên sản phẩm <span>*</span></p>
                            <input type="text" name="name" value="<?= $fetch_product['name'];?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Xuất xứ <span>*</span></p>
                            <input type="text" name="origin" value="<?= $fetch_product['xuatxu'];?>" class="box">
                        </div>
                        <p>Chất liệu<span>*</span></p>
                        <div class="flex">  
                            <div class="box">
                                <div class="input-field">
                                    <p>Chất liệu 1<span>*</span></p>
                                    <input type="text" name="chatlieu1" maxlength="100" value="<?= $fetch_detail['chatlieu_1'];?>" class="box">
                                </div>    
                            </div>
                            <div class="box">
                                <div class="input-field">
                                    <p>Chất liệu 2</p>
                                    <input type="text" name="chatlieu2" maxlength="100" value="<?= $fetch_detail['chatlieu_2'];?>" class="box">                                
                                </div>
                            </div>
                        </div>
                        
                        <p>Màu sắc<span>*</span></p>
                        <div class="flex">  
                            <div class="box">
                                <div class="input-field">
                                    <p>Màu sắc 1<span>*</span></p>
                                    <input type="text" name="color1" maxlength="100" value="<?= $fetch_detail['color_1'];?>" class="box">
                                </div>    
                            </div>
                            <div class="box">
                                <div class="input-field">
                                    <p>Màu sắc 2</p>
                                    <input type="text" name="color2" maxlength="100" value="<?= $fetch_detail['color_2'];?>" class="box">                                 
                                </div>
                            </div>
                        </div>
                        <p>Phong cách<span>*</span></p>
                        <div class="flex">  
                            <div class="box">
                                <div class="input-field">
                                    <p>Phong cách 1<span>*</span></p>
                                    <input type="text" name="style1" maxlength="100" value="<?= $fetch_detail['style_1'];?>" class="box">
                                </div>  
                                <div class="input-field">
                                    <p>Phong cách 2</p>
                                    <input type="text" name="style2" maxlength="100" value="<?= $fetch_detail['style_2'];?>" class="box">
                                </div>    
                            </div>
                            <div class="box">
                                <div class="input-field">
                                    <p>Phong cách 3</p>
                                    <input type="text" name="style3" maxlength="100" value="<?= $fetch_detail['style_3'];?>" class="box">                                 
                                </div>
                            </div>
                        </div>
                        <p>Dịp sử dụng<span>*</span></p>
                        <div class="flex">  
                            <div class="box">
                                <div class="input-field">
                                    <p>Dịp 1<span>*</span></p>
                                    <input type="text" name="dip1" maxlength="100" value="<?= $fetch_detail['dip_1'];?>" class="box">
                                </div>  
                                <div class="input-field">
                                    <p>Dịp 2</p>
                                    <input type="text" name="dip2" maxlength="100" value="<?= $fetch_detail['dip_2'];?>" class="box">
                                </div>    
                            </div>
                            <div class="box">
                                <div class="input-field">
                                    <p>Dịp 3</p>
                                    <input type="text" name="dip3" maxlength="100" value="<?= $fetch_detail['dip_3'];?>" class="box">                                 
                                </div>
                            </div>
                        </div>
                        <p>Mùa thích hợp<span>*</span></p>
                        <div class="flex">  
                            <div class="box">
                                <div class="input-field">
                                    <p>Mùa thích hợp 1<span>*</span></p>
                                    <input type="text" name="season1" maxlength="100" value="<?= $fetch_detail['season_1'];?>" class="box">
                                </div>  
                                <div class="input-field">
                                    <p>Mùa thích hợp 2</p>
                                    <input type="text" name="season2" maxlength="100" value="<?= $fetch_detail['season_2'];?>" class="box">
                                </div>    
                            </div>
                            <div class="box">
                                <div class="input-field">
                                    <p>Mùa thích hợp 3</p>
                                    <input type="text" name="season3" maxlength="100" value="<?= $fetch_detail['season_3'];?>" class="box">                                 
                                </div>
                                <div class="input-field">
                                    <p>Mùa thích hợp 4</p>
                                    <input type="text" name="season4" maxlength="100" value="<?= $fetch_detail['season_4'];?>" class="box">                                 
                                </div>
                            </div>
                        </div>
                        <div class="input-field">
                            <p>Độ tuổi phù hợp<span>*</span></p>
                            <div style="display: flex; gap: 15px;">
                                <input type="number" name="age_from" value="<?= $fetch_detail['old_from'];?>" class="box" style="flex: 1;">
                                <input type="number" name="age_to" value="<?= $fetch_detail['old_to'];?>" class="box" style="flex: 1;">
                            </div>
                        </div>
                        <div class="input-field">
                            <p>Họa tiết</p>
                            <input type="text" name="hoatiet" maxlength="100" value="<?= $fetch_detail['hoatiet'];?>" class="box">                                 
                        </div>
                        
                        <div class="input-field">
                            <p>Chi tiết sản phẩm <span>*</span></p>
                            <textarea name="description" class="box">
                                <?= $fetch_detail['chitiet']; ?>
                            </textarea>
                        </div>
                        <div class="input-field">
                            <p>Ảnh tổng quan <span>*</span></p>
                            <input type="file" name="background" accept="image/*" class="box">
                            <?php if($fetch_product['image'] != ''){?>
                                <img src="../uploaded_files/<?= $fetch_product['image']; ?>" class="image">
                            <?php } ?>
                            <div class="flex-btn">
                                    <!-- <input type="submit" name="update" value="Cập nhật" class="btn"> -->
                                    <input type="submit" name="delete_background" class="btn" value="Xóa ảnh">
                                    
                                </div>
                        </div>
                        
                        <div class="input-field">
                            <p>Ảnh mô tả <span>*</span></p>
                            <input type="file" name="images[]" accept="image/*" multiple class="box">
                            
                            <?php 
                                $select_images = $conn->prepare("SELECT * FROM `anhsp` WHERE sanpham_id=?");
                                $select_images->execute([$fetch_product['sanpham_id']]);
                                while ($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<img src="../uploaded_files/' . $fetch_images['img_path'] . '" style="width: 30%;">';
                                    
                                ?>
                                
                                <div class="flex-btn">
                                    <input type="hidden" name="image_id" value="<?= $fetch_images['id']; ?>">
                                    <input type="submit" name="delete_image" class="btn" value="Xóa ảnh">   
                                </div>
                            <?php }?>
                            
                        </div>

                        
                        <div class="flex-btn">
                            <input type="submit" name="update" value="Cập nhật" class="btn">
                            <input type="submit" name="delete_product" value="Xóa sản phẩm" class="btn"onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
    
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