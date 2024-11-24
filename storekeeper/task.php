<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include '../component/connect.php';
    if (isset($_COOKIE['kho_id'])) {
        $user_id = $_COOKIE['kho_id'];
    } else {
        $user_id = '';
        header('location:../login.php');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $taskId = $_POST['task_id'];

        // Cập nhật trạng thái trong bảng task thành đã hoàn thành (1)
        $update_status = $conn->prepare("UPDATE `task` SET daxong = 1 WHERE task_id = ?");
        $update_status->execute([$taskId]);
        $success_msg[] = 'Đã đánh dấu là hoàn thành!';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Task page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page">
    <div class="main-container">
        <?php include '../component/stock_header.php'; ?>
        <section class="user-container">
            <!-- <div class="back">
                <a href="admin_order.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div> -->
            <div class="heading">
                <h1>Danh sách yêu cầu</h1>
            </div>
            <div class="box-container">
                <div class="box order">
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Ngày giờ</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Truy vấn lấy danh sách yêu cầu từ bảng task
                            $select_tasks = $conn->prepare("SELECT * FROM `task` ORDER BY ngaytao DESC");
                            $select_tasks->execute();

                            if ($select_tasks->rowCount() > 0) {
                                $stt = 1; // Khởi tạo số thứ tự
                                while ($fetch_task = $select_tasks->fetch(PDO::FETCH_ASSOC)){
                                    $row_class = $fetch_task['daxong'] == 0 ? 'pending' : 'completed';
                                    
                                    ?>
                                    <tr class="task-row <?= $row_class; ?>">
                                        <td><?= $stt++; ?></td> <!-- In số thứ tự -->
                                        <td><?= date('H:i d/m/Y', strtotime($fetch_task['ngaytao'])); ?></td> <!-- Ngày giờ tạo -->
                                        <td>
                                            Yêu cầu nhập kho cho sản phẩm<a href="add_stock.php?post_id=<?= $fetch_task['sanpham_id']; ?>">#<?= $fetch_task['sanpham_id']; ?></a>
                                        </td>
                                        <td class="status-btn">
                                            <?php if ($fetch_task['daxong'] == 0): ?>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="task_id" value="<?= $fetch_task['task_id']; ?>">
                                                    <button type="submit">Đánh dấu hoàn thành</button>
                                                </form>
                                            <?php else: ?>
                                                <span>Đã hoàn thành</span>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '
                                    <tr>
                                        <td colspan="4" style="text-align: center;">Không có yêu cầu nào!</td>
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