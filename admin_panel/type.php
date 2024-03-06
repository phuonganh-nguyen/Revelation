<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
    }

    if (isset($_POST['submit'])) {
        $danhmuc_id = sp_id();
        $ten_danhmuc = $_POST['ten_danhmuc'];
        $image_dir = "../uploaded_files/"; // Đường dẫn đến thư mục ảnh
        $image_name = $_FILES['image']['name']; // Tên tệp ảnh
        $image_path = $image_dir . $image_name; // Đường dẫn đầy đủ đến tệp ảnh

        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        
        // hàm binary phân biệt dấu sắc hỏi ngã nặng
        $select_type = $conn->prepare("SELECT * FROM `danhmuc` WHERE BINARY ten_danhmuc = ?");
        $select_type->execute([$ten_danhmuc]);

        if ($select_type->rowCount() > 0) {
            // Danh mục đã tồn tại, hiển thị thông báo
            $warning_msg[] = 'Danh mục đã tồn tại';
        } else {
            // Thêm danh mục mới vào cơ sở dữ liệu
            $insert_type = $conn->prepare("INSERT INTO `danhmuc`(danhmuc_id, ten_danhmuc, image) VALUES (?, ?, ?)");
            $insert_type->execute([$danhmuc_id, $ten_danhmuc, $image_name]);

            $success_msg[] = 'Thêm danh mục thành công!';
        }
    }

    if (isset($_POST['edit'])) {
        $danhmuc_id = $_POST['danhmuc_id']; // Lấy danh mục ID từ form
        $ten_danhmuc = $_POST['ten_danhmuc']; // Lấy tên danh mục từ form

        // Kiểm tra xem tên danh mục đã tồn tại hay chưa
        $select_type = $conn->prepare("SELECT * FROM `danhmuc` WHERE BINARY ten_danhmuc=? AND danhmuc_id<>?");
        $select_type->execute([$ten_danhmuc, $danhmuc_id]);

        if ($select_type->rowCount() > 0) {
            // Tên danh mục đã tồn tại, hiển thị thông báo
            $warning_msg[] = 'Danh mục đã tồn tại';
        } else {
            // Cập nhật tên danh mục vào cơ sở dữ liệu
            $update_type = $conn->prepare("UPDATE `danhmuc` SET ten_danhmuc=? WHERE danhmuc_id=?");
            $update_type->execute([$ten_danhmuc, $danhmuc_id]);

            $success_msg[] = 'Cập nhật danh mục thành công!';
        }
    }

    if (isset($_POST['delete'])) {
        $danhmuc_id = $_POST['danhmuc_id']; // Lấy danh mục ID từ form
    
        // Xóa danh mục từ cơ sở dữ liệu
        $delete_type = $conn->prepare("DELETE FROM `danhmuc` WHERE danhmuc_id=?");
        $delete_type->execute([$danhmuc_id]);
    
        $success_msg[] = 'Xóa danh mục thành công!';
    }

    $admin_type = $conn->prepare("SELECT * FROM `danhmuc` WHERE them=?");
    $admin_type->execute(['dathem']);
    $total_type = $admin_type->rowCount();


    // -----


    if (isset($_POST['submit-b'])) {
        $brand_id = sp_id();
        $ten_thuonghieu = $_POST['ten_thuonghieu'];
        $image_dir = "../uploaded_files/"; // Đường dẫn đến thư mục ảnh
        $image_name = $_FILES['image']['name']; // Tên tệp ảnh
        $image_path = $image_dir . $image_name; // Đường dẫn đầy đủ đến tệp ảnh

        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        $select_brand = $conn->prepare("SELECT * FROM `thuonghieu` WHERE BINARY ten_thuonghieu = ?");
        $select_brand->execute([$ten_thuonghieu]);

        if ($select_brand->rowCount() > 0) {
            $warning_msg[] = 'Thương hiệu đã tồn tại';
        } else {
            $insert_brand = $conn->prepare("INSERT INTO `thuonghieu` (thuonghieu_id, ten_thuonghieu, image) VALUES (?, ?, ?)");
            $insert_brand->execute([$brand_id, $ten_thuonghieu, $image_name]);

            $success_msg[] = 'Thêm thương hiệu thành công!';
        }
    }

    if (isset($_POST['edit-b'])) {
        $brand_id = $_POST['thuonghieu_id'];
        $ten_thuonghieu = $_POST['ten_thuonghieu'];

        $select_brand = $conn->prepare("SELECT * FROM `thuonghieu` WHERE BINARY ten_thuonghieu=? AND thuonghieu_id<>?");
        $select_brand->execute([$ten_thuonghieu, $brand_id]);

        if ($select_brand->rowCount() > 0) {
            $warning_msg[] = 'Thương hiệu đã tồn tại';
        } else {
            $update_brand = $conn->prepare("UPDATE `thuonghieu` SET ten_thuonghieu=? WHERE thuonghieu_id=?");
            $update_brand->execute([$ten_thuonghieu, $brand_id]);

            $success_msg[] = 'Cập nhật thương hiệu thành công!';
        }
    }

    if (isset($_POST['delete-b'])) {
        $brand_id = $_POST['thuonghieu_id'];

        $delete_brand = $conn->prepare("DELETE FROM `thuonghieu` WHERE thuonghieu_id=?");
        $delete_brand->execute([$brand_id]);

        $success_msg[] = 'Xóa thương hiệu thành công!';
    }

    $admin_brand = $conn->prepare("SELECT * FROM `thuonghieu` WHERE them=?");
    $admin_brand->execute(['dathem']);
    $total_brand = $admin_brand->rowCount();
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Beauty - Admin category and brand pages</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <!--liên kết đến tệp CSS all.min.css của Font Awesome, được sử dụng để thêm các biểu tượng vào trang web.-->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="admin-type">
            <div class="heading">
                <h1>Quản lý danh mục</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            
                
                <div class="details">
                    <div class="flex">
                        <div class="box">
                            <h3>Tổng số <?= $total_type; ?> danh mục đã thêm: </h3>
                            <?php
                                $select_type = $conn->prepare("SELECT *FROM `danhmuc` WHERE them=?");
                                $select_type->execute(['dathem']);
                                if($select_type->rowCount()>0) {
                                    while ($fetch_type = $select_type->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <div class="edit_type">
                                    <form method="post" action="">
                                        <div>
                                            <img src="../uploaded_files/<?= $fetch_type['image']; ?>" alt="">
                                            <input type="hidden" name="danhmuc_id" value="<?= $fetch_type['danhmuc_id']; ?>">
                                            <input type="text" name="ten_danhmuc" value="<?= $fetch_type['ten_danhmuc'];?>" class="small-box">
                                            <input type="submit" name="edit" value="Cập nhật" class="btn" style="padding: .5rem 2rem; margin-top: 1rem;">
                                            <input type="submit" name="delete" value="Xóa" class="btn" style="padding: .5rem 2rem; margin-top: 1rem;">
                                        </div>
                                    </form>
                            </div>  
                            
                            <?php 
                                }
                                } else{
                                echo '
                                    <div class="empty">
                                        <p>Chưa có danh mục nào được thêm vào!</p>
                                    </div>
                                ';
                            }
                            ?>
                        </div>
                        <div class="box">
                            <form method="post" enctype="multipart/form-data">
                                <h3>Thêm danh mục mới</h3>
                                <div class="input-field">
                                    <input type="text" name="ten_danhmuc" maxlength="100" placeholder="Nhập tên danh mục" require class="small-box">
                                </div>
                                <div class="input-field">
                                    <p style="font-size: 1.2rem; margin-top: 2rem;">Ảnh minh họa<span>*</span></p>
                                    <input type="file" name="image" accept="image/*" require class="box">
                                </div>
                                <input type="submit" name="submit" value="Thêm" class="btn" style="padding: .5rem 2rem; margin-top: 1rem;">
                            </form>
                        </div>
                    </div>  
                </div>


                <div class="heading" style="margin-top: 2rem;">
                <h1>Quản lý thương hiệu</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
                <div class="details">
                    <div class="flex">
                        <div class="box">
                            <h3>Tổng số <?= $total_brand; ?> thương hiệu đã thêm: </h3>
                            <?php
                                $select_brand = $conn->prepare("SELECT *FROM `thuonghieu` WHERE them=?");
                                $select_brand->execute(['dathem']);
                                if($select_brand->rowCount()>0) {
                                    while ($fetch_brand = $select_brand->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <div class="edit_type">
                                    <form method="post" action="">
                                        <div>
                                            <img src="../uploaded_files/<?= $fetch_brand['image']; ?>" alt="">
                                            <input type="hidden" name="thuonghieu_id" value="<?= $fetch_brand['thuonghieu_id']; ?>">
                                            <input type="text" name="ten_thuonghieu" value="<?= $fetch_brand['ten_thuonghieu'];?>" class="small-box">
                                            <input type="submit" name="edit-b" value="Cập nhật" class="btn" style="padding: .5rem 2rem; margin-top: 1rem;">
                                            <input type="submit" name="delete-b" value="Xóa" class="btn" style="padding: .5rem 2rem; margin-top: 1rem;">
                                        </div>
                                    </form>
                            </div>  
                            
                            <?php 
                                }
                                } else{
                                echo '
                                    <div class="empty">
                                        <p>Chưa có thương hiệu nào được thêm vào!</p>
                                    </div>
                                ';
                            }
                            ?>
                        </div>
                        <div class="box">
                            <form method="post" enctype="multipart/form-data">
                                <h3>Thêm thương hiệu mới</h3>
                                <div class="input-field">
                                    <input type="text" name="ten_thuonghieu" maxlength="100" placeholder="Nhập tên thương hiệu" require class="small-box">
                                </div>
                                <div class="input-field">
                                    <p style="font-size: 1.2rem; margin-top: 2rem;">Ảnh minh họa<span>*</span></p>
                                    <input type="file" name="image" accept="image/*" require class="box">
                                </div>
                                <input type="submit" name="submit-b" value="Thêm" class="btn" style="padding: .5rem 2rem; margin-top: 1rem;">
                            </form>
                        </div>
                    </div>  
                </div>
        </section>
    </div>

    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>