<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    //thêm sp vào database
    if (isset($_POST['publish'])) {

        $id = sp_id();
        // $quantityS = $_POST['quantityS'];
        // $quantityM = $_POST['quantityM'];
        // $quantityL = $_POST['quantityL'];
        // $quantityXL = $_POST['quantityXL'];
        // $freesize = $_POST['freesize'];
        $name = $_POST['name']; 
        $type = $_POST['type'];
        
        $origin = $_POST['origin'];
        $description = $_POST['description'];
        $status = 'Đang hoạt động';
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


        // Lấy thông tin về ảnh tổng quan
        $image_tmp_name = $_FILES['background']['tmp_name'];
        $image_name = $_FILES['background']['name'];
        
        // Tạo tên mới cho ảnh
        $image_new_name = uniqid('image_') . '_' . $image_name;
    
        // Di chuyển ảnh vào thư mục lưu trữ
        $image_path = '../uploaded_files/' . $image_new_name;
        move_uploaded_file($image_tmp_name, $image_path);

        // thêm vào bảng motasanpham
        $insert_detail = $conn->prepare("INSERT INTO `motasanpham` (sanpham_id, loaisp, name, chitiet, chatlieu_1, chatlieu_2, color_1, color_2, 
            style_1, style_2, style_3, dip_1, dip_2, dip_3, hoatiet, season_1, season_2, season_3, season_4, old_from, old_to) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $insert_detail->execute([$id, $type, $name, $description, $chatlieu_1, $chatlieu_2, $color_1, $color_2, 
            $style_1, $style_2, $style_3, $dip_1, $dip_2, $dip_3, $hoatiet, $season_1, $season_2, $season_3, $season_4, $age_from, $age_to]);
        // Lưu đường dẫn của ảnh vào cột image của bảng sanpham
        $insert_product = $conn->prepare("INSERT INTO `sanpham` (sanpham_id, admin_id, name, loaisp, xuatxu, trangthai, image) VALUES (?,?,?,?,?,?,?)");
        $insert_product->execute([$id, $user_id, $name, $type, $origin, $status, $image_path]);
    
        // Lặp qua mỗi ảnh mô tả được tải lên và lưu chúng vào bảng anhsp
        foreach ($_FILES['images']['name'] as $key => $image_name) {
            // Lấy thông tin cụ thể về ảnh
            $image_size = $_FILES['images']['size'][$key];
            $image_tmp_name = $_FILES['images']['tmp_name'][$key];
            
            // Tạo tên duy nhất cho ảnh
            $image_new_name = uniqid('image_') . '_' . $image_name;
    
            // Di chuyển ảnh vào thư mục lưu trữ
            $image_path = '../uploaded_files/' . $image_new_name;
            move_uploaded_file($image_tmp_name, $image_path);
    
            // Lưu thông tin của ảnh vào cơ sở dữ liệu
            $insert_image = $conn->prepare("INSERT INTO anhsp (sanpham_id, img_path) VALUES (?, ?)");
            $insert_image->execute([$id, $image_path]);
        }
    
        header('location:view_product.php');  
    }
    
    if (isset($_POST['draft'])) {
    
        $id = sp_id();
        $quantityS = $_POST['quantityS'];
        $quantityM = $_POST['quantityM'];
        $quantityL = $_POST['quantityL'];
        $quantityXL = $_POST['quantityXL'];
        $freesize = $_POST['freesize'];
        $name = $_POST['name']; 
        $type = $_POST['type'];
        // $brand = $_POST['brand'];
        // $old_price = $_POST['price'];
        // $price = $old_price + ($old_price * 0.1);
        // $price = $_POST['price'];
        $origin = $_POST['origin'];
        $description = $_POST['description'];
        $status = 'Ngừng hoạt động';
        $chatlieu = $_POST['chatlieu'];
        $color = $_POST['color'];
        // $kho = $_POST['stock'];
    
        // Lặp qua mỗi ảnh mô tả được tải lên và lưu chúng vào bảng anhsp
        foreach ($_FILES['images']['name'] as $key => $image_name) {
            // Lấy thông tin cụ thể về ảnh
            $image_size = $_FILES['images']['size'][$key];
            $image_tmp_name = $_FILES['images']['tmp_name'][$key];
            
            // Tạo tên duy nhất cho ảnh
            $image_new_name = uniqid('image_') . '_' . $image_name;
    
            // Di chuyển ảnh vào thư mục lưu trữ
            $image_path = '../uploaded_files/' . $image_new_name;
            move_uploaded_file($image_tmp_name, $image_path);
    
            // Lưu thông tin của ảnh vào cơ sở dữ liệu
            $insert_image = $conn->prepare("INSERT INTO anhsp (sanpham_id, img_path) VALUES (?, ?)");
            $insert_image->execute([$id, $image_path]);
        }
    
        // Lưu background vào cột image của bảng sanpham
        $insert_product = $conn->prepare("INSERT INTO `sanpham` (sanpham_id, admin_id, name, chatlieu, color, loaisp, xuatxu, chitiet, trangthai, sizeS, sizeM, sizeL, sizeXL, freesize, image) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $insert_product->execute([$id, $user_id, $name, $chatlieu, $color, $type, $origin, $description, $status, $quantityS, $quantityM, $quantityL, $quantityXL, $freesize, $image_path]);
    
        header('location:view_product.php');  
        
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Add product page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
                    <?php
                        $select_categories = $conn->query("SELECT * FROM danhmuc");
                        $categories = $select_categories->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="input-field">
                        <p>Danh mục<span>*</span></p>
                        <select name="type" style="text-transform: capitalize;"> 
                            <!-- Lặp qua danh sách danh mục để tạo các options -->
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['name']; ?>" style="text-transform: capitalize;"><?= $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- <input type="text" name="type" maxlength="100" placeholder="Add product type" require class="box"> -->
                    </div>
                    <div class="input-field">
                        <p>Tên sản phẩm<span>*</span></p>
                        <input type="text" name="name" maxlength="100" placeholder="Nhập tên sản phẩm" required class="box">
                    </div>
                    
                    <div class="input-field">
                        <p>Xuất xứ<span>*</span></p>
                        <input type="text" name="origin" maxlength="100" placeholder="Nhập nơi xuất xứ" required class="box">
                    </div>

                    <p>Chất liệu<span>*</span></p>
                    <div class="flex">  
                        <div class="box">
                            <div class="input-field">
                                <p>Chất liệu 1<span>*</span></p>
                                <input type="text" name="chatlieu1" maxlength="100" placeholder="Nhập chất liệu" required class="box">
                            </div>    
                        </div>
                        <div class="box">
                            <div class="input-field">
                                <p>Chất liệu 2</p>
                                <input type="text" name="chatlieu2" maxlength="100" placeholder="Nhập chất liệu" class="box">                                 
                            </div>
                        </div>
                    </div>
                    <p>Màu sắc<span>*</span></p>
                    <div class="flex">  
                        <div class="box">
                            <div class="input-field">
                                <p>Màu sắc 1<span>*</span></p>
                                <input type="text" name="color1" maxlength="100" placeholder="Nhập màu sắc" required class="box">
                            </div>    
                        </div>
                        <div class="box">
                            <div class="input-field">
                                <p>Màu sắc 2</p>
                                <input type="text" name="color2" maxlength="100" placeholder="Nhập màu sắc" class="box">                                 
                            </div>
                        </div>
                    </div>
                    <div class="input-field">
                        <p>Họa tiết</p>
                        <input type="text" name="hoatiet" maxlength="100" placeholder="Nhập họa tiết" required class="box">                                 
                    </div>
                    <p>Phong cách<span>*</span></p>
                    <div class="flex">  
                        <div class="box">
                            <div class="input-field">
                                <p>Phong cách 1<span>*</span></p>
                                <input type="text" name="style1" maxlength="100" placeholder="Nhập phong cách" required class="box">
                            </div>  
                            <div class="input-field">
                                <p>Phong cách 2</p>
                                <input type="text" name="style2" maxlength="100" placeholder="Nhập phong cách" class="box">
                            </div>    
                        </div>
                        <div class="box">
                            <div class="input-field">
                                <p>Phong cách 3</p>
                                <input type="text" name="style3" maxlength="100" placeholder="Nhập phong cách" class="box">                                 
                            </div>
                        </div>
                    </div>
                    <p>Dịp sử dụng<span>*</span></p>
                    <div class="flex">  
                        <div class="box">
                            <div class="input-field">
                                <p>Dịp 1<span>*</span></p>
                                <input type="text" name="dip1" maxlength="100" placeholder="Nhập dịp sử dụng" required class="box">
                            </div>  
                            <div class="input-field">
                                <p>Dịp 2</p>
                                <input type="text" name="dip2" maxlength="100" placeholder="Nhập dịp sử dụng" class="box">
                            </div>    
                        </div>
                        <div class="box">
                            <div class="input-field">
                                <p>Dịp 3</p>
                                <input type="text" name="dip3" maxlength="100" placeholder="Nhập dịp sử dụng" class="box">                                 
                            </div>
                        </div>
                    </div>
                    <p>Mùa thích hợp<span>*</span></p>
                    <div class="flex">  
                        <div class="box">
                            <div class="input-field">
                                <p>Mùa thích hợp 1<span>*</span></p>
                                <input type="text" name="season1" maxlength="100" placeholder="Nhập mùa thích hợp" required class="box">
                            </div>  
                            <div class="input-field">
                                <p>Mùa thích hợp 2</p>
                                <input type="text" name="season2" maxlength="100" placeholder="Nhập mùa thích hợp" class="box">
                            </div>    
                        </div>
                        <div class="box">
                            <div class="input-field">
                                <p>Mùa thích hợp 3</p>
                                <input type="text" name="season3" maxlength="100" placeholder="Nhập mùa thích hợp" class="box">                                 
                            </div>
                            <div class="input-field">
                                <p>Mùa thích hợp 4</p>
                                <input type="text" name="season4" maxlength="100" placeholder="Nhập mùa thích hợp" class="box">                                 
                            </div>
                        </div>
                    </div>
                    <div class="input-field">
                        <p>Độ tuổi phù hợp<span>*</span></p>
                        <div style="display: flex; gap: 15px;">
                            <input type="number" name="age_from" placeholder="Từ" class="box" required style="flex: 1;">
                            <input type="number" name="age_to" placeholder="Đến" class="box" required style="flex: 1;">
                        </div>
                    </div>
                    <div class="input-field">
                        <p>Chi tiết sản phẩm<span>*</span></p>
                        <textarea name="description" require maxlength="1000" placeholder="Nhập chi tiết sản phẩm" required class="box"></textarea>
                    </div>
                    <div class="input-field">
                        <p>Ảnh tổng quan<span>*</span></p>
                        <input type="file" name="background" accept="image/*" required class="box">

                    </div>
                    <div class="input-field">
                        <p>Ảnh mô tả<span>*</span></p>
                        <!-- <input type="file" name="image" accept="image/*" require class="box"> -->
                        <input type="file" name="images[]" accept="image/*" multiple required class="box">

                    </div>
                    <div class="flex-btn">
                        <input type="submit" name="publish" value="Thêm sản phẩm" class="btn" onclick="return confirm ('Bạn có chắc muốn thêm sản phẩm này không?');">
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