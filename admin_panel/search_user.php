<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['admin_id'])) {
        $admin_id = $_COOKIE['admin_id'];
    } else {
        $admin_id = '';
        header('location:login.php');
    }

    //update order from database
    if (isset($_POST['update_order'])) {
        $hoadon_id = $_POST['hoadon_id'];
        $update_payment = $_POST['update_payment'];
        $update_pay = $conn->prepare("UPDATE `hoadon` SET tinhtrangthanhtoan=? WHERE hoadon_id=?");
        $update_pay->execute([$update_payment, $hoadon_id]);

        $new_status = $_POST['update_payment'];
        $update_status = $conn->prepare("UPDATE `hoadon` SET trangthai=? WHERE hoadon_id=?");
        if ($update_payment == 'Xác nhận'){
            $update_status->execute(['Đã xác nhận', $hoadon_id]);
        } elseif ($update_payment == 'Giao hàng'){
            $update_status->execute(['Đang giao hàng', $hoadon_id]);
        }
        
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
    <title>Secret Beauty - Search order page</title>
    <link rel="shortcut icon" href="../images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
</head>
<body>
        <div class="main-container">
            <?php include '../component/admin_header.php'; ?>
            <section class="user-container">
                <form action="search_user.php" method="post" class="search-form"> 
                        <input type="text" name="search_user" placeholder="Tìm kiếm tài khoản" required maxlength="100">
                        <button type="submit" class="fas fa-search" id="search_product_btn"></button>
                </form>
                <div class="heading" style="margin-top: 2rem;">
                    <h1>Kết quả tìm kiếm cho từ khoá "<?= $_POST['search_user']?>" </h1>
                </div>
                <div class="box-container">
                    <?php 
                        if (isset($_POST['search_user']) or isset($_POST['search_product_btn'])) {
                            $search_user = $_POST['search_user'];
                            $select_user = $conn->prepare("SELECT * FROM `user` WHERE (user_id LIKE '%{$search_user}%' 
                                or name LIKE '%{$search_user}%' or email LIKE '%{$search_user}%')");
                            $select_user->execute();

                            // echo '<p> ' . $search_products . '</p>';
                            if ($select_user->rowCount() > 0) {
                                while ($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)) {
                                    $user_id = $fetch_user['user_id'];
                    ?>
                    <div class="box" style="margin-bottom: 1rem;">
                            <p>ID: <span><?= $user_id; ?></span></p>
                            <p>Tên: <span style="text-transform: capitalize;"><?= $fetch_user['name']; ?></span></p>
                            <p>Email: <span><?= $fetch_user['email']; ?></span></p>
                        </div>
                        <?php
                            }
                        } else{
                            echo '
                                <div class="empty">
                                    <p>Không có người dùng!</p>
                                </div>
                            ';
                        }
                    }
                    ?>
                </div>

            </section>
        </div>

    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>