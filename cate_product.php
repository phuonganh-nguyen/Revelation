<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = '';
    }

    $category_id = $_GET['category_id'];
    include 'component/add_wishlist.php';
    include 'component/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Shop page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    <!-- slider section star -->
    <div class="products" >
        <div class="filter">
            <h3>Khoảng giá</h3>
            <div class="price-filter">             
                <input type="number" id="min-price" name="min-price" placeholder="Từ VNĐ" min="0">
                <input type="number" id="max-price" name="max-price" placeholder="Đến VNĐ" min="0">
            </div>
            <button type="button" onclick="applyFilters()" class="apply-button">Áp dụng</button>
        </div>
        <div class="product-list">
                <?php 
                    $select_thuonghieu = $conn->prepare("SELECT name FROM danhmuc WHERE danhmuc_id = ?");
                    $select_thuonghieu->execute([$category_id]);
                    $thuonghieu_name = $select_thuonghieu->fetch(PDO::FETCH_ASSOC)['name'];
                ?>
                <div class="heading">
                    <h1><?=  $thuonghieu_name;?></h1>
                    <h3 id="no-products-message" style="display: none; margin-bottom: 18rem;">Không tìm thấy sản phẩm phù hợp</h3>
                </div>

                <div class="box-container" style="margin-top: -1rem;">
                <?php 
                if (isset($_GET['category_id'])) {
                    $category_id = $_GET['category_id']; // Khởi tạo biến $category_id từ URL

                    // Lấy tên danh mục dựa trên ID danh mục
                    $select_category = $conn->prepare("SELECT name FROM danhmuc WHERE danhmuc_id = ?");
                    $select_category->execute([$category_id]);
                    $category_name = $select_category->fetch(PDO::FETCH_ASSOC)['name'];

                    // Lấy danh sách sản phẩm thuộc danh mục và đang hoạt động
                    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE BINARY loaisp = ? AND trangthai=?");
                    $select_products->execute([$category_name, 'Đang hoạt động']);

                    if ($select_products->rowCount() > 0) {
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                            
                            // Lấy tổng số lượng từ các cỡ sản phẩm
                            $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];

                            // Kiểm tra nếu tổng số lượng > 0 thì mới hiển thị sản phẩm
                            if ($total_quantity > 0) {
                    ?>
                            <div class="box" data-category="<?= $fetch_products['loaisp'] ?>" data-price="<?= $fetch_products['price'] ?>" onclick="window.location.href='view_page.php?pid=<?= $fetch_products['sanpham_id']?>'">
                                <img src="uploaded_files/<?= $fetch_products['image'];?>" class="image">
                                <?php
                                    // Kiểm tra trạng thái dựa trên tổng số lượng
                                    if ($total_quantity > 0 && $total_quantity <= 5) {
                                        echo '<span class="soluong" style="color: red;">Chỉ còn ' . $total_quantity . ' sản phẩm</span>';
                                    }
                                ?>
                                <div class="content">
                                    <div class="button">
                                    <?php
                                        $product_name = $fetch_products['name']; // Lấy tên sản phẩm

                                        // Kiểm tra độ dài của tên sản phẩm
                                        if (mb_strlen($product_name) > 24) {
                                            $product_name = mb_substr($product_name, 0, 23) . '...';
                                        }
                                    ?>
                                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $product_name ?></a></div>
                                    </div>
                                    <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.') ?> VNĐ</p>
                                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                                </div>
                            </div>
                    <?php
                                } // Kết thúc kiểm tra số lượng
                            } // Kết thúc vòng lặp while
                        } 
                    }
                ?>
                
            </div>
        </div>
    </div>
    <script>
        function applyFilters() {
            const minPrice = parseInt(document.getElementById('min-price').value) || 0;
            const maxPrice = parseInt(document.getElementById('max-price').value) || Infinity;
            const selectedCategory = document.querySelector('input[name="category"]:checked')?.value || null;

            const products = document.querySelectorAll('.box-container .box');
            const productHeading = document.getElementById('product-heading'); // Tiêu đề sản phẩm
            const noProductsMessage = document.getElementById('no-products-message'); // Thông báo không tìm thấy sản phẩm
            let productFound = false; // Biến kiểm tra có sản phẩm hay không

            products.forEach(product => {
                const productPrice = parseInt(product.getAttribute('data-price'));
                const productCategory = product.getAttribute('data-category');

                // Kiểm tra điều kiện giá
                const priceCondition = productPrice >= minPrice && productPrice <= maxPrice;

                // Kiểm tra điều kiện danh mục
                const categoryCondition = selectedCategory ? productCategory === selectedCategory : true;

                // Hiển thị hoặc ẩn sản phẩm
                if (priceCondition && categoryCondition) {
                    product.style.display = '';
                    productFound = true; // Có sản phẩm thỏa mãn điều kiện
                } else {
                    product.style.display = 'none';
                }
            });

            // Hiển thị thông báo nếu không có sản phẩm nào
            if (!productFound) {
                noProductsMessage.style.display = 'block'; // Hiển thị thông báo không tìm thấy
            } else {
                noProductsMessage.style.display = 'none'; // Ẩn thông báo không tìm thấy
            }
        }


    </script>
    
    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>