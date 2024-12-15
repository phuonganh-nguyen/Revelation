<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    if (isset($_POST['change_status'])) {
        $user_id = $_POST['user_id'];
    
        // Lấy trạng thái hiện tại của người dùng
        $select_status = $conn->prepare("SELECT trangthai FROM user WHERE user_id = ?");
        $select_status->execute([$user_id]);
        $user = $select_status->fetch(PDO::FETCH_ASSOC);
    
        // Đổi trạng thái
        if ($user['trangthai'] == 1) {
            // Nếu đang hoạt động, thay đổi thành ngừng hoạt động
            $update_status = $conn->prepare("UPDATE user SET trangthai = 0 WHERE user_id = ?");
            $update_status->execute([$user_id]);
        } else {
            // Nếu ngừng hoạt động, thay đổi thành đang hoạt động
            $update_status = $conn->prepare("UPDATE user SET trangthai = 1 WHERE user_id = ?");
            $update_status->execute([$user_id]);
        }
    
        // Sau khi thay đổi, chuyển hướng lại trang danh sách quản trị viên
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
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
                <h1>Danh sách nhân viên</h1>
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
                                <th>Vai trò</th>
                                <th>Hành động</th>
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
                                        <td><?= $vaitro; ?></td>
                                        <td class="status-user">
                                            <?php 
                                                // Kiểm tra trạng thái của người dùng và hiển thị nút tương ứng
                                                if ($fetch_user['trangthai'] == 1) {
                                                    // Trạng thái đang hoạt động, hiển thị nút "Ngừng hoạt động"
                                                    echo '<form method="POST">
                                                            <input type="hidden" name="user_id" value="' . $fetch_user['user_id'] . '">
                                                            <button type="submit" name="change_status">Vô hiệu hóa</button>
                                                        </form>';
                                                } else {
                                                    // Trạng thái ngừng hoạt động, hiển thị nút "Đang hoạt động"
                                                    echo '<form method="POST">
                                                            <input type="hidden" name="user_id" value="' . $fetch_user['user_id'] . '">
                                                            <button type="submit" name="change_status">Kích hoạt</button>
                                                        </form>';
                                                }
                                            ?>
                                        </td>
 
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