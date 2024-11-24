<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }


    $search_term = isset($_POST['search_user']) ? $_POST['search_user'] : '';

    // Câu lệnh SQL tìm kiếm theo tên, ID, hoặc email
    $select_users = $conn->prepare("SELECT * FROM `user` WHERE vaitro = 'khach' AND (user_id LIKE :search_term OR name LIKE :search_term OR email LIKE :search_term)");
    $select_users->execute(['search_term' => "%$search_term%"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Acc users page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page"> 
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?> 
        <section class="user-container">
            
            <div class="heading-search">
                <h1>Danh sách khách hàng</h1>
                <form action="" method="post" class="search-form"> 
                    <input type="text" name="search_user" placeholder="Tìm kiếm tài khoản" required maxlength="100">
                    <button type="submit" class="bi bi-search" id="search_product_btn"></button>
                </form>
            </div>
            <div class="box-container">
                <div class="box">
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>ID KH</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $select_users = $conn->prepare("SELECT * FROM `user` WHERE vaitro = 'khach'");
                            $select_users->execute();

                            if ($select_users->rowCount() > 0) {
                                $stt = 1; // Khởi tạo số thứ tự
                                while ($fetch_user = $select_users->fetch(PDO::FETCH_ASSOC)){
                                    ?>
                                    <tr>
                                        <td><?= $stt++; ?></td> <!-- In số thứ tự -->
                                        <td><?= $fetch_user['user_id']; ?></td>
                                        <td style="text-transform: capitalize;"><?= $fetch_user['name']; ?> (<?= $fetch_user['diem']; ?>đ)</td>
                                        <td><?= $fetch_user['email']; ?></td>
                                        <td><?= $fetch_user['ngaytao']; ?></td> <!-- Ngày tạo -->
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="5" style="text-align: center;">Không có người dùng!</td>
                                    </tr>
                                ';
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="align-items: center; display: flex; justify-content: center;">
                <a href="add_customer.php" class="btn">Thêm Khách hàng</a>
            </div>
        </section>
    </div>
    
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId || 'password-input');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);   
        }
    </script>

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