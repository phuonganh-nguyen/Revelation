<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    // Khởi tạo mảng lưu tổng chi phí nhập kho và doanh thu theo tháng
    $monthly_data = [];

    $select_nhapkho = $conn->prepare("
        SELECT DATE_FORMAT(nk.ngaynhap, '%Y-%m') AS month, 
    SUM((COALESCE(nk.sizeS, 0) + COALESCE(nk.sizeM, 0) + COALESCE(nk.sizeL, 0) + COALESCE(nk.sizeXL, 0) + COALESCE(nk.freesize, 0)) * sp.old_price) AS total_cost
FROM nhapkho AS nk
JOIN sanpham AS sp ON nk.sanpham_id = sp.sanpham_id
GROUP BY month

    ");
    $select_nhapkho->execute();

    while ($row = $select_nhapkho->fetch(PDO::FETCH_ASSOC)) {
        $monthly_data[$row['month']]['chi_phi_nhap_kho'] = $row['total_cost'];
    }

    $select_bill = $conn->prepare("
    SELECT DATE_FORMAT(ngaydat, '%Y-%m') AS month, 
    SUM(tongtien) AS total_revenue, 
    SUM(chietkhau) AS total_discount, 
    SUM(tongtienvon) AS total_cost_price
    FROM bill
    GROUP BY month
");
$select_bill->execute();

while ($row = $select_bill->fetch(PDO::FETCH_ASSOC)) {
    $monthly_data[$row['month']]['doanh_thu'] = $row['total_revenue'];
    $monthly_data[$row['month']]['chiet_khau'] = $row['total_discount'];
    $monthly_data[$row['month']]['tong_tien_von'] = $row['total_cost_price'];
}

// Tính lợi nhuận gộp và lợi nhuận ròng
foreach ($monthly_data as $month => $data) {
    $monthly_data[$month]['loi_nhuan_gop'] = $data['doanh_thu'] - $data['chi_phi_nhap_kho'];  // Lợi nhuận gộp
    $monthly_data[$month]['loi_nhuan_rong'] = ($data['doanh_thu'] - $data['chiet_khau']) - $data['tong_tien_von']; // Lợi nhuận ròng
}
?> 

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Revenue report</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page"> 
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?> 
        <section class="user-container">
            <div class="heading">
                <h1>Báo cáo tổng quan</h1>
            </div>
            <div class="chart">
                <div>
                    <canvas id="myChart"></canvas>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const ctx = document.getElementById('myChart');

                    // Dữ liệu cho biểu đồ
                    const labels = []; // Tháng
                    const chiPhiNhapKho = []; // Chi phí nhập kho
                    const doanhThu = []; // Doanh thu
                    const loiNhuanGop = []; // Lợi nhuận gộp
                    const loiNhuanRong = []; // Lợi nhuận ròng

                    <?php
                    // Lấy tất cả dữ liệu từ mảng monthly_data
                    // Lấy 5 tháng gần nhất cho biểu đồ
                    $lastFiveMonthsData = array_slice($monthly_data, 0, 5, true);
                    
                    // Sắp xếp dữ liệu theo tháng (giả định định dạng m/Y)
                    uksort($lastFiveMonthsData, function($a, $b) {
                        return strtotime($a) - strtotime($b); // Sắp xếp tăng dần
                    });

                    // Lặp qua mảng monthly_data và tạo dữ liệu cho biểu đồ
                    foreach ($lastFiveMonthsData as $month => $data) {
                        echo "labels.push('" . date('m/Y', strtotime($month)) . "');"; // Thêm tháng
                        echo "chiPhiNhapKho.push(" . $data['chi_phi_nhap_kho'] . ");"; // Thêm chi phí nhập kho
                        echo "doanhThu.push(" . $data['doanh_thu'] . ");"; // Thêm doanh thu
                        echo "loiNhuanGop.push(" . $data['loi_nhuan_gop'] . ");"; // Thêm lợi nhuận gộp
                        echo "loiNhuanRong.push(" . $data['loi_nhuan_rong'] . ");"; // Thêm lợi nhuận ròng
                    }
                    ?>

                    new Chart(ctx, {
                        type: 'line', // Đường
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Chi phí nhập kho',
                                    data: chiPhiNhapKho,
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                    fill: true
                                },
                                {
                                    label: 'Doanh thu',
                                    data: doanhThu,
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    fill: true
                                },
                                {
                                    label: 'Lợi nhuận ròng',
                                    data: loiNhuanRong,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    fill: true
                                },
                                {
                                    label: 'Lợi nhuận gộp',
                                    data: loiNhuanGop,
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                },
                                x: {
                                    reverse: false // Đảm bảo tháng gần nhất hiển thị bên trái
                                }
                            }
                        }
                    });
                </script>

            </div>
            <div class="box-container">
                <div class="box order">
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: center;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tháng</th>
                                <th>Chi phí nhập kho</th>
                                <th>Doanh thu (sau chiết khấu)</th>
                                <th>Lợi nhuận ròng</th>
                                <th>Lợi nhuận gộp</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if (!empty($monthly_data)) {
                                uksort($monthly_data, function($a, $b) {
                                    return strtotime($b) - strtotime($a); // Sắp xếp tháng giảm dần
                                });
                                $stt = 1;
                                foreach ($monthly_data as $month => $data) {
                                    ?>
                                    <tr>
                                        <td><?= $stt++; ?></td>
                                        <td><?= date('m/Y', strtotime($month)); ?></td>
                                        <td>
                                            <a href="stock_detail.php?month=<?= $month; ?>">
                                                <?= number_format($data['chi_phi_nhap_kho'], 0, ',', '.'); ?> VNĐ
                                            </a>
                                        </td>
                                        <td>
                                            <a href="revenue_detail.php?month=<?= $month; ?>">
                                                <?= number_format($data['doanh_thu'], 0, ',', '.'); ?> VNĐ</td>
                                            </a>                                          
                                        <td>
                                            <?= number_format($data['loi_nhuan_rong'], 0, ',', '.'); ?> VNĐ
                                            
                                        </td>
                                        <td>
                                            <?php if ($data['loi_nhuan_gop'] < 10000000 ) { ?>
                                                <span style="color: red;">
                                                    <?= number_format($data['loi_nhuan_gop'], 0, ',', '.'); ?> VNĐ
                                                </span>
                                            <?php } else { ?>
                                                <?= number_format($data['loi_nhuan_gop'], 0, ',', '.'); ?> VNĐ
                                            <?php } ?>
                                            
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="6">Chưa có dữ liệu</td>
                                    </tr>
                                ';
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
