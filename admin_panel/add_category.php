<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    if (isset($_POST['submit'])) {
        $danhmuc_id = sp_id();
        $ten_danhmuc = $_POST['ten_danhmuc'];
    
        // Kiểm tra nếu danh mục không rỗng
        if (!empty($ten_danhmuc)) {
            // Hàm binary phân biệt dấu sắc hỏi ngã nặng
            $select_type = $conn->prepare("SELECT * FROM `danhmuc` WHERE BINARY name = ?");
            $select_type->execute([$ten_danhmuc]);
            
            if ($select_type->rowCount() > 0) {
                // Danh mục đã tồn tại, hiển thị thông báo
                $warning_msg[] = 'Danh mục đã tồn tại';
            } else {
                // Thêm danh mục mới vào cơ sở dữ liệu
                $insert_type = $conn->prepare("INSERT INTO `danhmuc`(danhmuc_id, name) VALUES (?,?)");
                $insert_type->execute([$danhmuc_id, $ten_danhmuc]);
                header('location:show_categories.php');
            }
        } else {
            // Hiển thị thông báo nếu danh mục rỗng
            $warning_msg[] = 'Tên danh mục không được để trống';
        }
    }
    
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Add category page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
</head>
<body>
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="post-editor"> 
            <div class="back">
                <a href="show_categories.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
            <div class="heading">
                <h1>Thêm danh mục</h1>
            </div>
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="register">    
                    <div class="input-field">
                        <p>Tên danh mục<span>*</span></p>
                        <input type="text" name="ten_danhmuc" maxlength="100" placeholder="Nhập tên danh mục" require class="box">
                    </div>
                    <div class="flex-btn">
                        <input type="submit" name="submit" value="Thêm" class="btn">
                        
                    </div>
                </form>
            </div>
        </section> 
    </div>

    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>