<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
include '../component/connect.php';
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:../login.php');
}

// Thao tác xử lý cập nhật và xóa danh mục
if (isset($_POST['update_status'])) {
    $danhmuc_id = $_POST['danhmuc_id'];
    $trangthai = $_POST['trangthai'] == 'Hiện' ? 'Ẩn' : 'Hiện';

    // Cập nhật trạng thái của danh mục
    $update_status = $conn->prepare("UPDATE `danhmuc` SET trangthai = ? WHERE danhmuc_id = ?");
    $update_status->execute([$trangthai, $danhmuc_id]);
    // Cập nhật trạng thái của sản phẩm có loaisp là tên danh mục
    if ($trangthai == 'Ẩn') {
        $update_products = $conn->prepare("UPDATE `sanpham` SET trangthai = 'Ngừng hoạt động' WHERE loaisp = (SELECT name FROM danhmuc WHERE danhmuc_id = ?)");
        $update_products->execute([$danhmuc_id]);
    } else {
        // Nếu danh mục được cập nhật thành "Hiện", cập nhật sản phẩm thành "Đang hoạt động"
        $update_products = $conn->prepare("UPDATE `sanpham` SET trangthai = 'Đang hoạt động' WHERE loaisp = (SELECT name FROM danhmuc WHERE danhmuc_id = ?)");
        $update_products->execute([$danhmuc_id]);
    }
    $success_msg[] = 'Cập nhật trạng thái thành công!';
}

if (isset($_POST['delete_category'])) {
    $danhmuc_id = $_POST['danhmuc_id'];

    // Xóa danh mục
    $delete_category = $conn->prepare("DELETE FROM `danhmuc` WHERE danhmuc_id = ?");
    $delete_category->execute([$danhmuc_id]);

    $success_msg[] = 'Xóa danh mục thành công!';
}

?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Show categories page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="storekeeper-page">
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="show-post">
            <div class="back">
                <a href="add_category.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
            <div class="heading" style="margin-bottom: 1rem;">
                <h1>Tất cả danh mục</h1>
            </div>

            <div class="box-container">
                <div class="box">
                    <?php
                    $select_categories = $conn->prepare("SELECT * FROM `danhmuc` ORDER BY CASE WHEN name = 'phụ kiện khác' THEN 1 ELSE 0 END, name ASC");
                    $select_categories->execute();

                    // Lặp qua từng danh mục
                    while ($category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <form action="" method="post" class="product-stock">          
                            <a>
                                <?= htmlspecialchars($category['name']); ?>
                            </a>
                            <div class="product-actions">
                                <input type="hidden" name="danhmuc_id" value="<?= $category['danhmuc_id']; ?>">
                                <input type="hidden" name="trangthai" value="<?= $category['trangthai']; ?>">
                                <!-- <a href="edit_category.php?id=<?= $category['danhmuc_id']; ?>" class="edit-link">Sửa</a> -->
                                <button type="submit" name="update_status" style="margin-right:.5rem;" class="hidden-link"><?= $category['trangthai']; ?></button>
                                <!-- <button type="submit" name="delete_category" class="hidden-link">Xóa</button> -->
                            </div>
                        </form>
                    <?php
                    }
                    ?>
                </div>
            </div> <!-- Kết thúc box-container -->
        </section>
    </div>

    
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
