<?php 
include '../component/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:../login.php');
}

// Lấy tháng và năm từ URL
$month_year = isset($_GET['month']) ? $_GET['month'] : date('Y-m'); // Nếu không có tháng/năm trong URL, mặc định là tháng/năm hiện tại
list($year, $month) = explode('-', $month_year); // Tách năm và tháng từ chuỗi YYYY-MM
$total_sizeS = 0;
$total_sizeM = 0;
$total_sizeL = 0;
$total_sizeXL = 0;
$total_freesize = 0;
$total_overall_cost = 0;
$total_cost = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Stock detail page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page">
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="user-container">
        
            <div class="heading">
                <h1>Danh sách nhập kho - Tháng <?= htmlspecialchars($month) ?>/<?= htmlspecialchars($year) ?></h1>
            </div>
            <div class="box-container">
                <div class="box order">
                    <div class="custom-line"></div>
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                    <thead>
                            <tr>
                                <th>STT</th>
                                <th>Ngày nhập</th>
                                <th>Tên sản phẩm</th>
                                <th>Size S</th>
                                <th>Size M</th>
                                <th>Size L</th>
                                <th>Size XL</th>
                                <th>Free size</th>
                                <th>Giá nhập</th>
                                <th>Nhân viên</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $select_products = $conn->prepare(
                                "SELECT nhapkho.*, sanpham.name, sanpham.old_price, nhapkho.ngaynhap
                                 FROM `nhapkho`
                                 JOIN `sanpham` ON nhapkho.sanpham_id = sanpham.sanpham_id 
                                 WHERE YEAR(nhapkho.ngaynhap) = ? AND MONTH(nhapkho.ngaynhap) = ?
                                 ORDER BY nhapkho.ngaynhap DESC"
                            );
                            $select_products->execute([$year, $month]);

                            if ($select_products->rowCount() > 0) {
                                $stt = 1;
                                $prev_date = null;
                                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                                    $ngaynhap = $fetch_products['ngaynhap'];
                                    $formatted_date = date('d-m-Y', strtotime($ngaynhap));
                                    if ($prev_date != $formatted_date) {
                                        $prev_date = $formatted_date;
                                    }

                                    // Tính tổng số lượng và chi phí nhập kho
                                    $total_quantity = ($fetch_products['sizeS'] ?? 0) + ($fetch_products['sizeM'] ?? 0) + ($fetch_products['sizeL'] ?? 0) + ($fetch_products['sizeXL'] ?? 0) + ($fetch_products['freesize'] ?? 0);
                                    $total_cost_product = $total_quantity * $fetch_products['old_price'];
                                    // Cộng dồn số lượng và chi phí vào biến tổng
                                    $total_sizeS += $fetch_products['sizeS'] ?? 0;
                                    $total_sizeM += $fetch_products['sizeM'] ?? 0;
                                    $total_sizeL += $fetch_products['sizeL'] ?? 0;
                                    $total_sizeXL += $fetch_products['sizeXL'] ?? 0;
                                    $total_freesize += $fetch_products['freesize'] ?? 0;
                                    $total_cost += $total_cost_product;
                        ?>
                                    <tr>
                                        <td><?= $stt++; ?></td>
                                        <td><?= $formatted_date; ?></td>
                                        <td class="name-column">
        <?= mb_strlen($fetch_products['name'], 'UTF-8') > 15 ? mb_substr($fetch_products['name'], 0, 15, 'UTF-8') . '...' : htmlspecialchars($fetch_products['name']); ?>
        <span class="full-name" style="display: none;">
            <?= htmlspecialchars($fetch_products['name']); ?>
        </span>
    </td>
                                        <td><?= $fetch_products['sizeS'] ?? 0; ?></td>
                                        <td><?= $fetch_products['sizeM'] ?? 0; ?></td>
                                        <td><?= $fetch_products['sizeL'] ?? 0; ?></td>
                                        <td><?= $fetch_products['sizeXL'] ?? 0; ?></td>
                                        <td><?= $fetch_products['freesize'] ?? 0; ?></td>
                                        <td><?= number_format($fetch_products['old_price'], 0, ',', '.'); ?> x <?= $total_quantity; ?> = <?= number_format($total_cost_product, 0, ',', '.'); ?></td>
                                        <?php  
                                                $admin_id = $fetch_products['admin_id'];
                                                $select_admin = $conn->prepare("SELECT * FROM `user` WHERE user_id=?");
                                                $select_admin->execute([$admin_id]); 
                                                $fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);?>
                                        <td><?= $fetch_admin['name'] ?></td>
                                    </tr>
                        <?php
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="8" style="text-align: center;">Chưa có sản phẩm nào được thêm vào tháng này!</td>
                                    </tr>
                                ';
                            }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: center; font-weight: bold;">Tổng</td>
                                <td><?= $total_sizeS; ?></td>
                                <td><?= $total_sizeM; ?></td>
                                <td><?= $total_sizeL; ?></td>
                                <td><?= $total_sizeXL; ?></td>
                                <td><?= $total_freesize; ?></td>
                                <td><?= number_format($total_cost, 0, ',', '.'); ?> VND</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <script>
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
<script>
    document.querySelectorAll('.name-column').forEach(function (column) {
    column.addEventListener('mouseenter', function() {
        const fullName = column.querySelector('.full-name');
        fullName.style.display = 'inline-block'; // Hiển thị tên đầy đủ khi hover
    });
    column.addEventListener('mouseleave', function() {
        const fullName = column.querySelector('.full-name');
        fullName.style.display = 'none'; // Ẩn tên đầy đủ khi rời chuột
    });
});

</script>
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
