<?php 
    include '../component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    // Lấy tháng và năm từ URL hoặc sử dụng tháng hiện tại
    $month_year = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
    list($year, $month) = explode('-', $month_year);

    // Biến tổng để hiển thị ở phần chân bảng
    $total_revenue = 0; // Tổng doanh thu
    $total_net_profit = 0; // Tổng lợi nhuận ròng
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Revenue detail page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page">
<div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="user-container">
            <div class="heading">
                <h1>Doanh thu và Lợi nhuận - Tháng <?= htmlspecialchars($month) ?>/<?= htmlspecialchars($year) ?></h1>
            </div>
            <div class="box-container">
                <div class="box order">
                    <div class="custom-line"></div>
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Doanh thu (sau chiết khấu)</th>
                                <th>Lợi nhuận</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Truy vấn doanh thu và lợi nhuận theo tháng
                            $select_bills = $conn->prepare(
                                "SELECT bill_id, ngaydat, tongtien, tongtienvon, chietkhau
                                 FROM `bill`
                                 WHERE YEAR(ngaydat) = ? AND MONTH(ngaydat) = ?
                                 ORDER BY ngaydat DESC"
                            );
                            $select_bills->execute([$year, $month]);

                            if ($select_bills->rowCount() > 0) {
                                $stt = 1;
                                while ($bill = $select_bills->fetch(PDO::FETCH_ASSOC)) {
                                    // Tính doanh thu và lợi nhuận
                                    $revenue = $bill['tongtien'] - $bill['chietkhau']; // Doanh thu sau chiết khấu
                                    $net_profit = $revenue - $bill['tongtienvon']; // Lợi nhuận ròng

                                    // Cộng dồn tổng
                                    $total_revenue += $revenue;
                                    $total_net_profit += $net_profit;

                                    // Hiển thị hàng dữ liệu
                                    echo "
                                        <tr>
                                            <td>{$stt}</td>
                                            <td>{$bill['bill_id']}</td>
                                            <td>" . date('d-m-Y', strtotime($bill['ngaydat'])) . "</td>
                                            <td>" . number_format($revenue, 0, ',', '.') . " VNĐ</td>
                                            <td>" . number_format($net_profit, 0, ',', '.') . " VNĐ</td>
                                        </tr>
                                    ";
                                    $stt++;
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="5" style="text-align: center;">Chưa có đơn hàng nào trong tháng này!</td>
                                    </tr>
                                ';
                            }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: center; font-weight: bold;">Tổng</td>
                                <td><?= number_format($total_revenue, 0, ',', '.'); ?> VNĐ</td>
                                <td><?= number_format($total_net_profit, 0, ',', '.'); ?> VNĐ</td>
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

    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
