<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    //update order from database
    if (isset($_POST['update_order'])) {
        $hoadon_id = $_POST['hoadon_id'];
        $new_status = $_POST['update_status']; // Lấy giá trị của trạng thái mới từ biểu mẫu
        if($new_status == 'Xác nhận'){
            $status = 'Đã xác nhận';
        }elseif ($new_status == 'Giao hàng') {
            $status = 'Đang giao hàng';
        }
        // Cập nhật trạng thái của đơn hàng trong cơ sở dữ liệu
        $update_status = $conn->prepare("UPDATE `hoadon` SET trangthai=? WHERE hoadon_id=?");
        $update_status->execute([$status, $hoadon_id]);

        $success_msg[] = 'Trạng thái đơn hàng đã được cập nhật';
    }

    if (isset($_POST['delete_order'])) {
        $delete_id = $_POST['hoadon_id'];

        $verify_delete = $conn->prepare("SELECT * FROM `hoadon` WHERE hoadon_id=?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_order = $conn->prepare("DELETE FROM `hoadon` WHERE hoadon_id=?");
            $delete_order->execute([$delete_id]);
            $success_msg[] = 'Đơn hàng đã được xóa thành công!';
        } else {
            $warning_msg[] = 'Đơn hàng đã bị xóa';
        }
    }
?> 

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Admin order</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page"> 
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?> 
        <section class="user-container">
            <div class="heading-search" style="margin-bottom: -1rem;">
                <div>
                    <h1>Danh sách đơn hàng</h1>
                    <div class="order-filter">
                        <h3>Trạng thái</h3>
                        <div class="filter-pending">
                            <select name="type" onchange="filterOrders(this.value)"> 
                                <option value="Tất cả">Tất cả</option>
                                <option value="Chờ xác nhận">Chờ xác nhận</option>
                                <option value="Đóng hàng">Đóng hàng</option>
                                <option value="Đang giao hàng">Đang giao hàng</option>
                                <option value="Đã nhận hàng">Đã nhận hàng</option>
                                <option value="Đã hủy">Đã hủy</option>
                            </select>
                        </div>
                    </div>                   
                </div>
                <form action="javascript:void(0);" method="post" class="search-form">
                    <input type="text" name="search_bill" placeholder="ID đơn, người nhận, ngày" required maxlength="100">
                    <button type="submit" class="bi bi-search" id="search_product_btn"></button>
                </form>
            </div>
            <div class="box-container">
                <div class="box order">
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Ngày đặt</th> 
                                <th>ID đơn hàng</th>
                                <th>ID KH</th>
                                <th>Người nhận</th>
                                <th>Tổng thanh toán</th>
                                <th>Trạng thái</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Truy vấn lấy danh sách đơn hàng
                            $select_orders = $conn->prepare("SELECT b.bill_id, b.hoadon_id, b.user_id, b.name, b.tongthanhtoan, b.trangthai, b.ngaydat FROM bill b JOIN user u ON b.user_id = u.user_id WHERE b.hienthi = 1 ORDER BY b.ngaydat DESC");
                            $select_orders->execute();

                            if ($select_orders->rowCount() > 0) {
                                $stt = 1; // Khởi tạo số thứ tự
                                while ($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)){
                                    ?>
                                    <tr onclick="window.location.href='detail_order.php?get_id=<?= $fetch_order['bill_id']?>'">
                                        <td><?= $stt++; ?></td> <!-- In số thứ tự -->
                                        <td><?= date('H:i d/m/Y', strtotime($fetch_order['ngaydat'])); ?></td> <!-- Ngày đặt -->
                                        <td>#<?= $fetch_order['bill_id']; ?></td>
                                        <td>#<?= $fetch_order['user_id']; ?></td>
                                        <td style="text-transform: capitalize;"><?= htmlspecialchars($fetch_order['name']); ?></td>
                                        <td><?= number_format($fetch_order['tongthanhtoan'], 0, ',', '.'); ?> VNĐ</td>
                                        <td><?= htmlspecialchars($fetch_order['trangthai']); ?></td>
                                        
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="8" style="text-align: center;">Không có đơn hàng nào!</td>
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
    <script>
        // Hàm tìm kiếm đơn hàng
        function searchOrder() {
            const searchTerm = document.querySelector('input[name="search_bill"]').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr'); // Lấy tất cả các hàng trong bảng
            let found = false; // Biến để kiểm tra xem có tìm thấy kết quả không

            rows.forEach((row) => {
                const columns = row.querySelectorAll('td'); // Lấy tất cả các cột trong hàng
                let match = false; // Biến để kiểm tra nếu hàng này khớp với từ khóa tìm kiếm

                // Kiểm tra từng cột trong hàng
                columns.forEach((column) => {
                    if (column.textContent.toLowerCase().includes(searchTerm)) {
                        match = true; // Nếu tìm thấy, đặt biến match là true
                    }
                });

                // Hiển thị hoặc ẩn hàng dựa trên kết quả tìm kiếm
                if (match) {
                    row.style.display = ''; // Hiển thị hàng
                    found = true; // Đặt biến found là true
                } else {
                    row.style.display = 'none'; // Ẩn hàng
                }
            });

            if (!found) {
                alert('Không tìm thấy đơn hàng!'); // Thông báo nếu không tìm thấy
            }
        }

        // Gắn sự kiện submit vào form tìm kiếm
        document.querySelector('.search-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn chặn việc gửi form thông thường
            searchOrder(); // Gọi hàm tìm kiếm
        });

    </script>
    <script>
    // Hàm lọc đơn hàng theo trạng thái
    function filterOrders(status) {
        const rows = document.querySelectorAll('tbody tr'); // Lấy tất cả các hàng trong bảng

        rows.forEach(row => {
            const statusColumn = row.querySelector('td:nth-child(7)'); // Cột trạng thái (thứ 7 trong bảng)
            if (status === 'Tất cả' || statusColumn.textContent.trim() === status) {
                row.style.display = ''; // Hiển thị hàng
            } else {
                row.style.display = 'none'; // Ẩn hàng
            }
        });
    }

    // Gắn sự kiện onchange vào dropdown (nếu chưa có trong HTML)
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        filterOrders(this.value);
    });
</script>

    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
