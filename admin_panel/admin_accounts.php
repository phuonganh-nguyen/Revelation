<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
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
    <title>révélation - Accounts page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page"> 
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?> 
        <section class="user-container">
            <div class="heading-search">
                <h1>Danh sách quản trị viên</h1>
                <form action="search_user.php" method="post" class="search-form"> 
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
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Ngày tạo</th>
                                <th>Vai trò</th> <!-- Cột mới cho vai trò -->
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $select_users = $conn->prepare("SELECT * FROM `user` WHERE vaitro IN ('nhanvien', 'kho')");
                            $select_users->execute();

                            if ($select_users->rowCount() > 0) {
                                $stt = 1; // Khởi tạo số thứ tự
                                while ($fetch_user = $select_users->fetch(PDO::FETCH_ASSOC)){
                                    // Xác định vai trò
                                    $vaitro = $fetch_user['vaitro'] === 'nhanvien' ? 'Nhân viên duyệt đơn' : 'Thủ kho';
                                    ?>
                                    <tr>
                                        <td><?= $stt++; ?></td> <!-- In số thứ tự -->
                                        <td><?= $fetch_user['user_id']; ?></td>
                                        <td style="text-transform: capitalize;"><?= $fetch_user['name']; ?></td>
                                        <td><?= $fetch_user['email']; ?></td>
                                        <td><?= $fetch_user['ngaytao']; ?></td> <!-- Ngày tạo -->
                                        <td><?= $vaitro; ?></td> <!-- Hiển thị vai trò -->
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="align-items: center; display: flex; justify-content: center;">
                <a href="add_employee.php" class="btn">Thêm Nhân viên</a>
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

    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>