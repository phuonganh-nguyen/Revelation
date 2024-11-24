<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
include '../component/connect.php';
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:../login.php');
}

    //delete product
    if (isset($_POST['delete'])) {
        $p_id = $_POST['product_id'];

        // Lấy tất cả các hình ảnh liên quan đến sản phẩm
    $select_images = $conn->prepare("SELECT img_path FROM `anhsp` WHERE sanpham_id=?");
    $select_images->execute([$p_id]);

    // Lặp qua tất cả các hình ảnh và xóa chúng từ thư mục lưu trữ
    while ($image = $select_images->fetch(PDO::FETCH_ASSOC)) {
        $image_path = "../uploaded_files/" . $image['img_path'];
        if (file_exists($image_path)) {
            unlink($image_path); // Xóa hình ảnh từ thư mục lưu trữ
        }
    }

    // Xóa tất cả các hình ảnh liên quan đến sản phẩm từ bảng `anhsp`
    $delete_images = $conn->prepare("DELETE FROM `anhsp` WHERE sanpham_id=?");
    $delete_images->execute([$p_id]);
        $delete_product = $conn->prepare("DELETE FROM `sanpham` where sanpham_id=?");
        $delete_product->execute([$p_id]);

        $success_msg[] = 'Sản phẩm đã được xóa thành công!';
    }

    // Cập nhật trạng thái sản phẩm
    if (isset($_POST['update_status'])) {
        $p_id = $_POST['product_id'];
        $current_status = $_POST['current_status']; // Trạng thái hiện tại

        // Đảo ngược trạng thái
        $new_status = ($current_status === 'Đang hoạt động') ? 'Ngừng hoạt động' : 'Đang hoạt động';

        // Cập nhật trạng thái trong cơ sở dữ liệu
        $update_status = $conn->prepare("UPDATE `sanpham` SET trangthai = ? WHERE sanpham_id = ?");
        $update_status->execute([$new_status, $p_id]);

        $success_msg[] = 'Trạng thái sản phẩm đã được cập nhật thành công!';
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Product display page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="storekeeper-page">
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="show-post">
        <form action="javascript:void(0);" method="post" class="search-form">
            <input type="text" name="search_product" placeholder="Tìm kiếm sản phẩm" required maxlength="100">
            <button type="submit" class="bi bi-search" id="search_product_btn"></button>
        </form>

        <div class="heading" style="margin-bottom: 1rem; display: flex; gap: 30%; margin-left: 11%;">
            <h2 style="font-size: 1.2rem; text-transform: uppercase;">Tất cả sản phẩm:</h2>
            <h2 style="font-size: 1.2rem; text-transform: uppercase;"><a href="low_stock.php" class="low-stock">Sản phẩm sắp hết hàng <i class="bi bi-exclamation-circle-fill" style="font-size: 1.2rem;"></i></a></h2>
        </div>

        <?php
        // Lấy danh sách danh mục từ cơ sở dữ liệu
        $select_categories = $conn->prepare("SELECT * FROM `danhmuc` ORDER BY CASE WHEN name = 'phụ kiện khác' THEN 1 ELSE 0 END, name ASC");
        $select_categories->execute();

        // Lặp qua từng danh mục
        while ($category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="box-container">
                <div class="box">
                    <h2><?= htmlspecialchars($category['name']); ?></h2>
                    <?php
                    // Lấy sản phẩm theo danh mục
                    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE loaisp = ?"); // Giả sử 'loaisp' là tên cột chứa thông tin danh mục trong bảng 'sanpham'
                    $select_products->execute([$category['name']]); // Thay 'id' bằng tên cột chứa ID danh mục

                    if ($select_products->rowCount() > 0) {
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                            $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];
                            ?>
                            <form action="" method="post" class="product-stock" id="product-<?= $fetch_products['sanpham_id']; ?>">
                                <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']; ?>">
                                    
                                <a href="edit_product.php?id=<?= $fetch_products['sanpham_id']; ?>">
                                    <?= htmlspecialchars($fetch_products['name']); ?> (SL: <?= $total_quantity; ?>)
                                </a>
                                <div class="product-actions">
                                    <input type="hidden" name="current_status" value="<?= htmlspecialchars($fetch_products['trangthai']); ?>">
                                    <button type="submit" name="update_status" class="hidden-link">
                                        <?= $fetch_products['trangthai'] === 'Đang hoạt động' ? 'Hiện' : 'Ẩn'; ?>
                                    </button>
                                    <a href="review_page.php?post_id=<?= $fetch_products['sanpham_id']; ?>" class="review-link">Đánh giá</a>   
                                </div>
                            </form>
                            <?php
                        }
                    } else {
                        echo '
                            <div>
                                <p>Chưa có sản phẩm nào được thêm vào!</p>
                            </div>
                        ';
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>

        </section>
    </div>

    <script>
        // Hàm tìm kiếm sản phẩm và ẩn các hàng không khớp
function searchProduct() {
    const searchTerm = document.querySelector('input[name="search_product"]').value.toLowerCase(); // Lấy từ khóa tìm kiếm
    const products = document.querySelectorAll('.product-stock'); // Lấy tất cả sản phẩm
    let found = false;

    products.forEach((product) => {
        const productName = product.querySelector('a').textContent.toLowerCase(); // Lấy tên sản phẩm

        // Kiểm tra nếu tên sản phẩm trùng với từ khóa tìm kiếm
        if (productName.includes(searchTerm)) {
            product.style.display = ''; // Hiển thị sản phẩm tìm thấy
            found = true; // Đánh dấu là đã tìm thấy ít nhất 1 sản phẩm
        } else {
            product.style.display = 'none'; // Ẩn sản phẩm không khớp
        }
    });

    if (!found) {
        alert('Không tìm thấy sản phẩm!');
    }
}

// Gắn sự kiện submit vào form tìm kiếm
document.querySelector('.search-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Ngăn chặn việc gửi form thông thường
    searchProduct(); // Gọi hàm tìm kiếm
});

    </script>

    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>