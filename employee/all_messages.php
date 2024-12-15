<?php 
    //bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['nv_id'])) {
        $user_id = $_COOKIE['nv_id'];
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
    <title>révélation - Admin message</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
</head>
<body id="acc-page"> 
    <div class="main-container">
        <?php include '../component/employee_header.php'; ?> 
        <section class="user-container">
            <div class="heading-search">
                <h1>Tin nhắn</h1>
                <form action="" method="post" class="search-form"> 
                    <input type="text" name="search_user" placeholder="Tìm kiếm tài khoản" required maxlength="100">
                    <button type="submit" class="bi bi-search" id="search_product_btn"></button>
                </form>
            </div>
            <div class="box-container mess">
                <div class="box">
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                        <thead>
                            <tr>
                                <th>ID KH</th>
                                <th>Tên khách hàng</th>
                                <th>Tin nhắn mới nhất</th>
                                <th>Ngày gửi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Lấy danh sách tin nhắn mới nhất của từng user_id từ bảng send_message
                            $select_message = $conn->prepare("
                                SELECT user_id, noidung, ngaygui 
                                FROM send_message 
                                WHERE user_id IS NOT NULL 
                                ORDER BY ngaygui DESC
                            ");
                            $select_message->execute();

                            $seen_users = []; // Lưu danh sách user_id đã hiển thị

                            if ($select_message->rowCount() > 0) {
                                while ($message = $select_message->fetch(PDO::FETCH_ASSOC)) {
                                    $user_id = $message['user_id'];

                                    // Bỏ qua nếu user_id đã được hiển thị
                                    if (in_array($user_id, $seen_users)) {
                                        continue;
                                    }

                                    // Lưu user_id đã xử lý
                                    $seen_users[] = $user_id;

                                    // Lấy thông tin người dùng từ bảng users
                                    $select_user = $conn->prepare("SELECT name FROM user WHERE user_id = ?");
                                    $select_user->execute([$user_id]);
                                    $user_info = $select_user->fetch(PDO::FETCH_ASSOC);

                                    // Hiển thị thông tin người dùng và tin nhắn
                                    ?>
                                    <tr onclick="window.location.href='message.php?user_id=<?= $message['user_id']; ?>'">
                                        <td><?= $user_id; ?></td> <!-- ID khách hàng -->
                                        <td style="text-transform: capitalize;"><?= htmlspecialchars($user_info['name'] ?? 'Không xác định'); ?></td> <!-- Tên -->
                                        <td><?= htmlspecialchars($message['noidung']); ?></td> <!-- Tin nhắn mới nhất -->
                                        <td><?= date('H:i d/m/Y', strtotime($message['ngaygui'])); ?></td> <!-- Ngày gửi -->
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="4" style="text-align: center;">Không có tin nhắn!</td>
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
        function searchCustomer() {
            const searchTerm = document.querySelector('input[name="search_user"]').value.toLowerCase(); // Lấy từ khóa tìm kiếm
            const rows = document.querySelectorAll('.box-container tbody tr'); // Lấy tất cả các hàng trong bảng

            let foundAnyCustomer = false; // Kiểm tra xem có khách nào khớp không

            rows.forEach((row) => {
                const customerId = row.querySelectorAll('td')[1].textContent.toLowerCase(); // Cột ID khách
                const customerName = row.querySelectorAll('td')[2].textContent.toLowerCase(); // Cột tên khách
                const customerEmail = row.querySelectorAll('td')[3].textContent.toLowerCase(); // Cột email khách

                // Kiểm tra xem có khớp với từ khóa tìm kiếm không
                if (customerId.includes(searchTerm) || customerName.includes(searchTerm) || customerEmail.includes(searchTerm)) {
                    row.style.display = ''; // Hiển thị hàng
                    foundAnyCustomer = true; // Đánh dấu đã tìm thấy khách
                } else {
                    row.style.display = 'none'; // Ẩn các hàng không khớp
                }
            });

            if (!foundAnyCustomer) {
                alert('Không tìm thấy khách hàng nào!');
            }
        }

        // Gắn sự kiện khi người dùng bấm nút tìm kiếm
        document.querySelector('.search-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn chặn việc gửi form thông thường
            searchCustomer(); // Gọi hàm tìm kiếm
        });
    </script>
    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>