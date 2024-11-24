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

        $delete_product = $conn->prepare("DELETE FROM `sanpham` where sanpham_id=?");
        $delete_product->execute([$p_id]);

        $success_msg[] = 'Sản phẩm đã được xóa thành công!';
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
            <div class="heading" style="margin-bottom: 1rem;">
                <h1>Sản phẩm đang hoạt động</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
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
                    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE loaisp = ? AND trangthai=?"); // Giả sử 'loaisp' là tên cột chứa thông tin danh mục trong bảng 'sanpham'
                    $select_products->execute([$category['name'], 'Ngừng hoạt động']); // Thay 'id' bằng tên cột chứa ID danh mục

                    if ($select_products->rowCount() > 0) {
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                            $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];
                            ?>
                            <form action="" method="post" class="product-stock" id="product-<?= $fetch_products['sanpham_id']; ?>">
                                <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']; ?>">
                                <a>
                                    <?= htmlspecialchars($fetch_products['name']); ?> (Số lượng: <?= $total_quantity; ?>)
                                </a>
                                <div class="product-actions">
                                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']; ?>">
                                    <a href="edit_product.php?id=<?= $fetch_products['sanpham_id']; ?>" class="edit-link">Sửa</a>
                                    <a href="read_product.php?post_id=<?= $fetch_products['sanpham_id']; ?>" class="review-link">Đánh giá</a>
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
        // Hàm tìm kiếm sản phẩm và cuộn đến sản phẩm đó
        function searchProduct() {
            const searchTerm = document.querySelector('input[name="search_product"]').value.toLowerCase();
            const products = document.querySelectorAll('.product-stock');
            let found = false;

            products.forEach((product) => {
                const productName = product.querySelector('a').textContent.toLowerCase();
                
                // Kiểm tra nếu tên sản phẩm trùng với từ khóa tìm kiếm
                if (productName.includes(searchTerm)) {
                    // Đổi màu nền của form sản phẩm tìm thấy
                    product.style.backgroundColor = '#851639';  // Màu nền khi tìm thấy
                    product.querySelector('a').style.color = 'white'; 
                    found = true;

                    // Cuộn đến sản phẩm tìm thấy
                    product.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Đặt lại màu nền sau 3 giây
                    setTimeout(() => {
                        product.style.backgroundColor = '';
                        product.querySelector('a').style.color = ''; // Đặt lại màu chữ
                    }, 3000);
                }
            });

            if (!found) {
                alert('Không tìm thấy sản phẩm!');
            }
        }

        // Gắn sự kiện submit vào form tìm kiếm
        document.querySelector('.search-form').addEventListener('submit', function(e) {
            e.preventDefault();  // Ngăn chặn việc gửi form thông thường
            searchProduct();  // Gọi hàm tìm kiếm
        });
    </script>
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>