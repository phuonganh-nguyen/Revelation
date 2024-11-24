<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['kho_id'])) {
        $user_id = $_COOKIE['kho_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Storekeeper page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page">
    <div class="main-container">
        <?php include '../component/stock_header.php'; ?>
        <section class="user-container">
        
            <div class="heading-search">
                <h1>Danh sách sản phẩm</h1>
                <form action="javascript:void(0);" method="post" class="search-form" id="searchForm">
                    <input type="text" name="search_product" id="search_product" placeholder="Tên sản phẩm" required maxlength="100">
                    <button type="submit" class="bi bi-search" id="search_product_btn"></button>
                </form>
            </div>
            <?php
                // Lấy danh sách danh mục từ cơ sở dữ liệu
                $select_categories = $conn->prepare("SELECT * FROM `danhmuc` ORDER BY CASE WHEN name = 'phụ kiện khác' THEN 1 ELSE 0 END, name ASC");
                $select_categories->execute();

                // Lặp qua từng danh mục
                while ($category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="box-container">
                <div class="box order">
                    <h2><?= htmlspecialchars($category['name']); ?></h2>
                    <div class="custom-line"></div>
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên sản phẩm</th>
                                <th>Size S</th>
                                <th>Size M</th>
                                <th>Size L</th>
                                <th>Size XL</th>
                                <th>Free size</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Lấy sản phẩm theo danh mục
                            $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE loaisp = ?");
                            $select_products->execute([$category['name']]);

                            if ($select_products->rowCount() > 0) {
                                $stt = 1; // Khởi tạo số thứ tự
                                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                                    <tr onclick="window.location.href='add_stock.php?post_id=<?= $fetch_products['sanpham_id']; ?>'">
                                        <td><?= $stt++; ?></td> <!-- Số thứ tự -->
                                        <td><?= htmlspecialchars($fetch_products['name']); ?></td> <!-- Tên sản phẩm -->
                                        <td><?= $fetch_products['sizeS'] ?? 0; ?></td> <!-- Size S -->
                                        <td><?= $fetch_products['sizeM'] ?? 0; ?></td> <!-- Size M -->
                                        <td><?= $fetch_products['sizeL'] ?? 0; ?></td> <!-- Size L -->
                                        <td><?= $fetch_products['sizeXL'] ?? 0; ?></td> <!-- Size XL -->
                                        <td><?= $fetch_products['freesize'] ?? 0; ?></td> <!-- Freesize -->
                                    </tr>
                        <?php
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="7" style="text-align: center;">Chưa có sản phẩm nào được thêm vào!</td>
                                    </tr>
                                ';
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
        </section>
    </div>
    <script>
        // Hàm tìm kiếm sản phẩm chỉ theo tên sản phẩm
        function searchProduct() {
            const searchTerm = document.querySelector('input[name="search_product"]').value.toLowerCase(); // Lấy từ khóa tìm kiếm
            const tables = document.querySelectorAll('.box-container'); // Lấy tất cả các bảng sản phẩm
            let foundAnyProduct = false; // Biến để kiểm tra có tìm thấy sản phẩm nào không

            tables.forEach((table) => {
                const rows = table.querySelectorAll('tbody tr'); // Lấy tất cả các hàng trong bảng
                let foundInTable = false; // Biến để kiểm tra xem có sản phẩm nào trong bảng này không

                rows.forEach((row) => {
                    const productName = row.querySelectorAll('td')[1].textContent.toLowerCase(); // Cột tên sản phẩm (cột thứ 2)

                    if (productName.includes(searchTerm)) { // Nếu tìm thấy sản phẩm có tên khớp với từ khóa
                        row.style.display = ''; // Hiển thị hàng
                        foundInTable = true; // Đánh dấu tìm thấy sản phẩm trong bảng
                        foundAnyProduct = true; // Đánh dấu tìm thấy sản phẩm trong toàn bộ trang
                    } else {
                        row.style.display = 'none'; // Ẩn các hàng không khớp
                    }
                });

                // Ẩn hoặc hiện bảng dựa trên việc có sản phẩm nào khớp hay không
                table.style.display = foundInTable ? '' : 'none';
            });

            // Nếu không tìm thấy sản phẩm nào trong tất cả các bảng, hiển thị thông báo
            if (!foundAnyProduct) {
                alert('Không tìm thấy sản phẩm nào!'); // Thông báo nếu không tìm thấy kết quả
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