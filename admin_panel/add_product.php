<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
    }

    //thêm sp vào database
    if (isset($_POST['publish'])) {

        $id = sp_id();
        $quantity = $_POST['quantity'];
        $name = $_POST['name']; 
        $type = $_POST['type'];
        $brand = $_POST['brand'];
        $price = $_POST['price'];
        $origin = $_POST['origin'];
        $description = $_POST['description'];
        $status = 'Đang hoạt động';

        $image = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/'.$image;

        $select_image = $conn->prepare("SELECT*FROM `sanpham` WHERE image=? AND admin_id=?");

        $select_image->execute([$image, $admin_id]);

        if(isset($image)) {
            if ($select_image->rowCount() > 0) {
                $warning_msg[] = 'Tên bị trùng lặp';
            } elseif ($image_size > 2000000) {
                $warning_msg[] = 'Kích thước hình ảnh quá lớn';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
            }
        } else {
            $image = '';
        }

        if ($select_image->rowCount() > 0 AND $image != '') {
            $warning_msg[] = 'Vui lòng đặt lại tên hình ảnh';
        } else {
            $insert_product = $conn->prepare("INSERT INTO `sanpham` (sanpham_id, admin_id, soluong, name, loai_sp,
                thuonghieu, xuatxu, price, image, chitietsp, trangthai) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $insert_product->execute([$id, $admin_id, $quantity, $name, $type, $brand, $origin, $price, $image, $description, $status]);
            // $success_msg[] = 'Thêm sản phẩm thành công!';
            header('location:view_product.php');
        }
    }

    if (isset($_POST['draft'])) {

        $id = sp_id();
        $name = $_POST['name'];
        $type = $_POST['type'];
        $brand = $_POST['brand'];
        $price = $_POST['price'];
        $origin = $_POST['origin'];
        $description = $_POST['description'];
        $status = 'Ngừng hoạt động';

        $image = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/'.$image;

        $select_image = $conn->prepare("SELECT*FROM `sanpham` WHERE image=? AND admin_id=?");

        $select_image->execute([$image, $admin_id]);

        if(isset($image)) {
            if ($select_image->rowCount() > 0) {
                $warning_msg[] = 'Tên ảnh bị trùng lặp';
            } elseif ($image_size > 2000000) {
                $warning_msg[] = 'Kích thước hình ảnh quá lớn';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
            }
        } else {
            $image = '';
        }

        if ($select_image->rowCount() > 0 AND $image != '') {
            $warning_msg[] = 'Vui lòng đặt lại tên hình ảnh';
        } else {
            $insert_product = $conn->prepare("INSERT INTO `sanpham` (sanpham_id, admin_id, soluong, name, loai_sp,
                thuonghieu, xuatxu, price, image, chitietsp, trangthai) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $insert_product->execute([$id, $admin_id, $quantity, $name, $type, $brand, $origin, $price, $image, $description, $status]);
            $success_msg[] = 'Lưu dưới dạng bản nháp thành công!';
        }
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secret Beauty - Add product page</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="post-editor"> 
            <div class="heading">
                <h1>Thêm sản phẩm</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="register">
                    <div class="input-field">
                        <p>Số lượng<span>*</span></p>
                        <input type="number" name="quantity" maxlength="100" placeholder="Nhập số lượng" require class="box">
                    </div>
                    <div class="input-field">
                        <p>Tên sản phẩm<span>*</span></p>
                        <input type="text" name="name" maxlength="100" placeholder="Nhập tên sản phẩm" require class="box">
                    </div>
                    <?php
                        $select_categories = $conn->query("SELECT * FROM danhmuc");
                        $categories = $select_categories->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="input-field">
                        <p>Danh mục<span>*</span></p>
                        <select name="type"> 
                            <!-- Lặp qua danh sách danh mục để tạo các options -->
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['ten_danhmuc']; ?>" style="text-transform: capitalize;"><?= $category['ten_danhmuc']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- <input type="text" name="type" maxlength="100" placeholder="Add product type" require class="box"> -->
                    </div>

                    <?php
                        $select_brands = $conn->query("SELECT * FROM thuonghieu");
                        $brands = $select_brands->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="input-field">
                        <p>Thương hiệu<span>*</span></p>
                        <select name="brand">
                            <!-- Lặp qua danh sách thương hiệu để tạo các options -->
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['ten_thuonghieu']; ?>" style="text-transform: capitalize;"><?= $brand['ten_thuonghieu']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-field">
                        <p>Xuất xứ<span>*</span></p>
                        <input type="text" name="origin" maxlength="100" placeholder="Nhập nơi xuất xứ" require class="box">
                        <!-- <select name="origin"> 
                            <option value="Hàn Quốc">Hàn Quốc</option>
                            <option value="Hàn Quốc">Nhật Bản</option>
                            <option value="Hàn Quốc">Singapore</option>
                            <option value="Hàn Quốc">Trung Quốc</option>
                            <option value="Hàn Quốc">Thái Lan</option>
                            <option value="Hàn Quốc">Mỹ</option>
                            <option value="Hàn Quốc">Châu Âu</option>
                            <option>Khác</option>
                        </select> -->
                        <!-- <input type="text" name="origin"maxlength="100" placeholder="Nhập nơi xuất xứ" require class="box"> -->
                    </div>
                    <div class="input-field">
                        <p>Chi tiết sản phẩm<span>*</span></p>
                        <textarea name="description" require maxlength="1000" placeholder="Nhập chi tiết sản phẩm" require class="box"></textarea>
                    </div>
                    <div class="input-field">
                        <p>Giá tiền<span>*</span></p>
                        <input type="number" name="price" maxlength="100" placeholder="Nhập giá tiền" require class="box">
                    </div>
                    <div class="input-field">
                        <p>Ảnh sản phẩm<span>*</span></p>
                        <input type="file" name="image" accept="image/*" require class="box">
                    </div>
                    <div class="flex-btn">
                        <input type="submit" name="publish" value="Thêm sản phẩm" class="btn" onclick="return confirm ('Thêm sản phẩm thành công!');">
                        <input type="submit" name="draft" value="Lưu bản nháp" class="btn">
                    </div>
                </form>
            </div>
        </section> 
    </div>

    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>